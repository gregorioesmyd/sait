<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Solicitudes\SolicitudesBundle\Entity\Responsables;
use Solicitudes\SolicitudesBundle\Form\ResponsablesType;

/**
 * Responsables controller.
 *
 */
class ResponsablesController extends Controller
{

    /**
     * Lists all Responsables entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SolicitudesBundle:Responsables')->findAll();

        return $this->render('SolicitudesBundle:Responsables:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Responsables entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Responsables();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('responsables_show', array('id' => $entity->getId())));
        }

        return $this->render('SolicitudesBundle:Responsables:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Responsables entity.
     *
     * @param Responsables $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Responsables $entity)
    {
        $form = $this->createForm(new ResponsablesType(), $entity, array(
            'action' => $this->generateUrl('responsables_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Responsables entity.
     *
     */
    public function newAction()
    {
        $entity = new Responsables();
        $form   = $this->createCreateForm($entity);

        return $this->render('SolicitudesBundle:Responsables:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Responsables entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Responsables')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Responsables entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Responsables:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Responsables entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Responsables')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Responsables entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Responsables:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Responsables entity.
    *
    * @param Responsables $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Responsables $entity)
    {
        $form = $this->createForm(new ResponsablesType(), $entity, array(
            'action' => $this->generateUrl('responsables_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Responsables entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Responsables')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Responsables entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('responsables_edit', array('id' => $id)));
        }

        return $this->render('SolicitudesBundle:Responsables:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Responsables entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SolicitudesBundle:Responsables')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Responsables entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('responsables'));
    }

    /**
     * Creates a form to delete a Responsables entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('responsables_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
