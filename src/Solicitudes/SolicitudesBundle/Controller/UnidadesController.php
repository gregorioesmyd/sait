<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Solicitudes\SolicitudesBundle\Entity\Unidades;
use Solicitudes\SolicitudesBundle\Form\UnidadesType;

/**
 * Unidades controller.
 *
 */
class UnidadesController extends Controller
{

    /**
     * Lists all Unidades entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SolicitudesBundle:Unidades')->findAll();

        return $this->render('SolicitudesBundle:Unidades:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Unidades entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Unidades();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('unidades_show', array('id' => $entity->getId())));
        }

        return $this->render('SolicitudesBundle:Unidades:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Unidades entity.
     *
     * @param Unidades $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Unidades $entity)
    {
        $form = $this->createForm(new UnidadesType(), $entity, array(
            'action' => $this->generateUrl('unidades_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Unidades entity.
     *
     */
    public function newAction()
    {
        $entity = new Unidades();
        $form   = $this->createCreateForm($entity);

        return $this->render('SolicitudesBundle:Unidades:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Unidades entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Unidades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Unidades entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Unidades:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Unidades entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Unidades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Unidades entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SolicitudesBundle:Unidades:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Unidades entity.
    *
    * @param Unidades $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Unidades $entity)
    {
        $form = $this->createForm(new UnidadesType(), $entity, array(
            'action' => $this->generateUrl('unidades_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Unidades entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SolicitudesBundle:Unidades')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Unidades entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('unidades_edit', array('id' => $id)));
        }

        return $this->render('SolicitudesBundle:Unidades:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Unidades entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SolicitudesBundle:Unidades')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Unidades entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('unidades'));
    }

    /**
     * Creates a form to delete a Unidades entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('unidades_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
