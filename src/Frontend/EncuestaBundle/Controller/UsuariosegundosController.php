<?php

namespace Frontend\EncuestaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\EncuestaBundle\Entity\Usuariosegundos;
use Frontend\EncuestaBundle\Form\UsuariosegundosType;

/**
 * Usuariosegundos controller.
 *
 */
class UsuariosegundosController extends Controller
{

    /**
     * Lists all Usuariosegundos entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('EncuestaBundle:Usuariosegundos')->findAll();

        return $this->render('EncuestaBundle:Usuariosegundos:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Usuariosegundos entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Usuariosegundos();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('Usuariosegundos_show', array('id' => $entity->getId())));
        }

        return $this->render('EncuestaBundle:Usuariosegundos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Usuariosegundos entity.
     *
     * @param Usuariosegundos $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Usuariosegundos $entity)
    {
        $form = $this->createForm(new UsuariosegundosType(), $entity, array(
            'action' => $this->generateUrl('Usuariosegundos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Usuariosegundos entity.
     *
     */
    public function newAction()
    {
        $entity = new Usuariosegundos();
        $form   = $this->createCreateForm($entity);

        return $this->render('EncuestaBundle:Usuariosegundos:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Usuariosegundos entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Usuariosegundos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariosegundos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Usuariosegundos:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Usuariosegundos entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Usuariosegundos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariosegundos entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('EncuestaBundle:Usuariosegundos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Usuariosegundos entity.
    *
    * @param Usuariosegundos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuariosegundos $entity)
    {
        $form = $this->createForm(new UsuariosegundosType(), $entity, array(
            'action' => $this->generateUrl('Usuariosegundos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Usuariosegundos entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('EncuestaBundle:Usuariosegundos')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuariosegundos entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('Usuariosegundos_edit', array('id' => $id)));
        }

        return $this->render('EncuestaBundle:Usuariosegundos:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Usuariosegundos entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('EncuestaBundle:Usuariosegundos')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuariosegundos entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('Usuariosegundos'));
    }

    /**
     * Creates a form to delete a Usuariosegundos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('Usuariosegundos_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
