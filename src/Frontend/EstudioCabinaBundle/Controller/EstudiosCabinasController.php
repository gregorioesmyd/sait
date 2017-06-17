<?php

namespace Frontend\EstudioCabinaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EstudioCabinaBundle\Entity\EstudiosCabinas;
use Frontend\EstudioCabinaBundle\Form\EstudiosCabinasType;

/**
 * EstudiosCabinas controller.
 *
 */
class EstudiosCabinasController extends Controller
{

    /**
     * Lists all EstudiosCabinas entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->findAll();

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EstudiosCabinas entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EstudiosCabinas();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('estudioscabinas_show', array('id' => $entity->getId())));
        }

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EstudiosCabinas entity.
     *
     * @param EstudiosCabinas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EstudiosCabinas $entity)
    {
        $form = $this->createForm(new EstudiosCabinasType(), $entity, array(
            'action' => $this->generateUrl('estudioscabinas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EstudiosCabinas entity.
     *
     */
    public function newAction()
    {
        $entity = new EstudiosCabinas();
        $form   = $this->createCreateForm($entity);

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EstudiosCabinas entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstudiosCabinas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EstudiosCabinas entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstudiosCabinas entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EstudiosCabinas entity.
    *
    * @param EstudiosCabinas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EstudiosCabinas $entity)
    {
        $form = $this->createForm(new EstudiosCabinasType(), $entity, array(
            'action' => $this->generateUrl('estudioscabinas_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EstudiosCabinas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EstudiosCabinas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('estudioscabinas_edit', array('id' => $id)));
        }

        return $this->render('EstudioCabinaBundle:EstudiosCabinas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EstudiosCabinas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EstudiosCabinas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('estudioscabinas'));
    }

    /**
     * Creates a form to delete a EstudiosCabinas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estudioscabinas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
