<?php

namespace Frontend\CumpleaniosBundle\Controller\Efemerides;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\CumpleaniosBundle\Entity\Efemerides\Efemerides;
use Frontend\CumpleaniosBundle\Form\Efemerides\EfemeridesType;

/**
 * Efemerides\Efemerides controller.
 *
 */
class EfemeridesController extends Controller {

    /**
     * Lists all Efemerides\Efemerides entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CumpleaniosBundle:Efemerides\Efemerides')->findAll();

        $result = $this->renderData($entities);

        return $this->render('CumpleaniosBundle:Efemerides:index.html.twig', array(
                    'entities' => $result,
        ));
    }

    private function renderData(array $entities) {
        $meses = array(
            "01" => "Enero",
            "02" => "Febrero",
            "03" => "Marzo",
            "04" => "Abril",
            "05" => "Mayo",
            "06" => "Junio",
            "07" => "Julio",
            "08" => "Agosto",
            "09" => "Septiembre",
            "10" => "Octubre",
            "11" => "Noviembre",
            "12" => "Diciembre"
        );
        $result = array();
        foreach ($entities as $obj) {
            $obj->setMes($meses[$obj->getMes()]);
            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Creates a new Efemerides\Efemerides entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new Efemerides();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $anio = $entity->getAnio() == '' ? date("Y") : $entity->getAnio();
        $isvalidDate = checkdate($entity->getMes(), $entity->getDia(), $anio);
        if (!$isvalidDate) {
            $this->get('session')->getFlashBag()->add('alert', 'La fecha introducida no es valida.');
        } else {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('efemerides_show', array('id' => $entity->getId())));
            }
        }


        return $this->render('CumpleaniosBundle:Efemerides:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Efemerides\Efemerides entity.
     *
     * @param Efemerides $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Efemerides $entity) {
        $form = $this->createForm(new EfemeridesType(), $entity, array(
            'action' => $this->generateUrl('efemerides_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Efemerides\Efemerides entity.
     *
     */
    public function newAction() {
        $entity = new Efemerides();
        $form = $this->createCreateForm($entity);

        return $this->render('CumpleaniosBundle:Efemerides:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Efemerides\Efemerides entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CumpleaniosBundle:Efemerides\Efemerides')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Efemerides\Efemerides entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $result = $this->renderData(array($entity));

        return $this->render('CumpleaniosBundle:Efemerides:show.html.twig', array(
                    'entity' => $result[0],
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Efemerides\Efemerides entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CumpleaniosBundle:Efemerides\Efemerides')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Efemerides\Efemerides entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CumpleaniosBundle:Efemerides:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Efemerides\Efemerides entity.
     *
     * @param Efemerides $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Efemerides $entity) {
        $form = $this->createForm(new EfemeridesType(), $entity, array(
            'action' => $this->generateUrl('efemerides_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Efemerides\Efemerides entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CumpleaniosBundle:Efemerides\Efemerides')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Efemerides\Efemerides entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
             $this->get('session')->getFlashBag()->add('notice', 'Cambios realizado con exito');
            return $this->redirect($this->generateUrl('efemerides_show', array('id' => $id)));
        }

        return $this->render('CumpleaniosBundle:Efemerides:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Efemerides\Efemerides entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CumpleaniosBundle:Efemerides\Efemerides')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Efemerides\Efemerides entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('efemerides'));
    }

    /**
     * Creates a form to delete a Efemerides\Efemerides entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('efemerides_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'ELIMINAR'))
                        ->getForm()
        ;
    }

}
