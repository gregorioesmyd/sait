<?php

namespace Frontend\TruequesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Collections\ArrayCollection;
use Frontend\TruequesBundle\Entity\Categoria;
use Frontend\TruequesBundle\Form\CategoriaType;

/**
 * Categoria controller.
 *
 */
class CategoriaController extends Controller
{

    /**
     * Lists all Categoria entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TruequesBundle:Categoria')->findAll();

        return $this->render('TruequesBundle:Categoria:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Categoria entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Categoria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($entity->getProductos() as $key => $producto) {
                $producto->setCategoria($entity);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_categoria_show', array('id' => $entity->getId())));
        }

        return $this->render('TruequesBundle:Categoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Categoria entity.
     *
     * @param Categoria $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('admin_categoria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Categoria entity.
     *
     */
    public function newAction()
    {
        $entity = new Categoria();
        $form   = $this->createCreateForm($entity);

        return $this->render('TruequesBundle:Categoria:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Categoria entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TruequesBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('TruequesBundle:Categoria:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Categoria entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TruequesBundle:Categoria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Categoria entity.');
        }

        $originalProduct = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getProductos() as $product) {
            $originalProduct->add($product);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // foreach ($originalProduct as $product) {
            //     echo $product->getNombre() . "<br>";
            // }

            // echo "--------------------------<br>";

            // foreach ($originalProduct as $product) {
            //     echo $entity->getProductos()->contains($product) . " - " . $product->getNombre() . " <br>";
            // }

            // echo "<br>--------------------------<br>";
            // remove the relationship between the tag and the Task
            foreach ($originalProduct as $product) {
                if (false === $entity->getProductos()->contains($product)) {
                    echo $product->getNombre() . "<br>";
                    // remove the Task from the Tag
                    // $product->getCategoria()->removeElement($entity);

                    $entity->removeProducto($product);

                    // if it was a many-to-one relationship, remove the relationship like this
                    // $tag->setTask(null);

                    // $em->persist($entity);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($product);
                }
            }

            // echo "--------------------------<br>";

            // foreach ($entity->getProductos() as $product) {
            //     echo $product->getNombre() . "<br>";
            // }

            foreach ($entity->getProductos() as $key => $producto) {
                $producto->setCategoria($entity);
            }

            // echo "--------------------------<br>";

            $em->flush();

            return $this->redirect($this->generateUrl('admin_categoria_edit', array('id' => $id)));
        }

        return $this->render('TruequesBundle:Categoria:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a Categoria entity.
    *
    * @param Categoria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Categoria $entity)
    {
        $form = $this->createForm(new CategoriaType(), $entity, array(
            'action' => $this->generateUrl('admin_categoria_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Deletes a Categoria entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TruequesBundle:Categoria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Categoria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_categoria'));
    }

    /**
     * Creates a form to delete a Categoria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_categoria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
