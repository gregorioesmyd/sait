<?php

namespace Frontend\ProtocoloBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProtocoloBundle\Entity\Regalo;
use Frontend\ProtocoloBundle\Form\RegaloType;

/**
 * Regalo controller.
 *
 */
class RegaloController extends Controller
{

    /**
     * Lists all Regalo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProtocoloBundle:Regalo')->findAll();

        return $this->render('ProtocoloBundle:Regalo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Regalo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Regalo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);      
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
            $query = $em->createQuery($dql);
            $entities = $query->getResult();
            $entity->setSolicitante($entities[0]);

            //envio correo

                $datouser=$em->getRepository('UsuarioBundle:User')->find($idusuario);
                $correouser=$datouser->getUsername()."@telesurtv.net";
                $correoto[]=$correouser;
                /*Correo responsables de protocolo*/
                    $parametros['protocolo']=$this->container->getParameter('protocolo');
                    $salapro=explode(',',$parametros['protocolo']);
                    $correoproto = array_push($salapro,$correouser);
                    $correoproto = $salapro;
                /*Fin*/
                $correoaplica="aplicaciones@telesurtv.net";
               $message = \Swift_Message::newInstance()   
                ->setSubject('SOLICITUD DE PROTOCOLO')  
                ->setFrom($correoaplica)     // we configure the sender
                ->setTo($correoproto)   
                ->setBody( $this->renderView(
                        'ProtocoloBundle:Correo:regalo.html.twig',
                        array('regalo' => $entity,
                              'perfil'        => $entities)
                    ), 'text/html');

                $this->get('mailer')->send($message);
            //fin enviar correo
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA ENVIADO EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('protocolo_homepage', array('id' => $entity->getId())));
        }
        
        return $this->render('ProtocoloBundle:Regalo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Creates a form to create a Regalo entity.
     *
     * @param Regalo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Regalo $entity)
    {
        $form = $this->createForm(new RegaloType(), $entity, array(
            'action' => $this->generateUrl('regalo_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Regalo entity.
     *
     */
    public function newAction()
    {
        $entity = new Regalo();
        $form   = $this->createCreateForm($entity);
        return $this->render('ProtocoloBundle:Regalo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Finds and displays a Regalo entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Regalo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Regalo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Regalo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Regalo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Regalo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Regalo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Regalo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Regalo entity.
    *
    * @param Regalo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Regalo $entity)
    {
        $form = $this->createForm(new RegaloType(), $entity, array(
            'action' => $this->generateUrl('regalo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Regalo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Regalo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Regalo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'LA SOLICITUD FUE EDITADA EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('regalo_show', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', 'OCURRIO UN ERROR AL EDITAR LA SOLICITUD..');
        return $this->render('ProtocoloBundle:Regalo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Regalo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProtocoloBundle:Regalo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Regalo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('regalo'));
    }

    /**
     * Creates a form to delete a Regalo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('regalo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
