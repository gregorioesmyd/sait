<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\OperacionesExternos;
use Frontend\ControlEquipoBundle\Entity\RegistrosExternos;
use Frontend\ControlEquipoBundle\Form\RegistrosExternosType;

use Symfony\Component\HttpFoundation\Response;

/**
 * RegistrosExternos controller.
 *
 */
class RegistrosExternosController extends Controller
{

    /**
     * Lists all RegistrosExternos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->findAll();

        return $this->render('ControlEquipoBundle:RegistrosExternos:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new RegistrosExternos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new RegistrosExternos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if($this->getRequest()->isXmlHttpRequest()) {
                $validador = $this->get('validator');
                $errores = $validador->validate($entity);
                if (count($errores) > 0) {
                        $view = $this->renderView('ControlEquipoBundle:EquiposExternos:erroresexterno.html.twig', array(
                            'form'   => $form->createView(),
                        ));
                        return new Response(json_encode(array(
                          'status' => 'error',
                          'info'   => $view
                        )));
                }
                $em = $this->getDoctrine()->getManager();
                $equipo = $em->getRepository('ControlEquipoBundle:EquiposExternos')->find($entity->getEquipo());
                $dql   = "SELECT r FROM ControlEquipoBundle:RegistrosExternos r where r.fechaSalida is null and r.equipo= :idequipo";
                $query = $em->createQuery($dql);
                $query->setParameter('idequipo',$equipo->getId());
                $equipoexterno = $query->getResult();
                if(!empty($equipoexterno)){
                    return new Response(json_encode(array(
                          'status' => 'error',
                          'info' => '<span class="alert alert-danger errores" style="width:70%;display: block;"><div><b>Alerta! Ha ocurrido un error::</b><br></div>No se ha registrado una salida de este Equipo, por favor verifique.</span>'
                        )));
                }
                $creado = date_create_from_format('Y-m-d', \date("Y-m-d"));
                $entity->setFechaEntrada($creado);
                $em->persist($entity);
                $em->flush();

                // Registramos la Operacion, quien genero la entrada del Equipo
                $idusuario      = $this->get('security.context')->getToken()->getUser()->getId();
                $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
                $operacion      = new OperacionesExternos();
                $registroexterno = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($entity->getId());
                // Seteamos los valores de la Operacion
                $operacion->setReponsableUsuario($responsableUsuario);
                $operacion->setTipoOperacion(1);
                $operacion->setRegistroEquipoExterno($registroexterno);
                $em->persist($operacion);
                $em->flush();

                return new Response(json_encode(array(
                          'status'  => 'ok',
                          'info'    => 'Se registró la entrada del Equipo exitosamente',
                )));
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('registroexterno_show', array('id' => $entity->getId())));
        }

        return $this->render('ControlEquipoBundle:RegistrosExternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a RegistrosExternos entity.
     *
     * @param RegistrosExternos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegistrosExternos $entity)
    {
        $form = $this->createForm(new RegistrosExternosType(), $entity, array(
            'action' => $this->generateUrl('registroexterno_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Registrar salida de Equipo Externo
     *
     */
    public function salidaAction($id,$propietario,$visita)
    {
        $em = $this->getDoctrine()->getManager();

        $fecha = date_create_from_format('Y-m-d', \date("Y-m-d"));

        $dql   = "SELECT re FROM ControlEquipoBundle:RegistrosExternos re WHERE re.fechaSalida is null and re.id= :id";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        $salida = $query->getResult();

        if(empty($salida)) {
            $this->get('session')->getFlashBag()->add('alert', 'ERROR AL REGISTRAR SALIDA DE EQUIPO #NOID.');
            if($visita == 0) {
                return $this->redirect($this->generateUrl('equipoexterno_datos_empleado', array('id' => $propietario,'tipo' => 1)));
            } else {
                return $this->redirect($this->generateUrl('visita_show', array('id' => $visita)));
            }
        }
        $query = $em->createQuery('UPDATE ControlEquipoBundle:RegistrosExternos r SET r.fechaSalida = :fs WHERE r.id = :id');
        $query->setParameter('fs', $fecha);
        $query->setParameter('id', $id);
        $query->execute();

        // Registramos la Operacion, quien genero la salida del Equipo
        $idusuario          = $this->get('security.context')->getToken()->getUser()->getId();
        $responsableUsuario = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $operacion          = new OperacionesExternos();
        $registroexterno    = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($id);
        // Seteamos los valores de la Operacion
        $operacion->setReponsableUsuario($responsableUsuario);
        $operacion->setTipoOperacion(2);
        $operacion->setRegistroEquipoExterno($registroexterno);
        $em->persist($operacion);
        $em->flush();

        $this->get('session')->getFlashBag()->add('notice', 'SALIDA DE EQUIPO <b>"'.$registroexterno->getEquipo()->getDescripcionEquipo().'"</b> REGISTRADA CON EXITO.');
        if($visita == 0) {
            return $this->redirect($this->generateUrl('equipoexterno_datos_empleado', array('id' => $propietario,'tipo' => 1)));
        } else {
            return $this->redirect($this->generateUrl('visita_show', array('id' => $visita)));
        }
    }

    /**
     * Displays a form to create a new RegistrosExternos entity.
     *
     */
    public function newAction()
    {
        $entity = new RegistrosExternos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:RegistrosExternos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RegistrosExternos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegistrosExternos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:RegistrosExternos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RegistrosExternos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegistrosExternos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:RegistrosExternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a RegistrosExternos entity.
    *
    * @param RegistrosExternos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(RegistrosExternos $entity)
    {
        $form = $this->createForm(new RegistrosExternosType(), $entity, array(
            'action' => $this->generateUrl('registroexterno_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing RegistrosExternos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegistrosExternos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('registroexterno_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:RegistrosExternos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a RegistrosExternos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find RegistrosExternos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('registroexterno'));
    }

    /**
     * Creates a form to delete a RegistrosExternos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('registroexterno_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
