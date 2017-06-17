<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Solicitudes\SolicitudesBundle\Entity\Roles;
use Solicitudes\SolicitudesBundle\Form\RolesType;

/**
 * Roles controller.
 *
 */
class RolesController extends Controller
{

    /**
     * Lists all Roles entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SolicitudesBundle:Roles')->findAll();

        return $this->render('SolicitudesBundle:Roles:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Roles entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Roles();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('roles_show', array('id' => $entity->getId())));
        }

        return $this->render('SolicitudesBundle:Roles:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Roles entity.
     *
     * @param Roles $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Roles $entity)
    {
        $form = $this->createForm(new RolesType(), $entity, array(
            'action' => $this->generateUrl('roles_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Roles entity.
     *
     */
    public function newAction()
    {
        $entity = new Roles();
        $form   = $this->createCreateForm($entity);

        return $this->render('SolicitudesBundle:Roles:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Roles entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Roles')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Roles entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Roles:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Roles entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Roles')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Roles entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Roles:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Roles entity.
    *
    * @param Roles $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Roles $entity)
    {
        $form = $this->createForm(new RolesType(), $entity, array(
            'action' => $this->generateUrl('roles_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Roles entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Roles')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Roles entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('roles_edit', array('id' => $id)));
        }

        return $this->render('SolicitudesBundle:Roles:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Roles entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SolicitudesBundle:Roles')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Roles entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('roles'));
    }

    /**
     * Creates a form to delete a Roles entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('roles_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
