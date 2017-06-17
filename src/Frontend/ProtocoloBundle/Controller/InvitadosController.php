<?php

namespace Frontend\ProtocoloBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\ProtocoloBundle\Entity\Transporte;
use Frontend\ProtocoloBundle\Entity\Invitados;
use Frontend\ProtocoloBundle\Form\InvitadosType;
use Frontend\ProtocoloBundle\Form\TransporteType;

/**
 * Invitados controller.
 *
 */
class InvitadosController extends Controller
{

    /**
     * Lists all Invitados entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ProtocoloBundle:Invitados')->findAll();

        return $this->render('ProtocoloBundle:Invitados:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Invitados entity.
     *
     */
    public function createAction(Request $request)
    {
         $entity = new Invitados();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
            $dql = "select p from UsuarioBundle:Perfil p join p.user u where u.isActive=true and p.id=$idusuario";
            $query = $em->createQuery($dql);
            $entities = $query->getResult();
            $entity->setSolicitante($entities[0]);

            //Verifico que solicita transporte del canal
            if($entity->getTransportePersonal()=='false'){

                if($form->get('horaBusqueda')->getData() == ''){
                    $this->get('session')->getFlashBag()->add('alert', 'LA SOLICITUD NO FUE ENVIADA EL CAMPO DE HORA DE LA BUSQUEDA NO PUEDE ESTA VACIO..');
                    return $this->render('ProtocoloBundle:Invitados:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView()
                    ));
                }elseif ($form->get('tlfContacto')->getData()== '') {
                   $this->get('session')->getFlashBag()->add('alert', 'LA SOLICITUD NO FUE ENVIADA,EL CAMPO TELÉFONO DE CONTACTO NO PUEDE ESTAR VACIO..');
                    return $this->render('ProtocoloBundle:Invitados:new.html.twig', array(
                        'entity' => $entity,
                        'form'   => $form->createView()
                    ));
                }

                //envio correo a Protocolo

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
                            'ProtocoloBundle:Correo:invitado.html.twig',
                            array('invitado'      => $entity,
                                  'perfil'        => $entities)
                        ), 'text/html');

                    $this->get('mailer')->send($message);
                //fin enviar correo

                $em->persist($entity);
                $em->flush();

                $transporte    = new Transporte();
                $idinvitados   = $em->getRepository('ProtocoloBundle:Invitados')->find($entity->getId());
                $transporte->setInvitado($idinvitados);
                $transporte->setHoraBusqueda($form->get('horaBusqueda')->getData());
                $transporte->SetTlfContacto($form->get('tlfContacto')->getData());

                $em->persist($transporte);
                $em->flush();

                //Envio de correo a transporte 
                    /*Correo responsables de protocolo*/
                        $parametros['transporte']=$this->container->getParameter('transporte');
                        $salapro=explode(',',$parametros['transporte']);
                        $correotrasnp = array_push($salapro,$correouser);
                        $correotrasnp = $salapro;
                    /*Fin*/
                    $correoaplica="aplicaciones@telesurtv.net";
                    $message = \Swift_Message::newInstance()   
                    ->setSubject('NOTIFICACION DE SOLICITUD TRANSPORTE')  
                    ->setFrom($correoaplica)     // we configure the sender
                    ->setTo($correotrasnp)   
                    ->setBody( $this->renderView(
                            'ProtocoloBundle:Correo:transporteInvitados.html.twig',
                            array('transporte'      => $transporte,
                                  'perfil'          => $entities)
                        ), 'text/html');

                    $this->get('mailer')->send($message);

                if($entity->getWifi()=='true'){
                //Envio de correo a plataforma
                    /*Correo responsables de protocolo*/
                        $parametros['soporte']=$this->container->getParameter('soporte');
                        $salapro=explode(',',$parametros['soporte']);
                        $correowifi = array_push($salapro,$correouser);
                        $correowifi = $salapro;
                    /*Fin*/
                    $correoaplica="aplicaciones@telesurtv.net";
                   $message = \Swift_Message::newInstance()   
                    ->setSubject('NOTIFICACION DE SOLICITUD WIFI')  
                    ->setFrom($correoaplica)     // we configure the sender
                    ->setTo($correowifi)   
                    ->setBody( $this->renderView(
                            'ProtocoloBundle:Correo:wifi.html.twig',
                            array('invitado'      => $entity,
                                  'perfil'    => $entities)
                        ), 'text/html');

                    $this->get('mailer')->send($message);
                } 
           }else{
                //envio correo a Protocolo

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
                            'ProtocoloBundle:Correo:invitado.html.twig',
                            array('invitado'      => $entity,
                                  'perfil'        => $entities)
                        ), 'text/html');

                    $this->get('mailer')->send($message);
                //fin enviar correo

                $em->persist($entity);
                $em->flush();

                if($entity->getWifi()=='true'){
                    //Envio de correo a plataforma
                    /*Correo responsables de protocolo*/
                        $parametros['soporte']=$this->container->getParameter('soporte');
                        $salapro=explode(',',$parametros['soporte']);
                        $correowifi = array_push($salapro,$correouser);
                        $correowifi = $salapro;
                    /*Fin*/
                        $correoaplica="aplicaciones@telesurtv.net";
                       $message = \Swift_Message::newInstance()   
                        ->setSubject('NOTIFICACION DE SOLICITUD WIFI')  
                        ->setFrom($correoaplica)     // we configure the sender
                        ->setTo($correowifi)   
                        ->setBody( $this->renderView(
                                'ProtocoloBundle:Correo:wifi.html.twig',
                                array('invitado'      => $entity,
                                      'perfil'    => $entities)
                            ), 'text/html');

                        $this->get('mailer')->send($message);
                }   
    
        }
            

            $this->get('session')->getFlashBag()->add('notice', 'LA SOLICITUD FUE ENVIADA EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('protocolo_homepage', array('id' => $entity->getId())));       
            //Fin
            
    }
          
        return $this->render('ProtocoloBundle:Invitados:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
}

    /**
     * Creates a form to create a Invitados entity.
     *
     * @param Invitados $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Invitados $entity)
    {
        $form = $this->createForm(new InvitadosType(), $entity, array(
            'action' => $this->generateUrl('invitados_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Invitados entity.
     *
     */
    public function newAction()
    {
        $entity = new Invitados();
        $form   = $this->createCreateForm($entity);
        return $this->render('ProtocoloBundle:Invitados:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
     * Finds and displays a Invitados entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Invitados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invitados entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Invitados:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Invitados entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Invitados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invitados entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProtocoloBundle:Invitados:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Invitados entity.
    *
    * @param Invitados $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Invitados $entity)
    {
        $form = $this->createForm(new InvitadosType(), $entity, array(
            'action' => $this->generateUrl('invitados_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Invitados entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProtocoloBundle:Invitados')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invitados entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'LA SOLICITUD FUE EDITADA EXITOSAMENTE..');
            return $this->redirect($this->generateUrl('invitados_show', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('alert', 'OCURRIO UN ERROR AL EDITAR LA SOLICITUD..');
        return $this->render('ProtocoloBundle:Invitados:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Invitados entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProtocoloBundle:Invitados')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Invitados entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('invitados'));
    }

    /**
     * Creates a form to delete a Invitados entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('invitados_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
