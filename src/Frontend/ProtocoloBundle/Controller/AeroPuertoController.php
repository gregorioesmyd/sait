<?php

namespace Frontend\ProtocoloBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Frontend\ProtocoloBundle\Entity\AeroPuerto;
use Frontend\ProtocoloBundle\Form\AeroPuertoType;

/**
 * AeroPuerto controller.
 *
 */
class AeroPuertoController extends Controller
{

    /**
     * Lists all AeroPuerto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProtocoloBundle:AeroPuerto')->findAll();

        return $this->render('ProtocoloBundle:AeroPuerto:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new AeroPuerto entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AeroPuerto();
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


                /* Correo para el personal de transporte */
                    $parametros['transporte']=$this->container->getParameter('transporte');
                    $correo_transporte=explode(',',$parametros['transporte']);

                /* Fin */

                $correos = array();

                foreach ($correo_transporte as $key) {
                  array_push($correoproto,$key);
                }
                
                $correos = $correoproto;
                
               $correoaplica="aplicaciones@telesurtv.net";
               $message = \Swift_Message::newInstance()   
                ->setSubject('SOLICITUD DE PROTOCOLO')  
                ->setFrom($correoaplica)     // we configure the sender
                ->setTo($correos)   
                ->setBody( $this->renderView(
                        'ProtocoloBundle:Correo:aeropuerto.html.twig',
                        array('aeropuerto' => $entity,
                              'perfil'     => $entities)
                    ), 'text/html');

                $this->get('mailer')->send($message);
            //fin enviar correo
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'TU SOLICITUD SE HA ENVIADO EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('protocolo_homepage', array('id' => $entity->getId())));
        }
         return $this->render('ProtocoloBundle:AeroPuerto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AeroPuerto entity.
     *
     * @param AeroPuerto $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AeroPuerto $entity)
    {
        $form = $this->createForm(new AeroPuertoType(), $entity, array(
            'action' => $this->generateUrl('aeropuerto_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new AeroPuerto entity.
     *
     */
    public function newAction()
    {
        $entity = new AeroPuerto();
        $form   = $this->createCreateForm($entity);

        return $this->render('ProtocoloBundle:AeroPuerto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AeroPuerto entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:AeroPuerto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AeroPuerto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:AeroPuerto:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AeroPuerto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:AeroPuerto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AeroPuerto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:AeroPuerto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AeroPuerto entity.
    *
    * @param AeroPuerto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AeroPuerto $entity)
    {
        $form = $this->createForm(new AeroPuertoType(), $entity, array(
            'action' => $this->generateUrl('aeropuerto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing AeroPuerto entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:AeroPuerto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AeroPuerto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('aeropuerto_edit', array('id' => $id)));
        }

        return $this->render('ProtocoloBundle:AeroPuerto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a AeroPuerto entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProtocoloBundle:AeroPuerto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AeroPuerto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('aeropuerto'));
    }

    /**
     * Creates a form to delete a AeroPuerto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aeropuerto_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
