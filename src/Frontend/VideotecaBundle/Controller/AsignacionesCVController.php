<?php

namespace Frontend\VideotecaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\VideotecaBundle\Entity\AsignacionesCV;
use Frontend\VideotecaBundle\Form\AsignacionesCVType;

/**
 * AsignacionesCV controller.
 *
 */
class AsignacionesCVController extends Controller
{

    /**
     * Lists all AsignacionesCV entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VideotecaBundle:AsignacionesCV')->findAll();

        return $this->render('VideotecaBundle:AsignacionesCV:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AsignacionesCV entity.
     *
     */
    public function createAction(Request $request,$id)
    {   
        
        $entity = new AsignacionesCV();
        $form = $this->createCreateForm($id,$entity);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $cintaVG = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);
        $exit    = $cintaVG->getExistencia();
        
        
        if ($form->isValid()) {
            
            $cantidad           = $entity->getCantidad();
            $idusuario          = $this->get('security.context')->getToken()->getUser()->getId();
            $userprestamista    = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
            $entity->setIdUsuarioPrestamista($userprestamista);
            $fecha_entrega = date_create_from_format('Y-m-d G:i:s', \date("Y-m-d G:i:s"));
            $entity->setFechaEntrega($fecha_entrega);
            $entity->setIdCintasVirgenes($cintaVG);
            
                 if($exit >= $cantidad){
                   $exitactual = $exit - $cantidad;
                }else{
                    $this->get('session')->getFlashBag()->add('alert', 'LA CANTIDAD A ASIGNAR NO PUEDE SER SUPERIOR A LO EXISTENTE');
                    return $this->redirect( $this->generateUrl('asignacionescv_new',array('id'=>$id)));
                }
            
            $em->persist($entity);
            $em->flush();
            
           
                    
                $sql = $em->createQuery('update VideotecaBundle:CintasVirgenes x set x.existencia= :exitactual WHERE x.id = :idCV');
                $sql->setParameter('exitactual', $exitactual);
                $sql->setParameter('idCV', $entity->getIdCintasVirgenes());
                $sql->execute();     

            $this->get('session')->getFlashBag()->add('notice', 'LA ASIGNACIÓN SE HA REALIZADO EXITOSAMENTE');
            return $this->redirect($this->generateUrl('asignacionescv_show', array('id' => $entity->getId())));
        }
        
        return $this->render('VideotecaBundle:AsignacionesCV:new.html.twig', array(
            'entity'   => $entity,
            'form'     => $form->createView(),
            'id'       => $id,
            'cintaVG'  => $cintaVG,
        ));
    }

    /**
     * Creates a form to create a AsignacionesCV entity.
     *
     * @param AsignacionesCV $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm($id,AsignacionesCV $entity)
    {
        $form = $this->createForm(new AsignacionesCVType($id), $entity, array(
            'action' => $this->generateUrl('asignacionescv_create',array('id' => $id)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',
                array('label' => 'GUARDAR',
                      'attr' => array('class' => 'btn btn-default')
            ));

        return $form;
    }

    /**
     * Displays a form to create a new AsignacionesCV entity.
     *
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $cintaVG = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);
        $entity = new AsignacionesCV();
        $form   = $this->createCreateForm($id,$entity);

        return $this->render('VideotecaBundle:AsignacionesCV:new.html.twig', array(
            'entity'       => $entity,
            'form'         => $form->createView(),
            'cintaVG'      => $cintaVG,
            'id'           => $id
        ));
    }

    /**
     * Finds and displays a AsignacionesCV entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:AsignacionesCV')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AsignacionesCV entity.');
        }

        return $this->render('VideotecaBundle:AsignacionesCV:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing AsignacionesCV entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:AsignacionesCV')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AsignacionesCV entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('VideotecaBundle:AsignacionesCV:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AsignacionesCV entity.
    *
    * @param AsignacionesCV $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AsignacionesCV $entity)
    {
        $form = $this->createForm(new AsignacionesCVType(), $entity, array(
            'action' => $this->generateUrl('asignacionescv_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit',
                array('label' => 'GUARDAR',
                      'attr' => array('class' => 'btn btn-default')
            ));

        return $form;
    }
    /**
     * Edits an existing AsignacionesCV entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:AsignacionesCV')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AsignacionesCV entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

           return $this->redirect($this->generateUrl('asignacionescv_show', array('id' => $entity->getId())));
        }

        return $this->render('VideotecaBundle:AsignacionesCV:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a AsignacionesCV entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('VideotecaBundle:AsignacionesCV')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AsignacionesCV entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('asignacionescv'));
    }

    /**
     * Creates a form to delete a AsignacionesCV entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('asignacionescv_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
