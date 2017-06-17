<?php

namespace Progis\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Progis\PrincipalBundle\Entity\Comentarioarchivo;
use Progis\PrincipalBundle\Entity\ComentarioTicket;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProcesarTicketController extends Controller
{
    public function seguridad() {

        $admin=false;if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolTicket($session,$admin);
    }
    
    //FUNCIONES
    public function cuentaRegresiva($em, $perfil,$tipo){
        
        $ticketModelo = $this->get('ticketModelo');
        $idNivelesUsuarioRolTicket=$ticketModelo->idNivelesUsuarioRolTicket($perfil);
        
        if($tipo=='ticket')
            $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable r join p.ticket t where t.nivelorganizacional in (:niveles) and r.usuario= :usuario and p.ubicacion=2 ";
        else if($tipo=='otros')
            $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable r join p.ticket t where t.nivelorganizacional in (:niveles) and r.usuario!= :usuario and p.ubicacion=2 ";
        
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $query->setParameter('niveles',$idNivelesUsuarioRolTicket);
        $procesarEnProceso = $query->getResult();
        
        $cuentaregresiva=array();
        $ubicacionModelo = $this->get('ubicacionModelo');
        
        foreach ($procesarEnProceso as $pep) {
            $cuentaregresiva[$pep->getTicket()->getId()]=$ubicacionModelo->calculaCuentaRegresiva($pep,'ticket'); 
        }
        
        return $cuentaregresiva;
    }
    
    public function misTickets($em, $idusuario) {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable me join p.ticket t where me.usuario= :usuario order by p.orden asc,t.fechasolicitud asc ";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $procesar = $query->getResult();
        return $procesar;
    }
    
    public function otrosTickets($em, $idusuario) {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        //busco los niveles donde el usuario tiene rol de ticket
        $ticketModelo = $this->get('ticketModelo');
        $idNivelesUsuarioRolTicket=$ticketModelo->idNivelesUsuarioRolTicket($perfil);
        //fin

        $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable me join p.ticket t where me.usuario!= :usuario and t.nivelorganizacional in (:idnivel) order by t.fechasolicitud desc, t.fechasolucion desc ";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $query->setParameter('idnivel',$idNivelesUsuarioRolTicket);
        $procesar = $query->getResult();
        return $procesar;
    }
    //FIN FUNCIONES

    public function procesarTicketAction()
    {
        $session = $this->getRequest()->getSession();
        $rolGeneral=$session->get('rolGeneral');

        if(
                   $rolGeneral['ROLE_PROGIS_TICKET_ADMIN']==true
                or $rolGeneral['ROLE_PROGIS_TICKET_TECNICO']==true 
                or $rolGeneral['ROLE_PROGIS_SUPERVISOR']==true 
                or $this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN')
          );
        
        else throw new AccessDeniedException();
        
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $smsModal=null;
        
        $mistickets=$this->misTickets($em, $idusuario);
        $cuentaregresiva=$this->cuentaRegresiva($em, $perfil,'ticket');
        
        //cantidad de comentarios en actividades de los objetivos
        $ticketModelo = $this->get('ticketModelo');
        $cantidadComentarioTicket=$ticketModelo->cantidadComentarioTicket($mistickets);

        return $this->render('TicketBundle:Procesar:procesarTicket.html.twig',array('cantidadComentarioTicket'=>$cantidadComentarioTicket,'procesar'=>$mistickets,'cuentaregresiva'=>$cuentaregresiva,'smsModal'=>$smsModal));
    }
    
    public function procesarTicketOtrosAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $smsModal=null;

        $otrostickets=$this->otrosTickets($em, $idusuario);
        $cuentaregresiva=$this->cuentaRegresiva($em, $perfil,'otros');
        
        //cantidad de comentarios en actividades de los objetivos
        $ticketModelo = $this->get('ticketModelo');
        $cantidadComentarioTicket=$ticketModelo->cantidadComentarioTicket($otrostickets);

        return $this->render('TicketBundle:Procesar:procesarTicketOtros.html.twig',array('cantidadComentarioTicket'=>$cantidadComentarioTicket,'procesar'=>$otrostickets,'cuentaregresiva'=>$cuentaregresiva,'smsModal'=>$smsModal));
        
    }
    
    public function CerrarTicketAction(Request $request,$idticket)
    {   
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $mistickets=$this->misTickets($em, $idusuario);
        $cuentaregresiva=$this->cuentaRegresiva($em, $perfil,'ticket');
            
        $t = $em->getRepository('TicketBundle:Ticket')->find($idticket);

        if($request->get("solucion")==null):

            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La solución no debe estar en blanco.";
        
            return $this->render('TicketBundle:Procesar:procesarTicket.html.twig',array('procesar'=>$mistickets,'cuentaregresiva'=>$cuentaregresiva,'smsModal'=>$smsModal));

        elseif(strlen($request->get("solucion"))<=10):
            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La solución debe contener al menos 10 caracteres.";
            return $this->render('TicketBundle:Procesar:procesarTicket.html.twig',array('procesar'=>$mistickets,'cuentaregresiva'=>$cuentaregresiva,'smsModal'=>$smsModal));

        else:
            $fs=new \DateTime(\date("d-m-Y"));
            $hs=new \DateTime(\date("G:i:s"));
            $t->setFechasolucion($fs);
            $t->setHorasolucion($hs);
            $t->setSolucion($request->get("solucion"));
            $t->setEstatus(4);
            $em->flush();
            
            //envio correo de ticket asignado
            $html= $this->renderView('TicketBundle:Correo:cerrado.html.twig',array('ticket' => $t));
            $funcion=$this->get("funcionModelo");
            $correo=$funcion->correo('Califique la atencion del ticket cerrado','Progis_Informacion',array($t->getSolicitante()->getUser()->getUsername().'@telesurtv.net'),$html);
            $this->get('mailer')->send($correo); 
            //fin envio correo
          
            $this->get('session')->getFlashBag()->add('notice', 'El ticket se ha cerrado con exito');
            return $this->redirect($this->generateUrl('progis_ticket_procesarTicket'));
        endif;
    }
    
   public function JustificarTicketAction(Request $request,$idticket)
    {  
        $this->seguridad();
    
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $ticket = $em->getRepository('TicketBundle:Ticket')->find($idticket);

        if($request->get("justificacion")==null):
            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La justificación no debe estar en blanco.";

        elseif(strlen($request->get("justificacion"))<=10):
            $smsModal="<b>Alerta! Ha ocurrido un error en el formulario:</b><br>La justificación debe contener al menos 10 caracteres.";

        else:
            $coment=new Comentarioarchivo;
        
            $fa=new \DateTime(\date("d-m-Y G:i:s"));

            $coment->setComentario($request->get("justificacion"));
            $coment->setResponsable($perfil);
            $coment->setFechahora($fa);
            $coment->setTipo(4);
            $em->persist($coment);
            $em->flush();
            
            $comentarioEntidad=$coment;
            
            $entity = new ComentarioTicket();
            $entity->setTicket($ticket);
            $entity->setComentarioarchivo($comentarioEntidad);
            $em->persist($entity);
            $em->flush();
            
            
            //cambio la justificacion a false
            $procesarTicket = $em->getRepository('TicketBundle:ProcesarTicket')->find($idticket);
            $procesarTicket->setJustificacion(false);
            $em->flush();
            
            
            $this->get('session')->getFlashBag()->add('notice', 'La justificación se ha realizado con exito');
            return $this->redirect($this->generateUrl('progis_comentarioarchivo_lista',array("id"=>$idticket,"entidad"=>"Ticket",'desde'=>"Ticket")));
        endif;
        
        $mistickets=$this->misTickets($em, $idusuario);
        $cuentaregresiva=$this->cuentaRegresiva($em, $perfil,'ticket');
        return $this->render('TicketBundle:Procesar:procesarTicket.html.twig',array('procesar'=>$mistickets,'cuentaregresiva'=>$cuentaregresiva,'smsModal'=>$smsModal));
    }
    
}
