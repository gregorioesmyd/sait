<?php

namespace Frontend\VideotecaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\VideotecaBundle\Entity\CintasVirgenes;
use Frontend\VideotecaBundle\Form\CintasVirgenesType;

/**
 * CintasVirgenes controller.
 *
 */
class CintasVirgenesController extends Controller
{

    /**
     * Lists all CintasVirgenes entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VideotecaBundle:CintasVirgenes')->findAll();

        return $this->render('VideotecaBundle:CintasVirgenes:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new CintasVirgenes entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new CintasVirgenes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'SE HA AGREGADO LA CINTA EXITOSAMENTE');
            return $this->redirect($this->generateUrl('cintasvirgenes_show', array('id' => $entity->getId())));
        }

        return $this->render('VideotecaBundle:CintasVirgenes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CintasVirgenes entity.
     *
     * @param CintasVirgenes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CintasVirgenes $entity)
    {
        $form = $this->createForm(new CintasVirgenesType(), $entity, array(
            'action' => $this->generateUrl('cintasvirgenes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit',
                array('label' => 'GUARDAR',
                      'attr' => array('class' => 'btn btn-default')
            ));

        return $form;
    }

    /**
     * Displays a form to create a new CintasVirgenes entity.
     *
     */
    public function newAction()
    {
        $entity = new CintasVirgenes();
        $form   = $this->createCreateForm($entity);

        return $this->render('VideotecaBundle:CintasVirgenes:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CintasVirgenes entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
        }

        return $this->render('VideotecaBundle:CintasVirgenes:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing CintasVirgenes entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('VideotecaBundle:CintasVirgenes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CintasVirgenes entity.
    *
    * @param CintasVirgenes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CintasVirgenes $entity)
    {
        $form = $this->createForm(new CintasVirgenesType(), $entity, array(
            'action' => $this->generateUrl('cintasvirgenes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
                'label' => 'Guardar',
                'attr'  => array('class' => 'btn btn-default' )
            ));

        return $form;
    }
    /**
     * Edits an existing CintasVirgenes entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'SE HA MODIFICADO LA CINTA EXITOSAMENTE');
            return $this->redirect($this->generateUrl('cintasvirgenes_show', array('id' => $id)));
        }

        return $this->render('VideotecaBundle:CintasVirgenes:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CintasVirgenes entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cintasvirgenes'));
    }

    /**
     * Creates a form to delete a CintasVirgenes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cintasvirgenes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                    'label' => 'Eliminar',
                    'attr'  => array('class' => 'btn btn-danger')
                ))
            ->getForm()
        ;
    }
public function asignarCVAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
        }

        $editexitForm = $this->createEditExitForm($entity);

        return $this->render('VideotecaBundle:CintasVirgenes:agregarcv.html.twig', array(
            'entity'          => $entity,
            'editexit_form'   => $editexitForm->createView(),
        ));
    }
    
    private function createEditExitForm(CintasVirgenes $entity)
    {
        $form = $this->createForm(new CintasVirgenesType(), $entity, array(
            'action' => $this->generateUrl('cintasvirgenes_updateCV', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit',
                array('label' => 'GUARDAR',
                      'attr' => array('class' => 'btn btn-default')
            ));

        return $form;
    }
    
    public function updateCVAction(Request $request, $id)
    {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('VideotecaBundle:CintasVirgenes')->find($id);
        $editexitForm = $this->createEditExitForm($entity);
        
        if($request->request->get('cantidad')){
            
            $cantidad = $request->request->get('cantidad');
            $existencia = $entity->getExistencia();
            $nuevaExistencia = $existencia + $cantidad;
            $entity->setExistencia($nuevaExistencia);
            
            if ($cantidad) {
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'SE HA AGREGADO LA CINTA EXITOSAMENTE');
                return $this->redirect($this->generateUrl('cintasvirgenes_show', array('id' => $entity->getId())));
            }
            
        }else{
             $this->get('session')->getFlashBag()->add('alert', 'DEBE ESCRIBIR UNA CANTIDAD A AGREGAR');
             return $this->redirect( $this->generateUrl('cintasvirgenes_asignarCV',array('id'=>$id)));
                 
        }
   
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CintasVirgenes entity.');
        }     
        

        return $this->render('VideotecaBundle:CintasVirgenes:agregarcv.html.twig', array(
            'entity'          => $entity,
            'editexit_form'   => $editexitForm->createView(),
        ));
    }
}
