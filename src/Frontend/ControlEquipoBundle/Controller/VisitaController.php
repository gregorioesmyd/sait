<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\Visita;
use Frontend\ControlEquipoBundle\Form\VisitaType;

use Frontend\ControlEquipoBundle\Entity\Usuario;
use Frontend\ControlEquipoBundle\Form\UsuarioType;

/**
 * Visita controller.
 *
 */
class VisitaController extends Controller
{

    /**
     * Lists all Visita entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql   = "SELECT v FROM ControlEquipoBundle:Visita v join v.usuario u order by v.fechaentrada DESC, v.horaentrada DESC";
        $query = $em->createQuery($dql);
        //$query->setMaxResults(250);
        $entities = $query->getResult();

        return $this->render('ControlEquipoBundle:Visita:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Visita entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //busco si existe el usuario
        $datos=$request->get('frontend_controlequipobundle_usuario');
        $cedula=$datos['cedula'];
        $usuario = $em->getRepository('ControlEquipoBundle:Usuario')->findByCedula($cedula);



        $entityv = new Visita();
        $formv = $this->createCreateFormV($entityv);

        $entityu = new Usuario();
        $formu = $this->createCreateFormU($entityu);

        $formv->handleRequest($request);

        if(empty($usuario)){
            $formu->handleRequest($request);
        }


        if ($formv->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $fa=new \DateTime(\date("d-m-Y"));
            $ha=new \DateTime(\date("G:i:s"));
            $entityv->setFechaentrada($fa);
            $entityv->setHoraentrada($ha);
            $entityv->setUsuario($entityu);
            $em->persist($entityv);

            if (empty($usuario)) $em->persist($entityu);
            else {$us = $em->getRepository('ControlEquipoBundle:Usuario')->find($usuario[0]->getId()); $entityv->setUsuario($us);}

            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Visita registrada con exito.');
            return $this->redirect($this->generateUrl('visita_show', array('id' => $entityv->getId())));
        } else $this->get('session')->getFlashBag()->add('alert', 'Ha ocurrido un error en el formulario.');



        return $this->render('ControlEquipoBundle:Visita:new.html.twig', array(
            'entity' => $entityv,
            'formv'   => $formv->createView(),
            'formu'   => $formu->createView(),
            'usuario'=>$usuario
        ));
    }


    /**
     * Creates a form to create a Visita entity.
     *
     * @param Visita $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFormV(Visita $entity)
    {
        $form = $this->createForm(new VisitaType(), $entity, array(
            'action' => $this->generateUrl('visita_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    private function createCreateFormU(Usuario $entity)
    {
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('visita_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    public function buscaAction()
    {

        return $this->render('ControlEquipoBundle:Visita:busca.html.twig');
    }

    /**
     * Displays a form to create a new Visita entity.
     *
     */
    public function newAction(Request $request)
    {   $em = $this->getDoctrine()->getManager();
        $entity = new Visita();
        $formv   = $this->createCreateFormV($entity);

        $entity = new Usuario();
        $formu   = $this->createCreateFormU($entity);


        $cedula=$datos=$request->get('cedula');
        if(empty($cedula)){
            $this->get('session')->getFlashBag()->add('alert', 'La cédula no debe estar en blanco.');
            return $this->redirect($this->generateUrl('visita_busca'));
        }

        $usuario = $em->getRepository('ControlEquipoBundle:Usuario')->findByCedula($cedula);

        $foto=0;
        if(!empty($usuario)){
            $dql   = "SELECT v FROM ControlEquipoBundle:Visita v where v.fechasalida is null and v.usuario= :idus";
            $query = $em->createQuery($dql);
            $query->setParameter('idus',$usuario[0]->getId());
            $visita = $query->getResult();
            if(!empty($visita)){
                $this->get('session')->getFlashBag()->add('alert', 'No se ha registrado una salida de este usuario, por favor verifique.');
                return $this->redirect($this->generateUrl('visita_show',array('id'=>$visita[0]->getId())));
            }

            $nombre=$usuario[0]->getCedula();
            if (file_exists('/var/www/uploads/visitas/'.$nombre)):
                $foto=1;
            endif;
        }

        return $this->render('ControlEquipoBundle:Visita:new.html.twig', array(
            'entity' => $entity,
            'formv'   => $formv->createView(),
            'formu'   => $formu->createView(),
            'usuario' => $usuario,
            'foto' => $foto
        ));
    }

    public function salidaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaactual=new \DateTime(\date("d-m-Y"));
        $horaactual=new \DateTime(\date("G:i:s"));

        $query = $em->createQuery('update ControlEquipoBundle:Visita v set v.fechasalida= :fs, v.horasalida= :hs WHERE v.id = :id');
        $query->setParameter('fs', $fechaactual);
        $query->setParameter('hs', $horaactual);
        $query->setParameter('id', $id);
        $query->execute();

        $this->get('session')->getFlashBag()->add('notice', 'Salida registrada con exito.');
        return $this->redirect($this->generateUrl('visita_show', array('id' => $id)));
    }
    /**
     * Finds and displays a Visita entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Visita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Visita entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $foto=0;

        $nombre = $entity->getUsuario()->getCedula().'.png';

        $nombre=$entity->getUsuario()->getCedula();

        if (file_exists('/var/www/uploads/visitas/'.$nombre)):
            $foto=1;
        endif;

        // Consultamos sus equipos
        $equipos  = $em->getRepository('ControlEquipoBundle:RegistrosExternos')->findBy(array('propietarioId' => $entity->getUsuario()->getId(),
            'tipo' => 2,'fechaSalida' => NULL));
        $ye = $em->getRepository('ControlEquipoBundle:EquiposExternos')->findBy(array('propietarioId' => $entity->getUsuario()->getId(),
                'tipoPropietario' => 2));

        return $this->render('ControlEquipoBundle:Visita:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'foto'        => $foto,
            'equipos'     => $equipos,
            'yourequipos' => $ye
        ));
    }

    /**
     * Displays a form to edit an existing Visita entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Visita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Visita entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:Visita:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Visita entity.
    *
    * @param Visita $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Visita $entity)
    {
        $form = $this->createForm(new VisitaType(), $entity, array(
            'action' => $this->generateUrl('visita_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Visita entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Visita')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Visita entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('visita_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:Visita:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Visita entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:Visita')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Visita entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('visita'));
    }

    /**
     * Creates a form to delete a Visita entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('visita_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
}
