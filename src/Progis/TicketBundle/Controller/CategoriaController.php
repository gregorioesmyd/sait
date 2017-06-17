<?php

namespace Progis\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\TicketBundle\Entity\Categoria;
use Progis\TicketBundle\Form\CategoriaType;

/**
 * Categoria controller.
 *
 */


class CategoriaController extends Controller
{
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolDefiniciones($session,$admin);
    }
    /**
     * Lists all Categoria entities.
     *
     */
    public function indexAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();

        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $dql = "select c from TicketBundle:Categoria c where c.nivelorganizacional= :id";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$perfil->getNivelorganizacional()->getId());
        $entities = $query->getResult();

        //$entities = $em->getRepository('TicketBundle:Categoria')->findAll();
        return $this->render('TicketBundle:Categoria:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Categoria entity.
     *
     */
    public function createAction(Request $request)
    {
        $this->seguridad();

        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity  = new Categoria();
        $form   = $this->createCreateForm($entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->setNivelorganizacional($perfil->getNivelorganizacional());
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'La categoria fue creada con exito.');
            return $this->redirect($this->generateUrl('progis_categoria'));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Ha ocurrido un error en el formulario.');
        return $this->render('TicketBundle:Categoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Categoria entity.
     *
     */
    public function newAction()
    {
        $this->seguridad();

        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = new Categoria();
        $form   = $this->createCreateForm($entity);

        return $this->render('TicketBundle:Categoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    private function createCreateForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('progis_categoria_create'),
            'method' => 'POST',
        ));

        return $form;
    }
    
    private function createEditForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('progis_categoria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('progis_categoria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'BORRAR'))
            ->getForm()
        ;
    }
    
    
    public function editAction($id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = $em->getRepository('TicketBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TicketBundle:Categoria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Categoria entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $this->seguridad();
        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = $em->getRepository('TicketBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoriaType($perfil->getNivelorganizacional()->getId()), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'La categoria se actualizó con exito.');
            return $this->redirect($this->generateUrl('progis_categoria_edit', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Ha ocurrido un error en el formulario.');
        return $this->render('TicketBundle:Categoria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Categoria entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TicketBundle:Categoria')->find($id);

        $dql = "select s from TicketBundle:Subcategoria s join s.categoria c where c.id= :id";
        $query = $em->createQuery($dql);
        $query->setParameter('id',$id);
        $entities = $query->getResult();

        if(!empty($entities)){
            $this->get('session')->getFlashBag()->add('alert', 'No se peude eliminar la categoria '.$entity->getCategoria().' porque tiene subcategorias aociadas.');
            return $this->redirect($this->generateUrl('progis_categoria'));
        }


        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categoria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        $this->get('session')->getFlashBag()->add('notice', 'Categoria eliminada con exito.');
        return $this->redirect($this->generateUrl('progis_categoria'));
    }


}
