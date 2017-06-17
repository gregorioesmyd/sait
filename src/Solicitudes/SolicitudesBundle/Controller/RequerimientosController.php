<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Solicitudes\SolicitudesBundle\Entity\Requerimientos;
use Solicitudes\SolicitudesBundle\Form\RequerimientosType;

/**
 * Requerimientos controller.
 *
 */
class RequerimientosController extends Controller
{

    /**
     * Lists all Requerimientos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SolicitudesBundle:Requerimientos')->findAll();

        return $this->render('SolicitudesBundle:Requerimientos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Requerimientos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Requerimientos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('requerimientos_show', array('id' => $entity->getId())));
        }

        return $this->render('SolicitudesBundle:Requerimientos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Requerimientos entity.
     *
     * @param Requerimientos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Requerimientos $entity)
    {
        $form = $this->createForm(new RequerimientosType(), $entity, array(
            'action' => $this->generateUrl('requerimientos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Requerimientos entity.
     *
     */
    public function newAction()
    {
        $entity = new Requerimientos();
        $form   = $this->createCreateForm($entity);

        return $this->render('SolicitudesBundle:Requerimientos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Requerimientos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Requerimientos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Requerimientos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Requerimientos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Requerimientos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Requerimientos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Requerimientos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Requerimientos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Requerimientos entity.
    *
    * @param Requerimientos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Requerimientos $entity)
    {
        $form = $this->createForm(new RequerimientosType(), $entity, array(
            'action' => $this->generateUrl('requerimientos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Requerimientos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Requerimientos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Requerimientos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('requerimientos_edit', array('id' => $id)));
        }

        return $this->render('SolicitudesBundle:Requerimientos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Requerimientos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SolicitudesBundle:Requerimientos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Requerimientos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('requerimientos'));
    }

    /**
     * Creates a form to delete a Requerimientos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('requerimientos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
