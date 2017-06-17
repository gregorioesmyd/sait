<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ControlEquipoBundle\Entity\Marcas;
use Frontend\ControlEquipoBundle\Form\MarcasType;

/**
 * Marcas controller.
 *
 */
class MarcasController extends Controller
{

    /**
     * Lists all Marcas entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ControlEquipoBundle:Marcas')->findAll();

        return $this->render('ControlEquipoBundle:Marcas:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Marcas entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Marcas();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('marca_show', array('id' => $entity->getId())));
        }

        return $this->render('ControlEquipoBundle:Marcas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Marcas entity.
     *
     * @param Marcas $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Marcas $entity)
    {
        $form = $this->createForm(new MarcasType(), $entity, array(
            'action' => $this->generateUrl('marca_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Registrar'));

        return $form;
    }

    /**
     * Displays a form to create a new Marcas entity.
     *
     */
    public function newAction()
    {
        $entity = new Marcas();
        $form   = $this->createCreateForm($entity);

        return $this->render('ControlEquipoBundle:Marcas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Marcas entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Marcas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Marcas entity.');
        }
        $modelos  = $em->getRepository('ControlEquipoBundle:Modelos')->findByMarca($id);

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:Marcas:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'modelos'     => $modelos
        ));
    }

    /**
     * Displays a form to edit an existing Marcas entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Marcas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Marcas entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ControlEquipoBundle:Marcas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Marcas entity.
    *
    * @param Marcas $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Marcas $entity)
    {
        $form = $this->createForm(new MarcasType(), $entity, array(
            'action' => $this->generateUrl('marca_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modificar'));

        return $form;
    }
    /**
     * Edits an existing Marcas entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ControlEquipoBundle:Marcas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Marcas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('marca_edit', array('id' => $id)));
        }

        return $this->render('ControlEquipoBundle:Marcas:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Marcas entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ControlEquipoBundle:Marcas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Marcas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('marca'));
    }

    /**
     * Creates a form to delete a Marcas entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('marca_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
