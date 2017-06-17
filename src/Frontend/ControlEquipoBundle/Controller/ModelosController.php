<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\Modelos;
use Frontend\ControlEquipoBundle\Form\ModelosType;

/**
 * Modelos controller.
 *
 */
class ModelosController extends Controller
{

    /**
     * Lists all Modelos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:Modelos')->findAll();

        return $this->render('ControlEquipoBundle:Modelos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Modelos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Modelos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('modelo_show', array('id' => $entity->getId())));
        }

        return $this->render('ControlEquipoBundle:Modelos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Modelos entity.
     *
     * @param Modelos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Modelos $entity)
    {
        $form = $this->createForm(new ModelosType(), $entity, array(
            'action' => $this->generateUrl('modelo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Modelos entity.
     *
     */
    public function newAction()
    {
        $entity = new Modelos();
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:Modelos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Modelos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Modelos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:Modelos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Modelos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Modelos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:Modelos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Modelos entity.
    *
    * @param Modelos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Modelos $entity)
    {
        $form = $this->createForm(new ModelosType(), $entity, array(
            'action' => $this->generateUrl('modelo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Modelos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Modelos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Modelos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('modelo_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:Modelos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Modelos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:Modelos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Modelos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('modelo'));
    }

    /**
     * Creates a form to delete a Modelos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modelo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
