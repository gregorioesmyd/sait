<?php

namespace Progis\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\TicketBundle\Entity\Subcategoria;
use Progis\TicketBundle\Form\SubcategoriaType;

use Progis\TicketBundle\Entity\Categoria;
/**
 * Subcategoria controller.
 *
 */
class SubcategoriaController extends Controller
{
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }

    /**
     * Lists all Subcategoria entities.
     *
     */
    public function indexAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $categoria=$em->getRepository('TicketBundle:Categoria')->find($id);

        $dql = "select s from TicketBundle:Subcategoria s join s.categoria c where c.id= :id";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        $entities = $query->getResult();
        if(empty($entities))$entities=null;

        return $this->render('TicketBundle:Subcategoria:index.html.twig', array(
            'entities' => $entities,
            'categoria'=>$categoria
        ));
    }
    /**
     * Creates a new Subcategoria entity.
     *
     */
    public function createAction(Request $request, $id)
    {
        $this->seguridad();

        $em = $this->getDoctrine()->getManager();
        $categoria=$em->getRepository('TicketBundle:Categoria')->find($id);

        $entity  = new Subcategoria();
        $form   = $this->createCreateForm($entity,$categoria);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCategoria($categoria);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'La subcategoria se creó con exito.');
            return $this->redirect($this->generateUrl('progis_subcategoria', array('id' => $entity->getCategoria()->getId())));
        }

        return $this->render('TicketBundle:Subcategoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'categoria'=>$categoria,

        ));
    }

    /**
     * Displays a form to create a new Subcategoria entity.
     *
     */
    public function newAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $categoria=$em->getRepository('TicketBundle:Categoria')->find($id);

        $entity = new Subcategoria();
        $form   = $this->createCreateForm($entity,$categoria);

        return $this->render('TicketBundle:Subcategoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'categoria'=>$categoria
        ));
    }
  private function createCreateForm(Subcategoria $entity,$categoria)
    {
        $form = $this->createForm(new SubcategoriaType(), $entity, array(
            'action' => $this->generateUrl('progis_subcategoria_create', array('id' => $categoria->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    
    private function createEditForm(Subcategoria $entity)
    {
        $form = $this->createForm(new SubcategoriaType(), $entity, array(
            'action' => $this->generateUrl('progis_subcategoria_update', array('idsub' => $entity->getId(),'idcat'=>$entity->getCategoria()->getCategoria())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('progis_subcategoria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }

    /**
     * Finds and displays a Subcategoria entity.
     *
     */
    public function showAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Subcategoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subcategoria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TicketBundle:Subcategoria:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Subcategoria entity.
     *
     */
    public function editAction($idsub, $idcat)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Subcategoria')->find($idsub);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subcategoria entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($idsub);

        return $this->render('TicketBundle:Subcategoria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Subcategoria entity.
     *
     */
    public function updateAction(Request $request, $idsub,$idcat)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TicketBundle:Subcategoria')->find($idsub);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Subcategoria entity.');
        }

        $deleteForm = $this->createDeleteForm($idsub);

        $editForm = $this->createEditForm($entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'La subcategoria se actualizó con exito.');
            return $this->redirect($this->generateUrl('progis_subcategoria_edit', array('idsub' => $idsub,'idcat'=>$idcat)));
        }

        return $this->render('TicketBundle:Subcategoria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Subcategoria entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TicketBundle:Subcategoria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Subcategoria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('notice', 'La subcategoria '.$entity->getSubcategoria().' se eliminó con exito.');
        return $this->redirect($this->generateUrl('progis_subcategoria',array('id' => $entity->getCategoria()->getId())));
    }


}
