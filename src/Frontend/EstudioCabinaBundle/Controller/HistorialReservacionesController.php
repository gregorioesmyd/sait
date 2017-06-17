<?php

namespace Frontend\EstudioCabinaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EstudioCabinaBundle\Entity\HistorialReservaciones;
use Frontend\EstudioCabinaBundle\Form\HistorialReservacionesType;

/**
 * HistorialReservaciones controller.
 *
 */
class HistorialReservacionesController extends Controller
{

    /**
     * Lists all HistorialReservaciones entities.
     *
     */
    public function indexAction($id,$tipo,$ubicacion=0)
    { 
        $em = $this->getDoctrine()->getManager();
        $entity         = $em->getRepository('EstudioCabinaBundle:Reservaciones')->find($id);
        $idu            = $entity->getPerfilId();
        $actual         = strtotime(date_format(new \DateTime("now"), 'd-m-Y')) ;
        $fechabd        = strtotime(date_format($entity->getFecha(), 'd-m-Y'));
        if ($actual > $fechabd and $entity->getEstatus()!= 1 and $entity->getEstatus()!= 3 ){
                    $entity->setEstatus(4);
            }
        if ($actual > $fechabd and $entity->getEstatus()== 1 ){
                $entity->setEstatus(5);
        }
            
        //consulto los datos del usuario que creo la pauta 
            $dql = "select p from UsuarioBundle:Perfil p where p.id=$idu";
            $q = $em->createQuery($dql);
            $usu = $q->getResult();
        //Consulto el Historial de movimiento de esta solicitud
         $id_reser= $entity->getId();
         $query = $em->createQuery('select x from EstudioCabinaBundle:HistorialReservaciones x WHERE x.reservaciones = :id')->setParameter('id', $id_reser);
         $historial = $query->getResult();
            if($historial){
                    foreach ($historial as $datos){
                        
                       $idusuario = $datos->getPerfil();

                    //consulto los datos del usuario que creo ó aprobo-rechazo la solicitud
                        $dql    = "select p from UsuarioBundle:Perfil p  where p.id=$idusuario";
                        $query  = $em->createQuery($dql);
                        $perfil = $query->getResult();
                        $usuario[$datos->getId()]['nombres_apellidos'] = $perfil[0]->getPrimerNombre() .' '.$perfil[0]->getPrimerApellido();
                    //fin 
            }
          } else {
              $usuario = array();
          }
          
         return $this->render('EstudioCabinaBundle:HistorialReservaciones:index.html.twig', array(
            'entity'            => $entity,
            'usuario'           => $usu,
            'historial'         => $historial,
            'tipo'              => $tipo,
            'ubicacion'         => $ubicacion,
            'perfil'            => $usuario
        ));
    }
    /**
     * Creates a new HistorialReservaciones entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new HistorialReservaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('historialreservaciones_show', array('id' => $entity->getId())));
        }

        return $this->render('EstudioCabinaBundle:HistorialReservaciones:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a HistorialReservaciones entity.
     *
     * @param HistorialReservaciones $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HistorialReservaciones $entity)
    {
        $form = $this->createForm(new HistorialReservacionesType(), $entity, array(
            'action' => $this->generateUrl('historialreservaciones_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HistorialReservaciones entity.
     *
     */
    public function newAction()
    {
        $entity = new HistorialReservaciones();
        $form   = $this->createCreateForm($entity);

        return $this->render('EstudioCabinaBundle:HistorialReservaciones:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a HistorialReservaciones entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:HistorialReservaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HistorialReservaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:HistorialReservaciones:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing HistorialReservaciones entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:HistorialReservaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HistorialReservaciones entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:HistorialReservaciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a HistorialReservaciones entity.
    *
    * @param HistorialReservaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HistorialReservaciones $entity)
    {
        $form = $this->createForm(new HistorialReservacionesType(), $entity, array(
            'action' => $this->generateUrl('historialreservaciones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing HistorialReservaciones entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:HistorialReservaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HistorialReservaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('historialreservaciones_edit', array('id' => $id)));
        }

        return $this->render('EstudioCabinaBundle:HistorialReservaciones:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a HistorialReservaciones entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EstudioCabinaBundle:HistorialReservaciones')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HistorialReservaciones entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('historialreservaciones'));
    }

    /**
     * Creates a form to delete a HistorialReservaciones entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('historialreservaciones_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
