<?php

namespace Progis\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progis\TicketBundle\Entity\Ticket;
use Progis\TicketBundle\Form\TicketType;
use Progis\TicketBundle\Form\SolicitudInternaType;

class SolicitudController extends Controller
{
    public function solicitudAction()
    {
        $entity = new Ticket();
        $form   = $this->createCreateForm($entity);
        
        return $this->render('TicketBundle:Solicitud:solicitud.html.twig', array('form' => $form->createView()));
    }
    
    public function solicitudInternaAction()
    {
        $entity = new Ticket();
        $form   = $this->createCreateFormSI($entity);
        
        return $this->render('TicketBundle:Solicitud:solicitudInterna.html.twig', array('form' => $form->createView()));
    }
    
    private function createCreateForm(Ticket $entity)
    {
        $form = $this->createForm(new TicketType(), $entity, array(
            'action' => $this->generateUrl('progis_ticket_procesarSolicitud'),
            'method' => 'POST',
        ));
        return $form;
    }
    
    private function createCreateFormSI(Ticket $entity)
    {
        $form = $this->createForm(new SOlicitudInternaType(), $entity, array(
            'action' => $this->generateUrl('progis_ticket_procesarSolicitudInterna'),
            'method' => 'POST',
        ));
        return $form;
    }
    
    public function procesarSolicitudAction(Request $request)
    {
        
        
        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = new Ticket();
        $form   = $this->createCreateForm($entity);
        
        //agrego solicitante para saltar la validacion
        $data=$form->getData();
        $data->setSolicitante($perfil);
        
        $form->bind($request);

        if ($form->isValid()) {
            $fa=new \DateTime(\date("d-m-Y"));
            $ha=new \DateTime(\date("G:i:s"));
            
            $entity->setSolicitante($perfil);
            $entity->setFechasolicitud($fa);
            $entity->setHorasolicitud($ha);
            $entity->setReasignado(false);
            $entity->setEstatus(1);
            $entity->setRegistradopor($perfil);
                    
            //procesar archivo
            if($form['file']->getData()):
                
                $file=$form['file']->getData();
                
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $nombre=$file->getClientOriginalName();
                $nombreLargo=explode(".", $nombre);
                $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
                $extension = $nombreLargo[1];
                
                //validaciones
                    $extensiones=array('jpg','jpeg','png','gif','doc','odt','xls','xlsx','docx','pdf','ppt','pptx','zip','rar','odt','ods','JPG','JPEG','PNG','GIF','DOC','ODT','XLS','XLSX','DOCX','PDF','PPT','PPTX','ZIP','RAR','ODT','ODS','txt','TXT');
                    if (!in_array($extension,$extensiones)) {
                        $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                        return $this->render('TicketBundle:Solicitud:solicitud.html.twig', array('form' => $form->createView()));
                    }
                //fin
                    
                if($file->move('/var/www/uploads/sit/',$nombre.'_'.\date("Gis").'.'.$extension) )
                {
                    $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
                }
                
            endif;
            //fin procesar archivo
            
            $em->persist($entity);
            $em->flush();

            //envio correo
            $html= $this->renderView('TicketBundle:Correo:solicitud.html.twig',array('ticket' => $entity));
            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Tienes una nueva solicitud','Progis_Informacion',array($entity->getNivelorganizacional()->getCorreo()),$html);
            $this->get('mailer')->send($correo); 
            //fin envio correo
            
            $this->get('session')->getFlashBag()->add('notice', 'El ticket fue enviado con éxito, será notificado por correo electrónico sobre los avances del mismo.');
            return $this->redirect($this->generateUrl('ticket_homepage'));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Ha ocurrido un error en el formulario.');
        return $this->render('TicketBundle:Solicitud:solicitud.html.twig', array('form' => $form->createView()));
    }
    
    public function procesarSolicitudInternaAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);

        $entity = new Ticket();
        $form   = $this->createCreateFormSI($entity);
        $form->bind($request);

        if ($form->isValid()) {
            $fa=new \DateTime(\date("d-m-Y"));
            $ha=new \DateTime(\date("G:i:s"));
            
            $entity->setFechasolicitud($fa);
            $entity->setHorasolicitud($ha);
            $entity->setReasignado(false);
            $entity->setEstatus(1);
            $entity->setRegistradopor($perfil);
                    
            //procesar archivo
            if($form['file']->getData()):
                
                $file=$form['file']->getData();
                
                $tamaño=number_format($file->getClientSize(),0, ',', '')/1000;
                $nombre=$file->getClientOriginalName();
                $nombreLargo=explode(".", $nombre);
                $nombre= str_replace(array(" ","."),array("",""),$nombreLargo[0]);
                $extension = $nombreLargo[1];
                
                //validaciones
                    $extensiones=array('jpg','jpeg','png','gif','doc','odt','xls','xlsx','docx','pdf','ppt','pptx','zip','rar','odt','ods');
                    if (!in_array($extension,$extensiones)) {
                        $this->get('session')->getFlashBag()->add('alert', 'El formato de archivo que intenta subir no está permitido.');
                        return $this->render('TicketBundle:Solicitud:solicitudInterna.html.twig', array('form' => $form->createView()));
                    }
                //fin
                    
                if($file->move('/var/www/uploads/sit/',$nombre.'_'.\date("Gis").'.'.$extension) )
                {
                    $entity->setArchivo($nombre.'_'.\date("Gis").'.'.$extension);
                }
                
            endif;
            //fin procesar archivo
            
            $em->persist($entity);
            $em->flush();

            //envio correo
            $html= $this->renderView('TicketBundle:Correo:solicitud.html.twig',array('ticket' => $entity));
            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Tienes una nueva solicitud','Progis_Informacion',array($entity->getNivelorganizacional()->getCorreo()),$html);
            $this->get('mailer')->send($correo); 
            //fin envio correo
            
            $this->get('session')->getFlashBag()->add('notice', 'El ticket fue enviado con éxito, el usuario será notificado por correo electrónico sobre los avances del mismo.');
            return $this->redirect($this->generateUrl('ticket_solicitudInterna'));
        }

        $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Ha ocurrido un error en el formulario.');
        return $this->render('TicketBundle:Solicitud:solicitudInterna.html.twig', array('form' => $form->createView()));
    }
    
    public function misticketsAction()
    {
        $em = $this->getDoctrine()->getManager();
        //traigo los datos del usuario conectado
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        
        $tickets =  $em->getRepository('TicketBundle:Ticket')->findBySolicitante($perfil);
        
        return $this->render('TicketBundle:Solicitud:mistickets.html.twig', array('tickets' => $tickets));
    }
    
}
