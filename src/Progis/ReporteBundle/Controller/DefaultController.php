<?php

namespace Progis\ReporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\ReporteBundle\Entity\ticketsCreados;
use Progis\ReporteBundle\Form\ticketsCreadosType;

use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        //para obtener los usuarios del espacio de trabajo
        $funcionModelo = $this->get('funcionModelo');
        $miembros=$funcionModelo->miembrosMiEspacioEscalera($perfil);
        //fin

        $usuarios=array();$ids=array();
        foreach ($miembros as $m) {
            $usuarios[]=$m->getUsuario();
        }
        $usuarios=array_unique($usuarios);
        
        $dql = "select x from TicketBundle:ProcesarTicket x join x.responsable u where u.usuario in (:ids) and x.ubicacion=2";
        $query = $em->createQuery($dql);
        $query->setParameter("ids",$usuarios);
        $tickets = $query->getResult();
        
        $procesoTicket=array();
        foreach ($tickets as $t) {
            $procesoTicket[$t->getResponsable()->getUsuario()->getId()]=$t;
        }

        $dql = "select x from ProyectoBundle:ProcesarActividad x join x.responsable mp join mp.miembroespacio me where me.usuario in (:ids) and x.ubicacion=2";
        $query = $em->createQuery($dql);
        $query->setParameter("ids",$usuarios);
        $actividades = $query->getResult();

        $procesoActividad=array();
        foreach ($actividades as $a) {
            $procesoActividad[$a->getResponsable()->getMiembroespacio()->getUsuario()->getId()]=$a;
        }
        
        
        
        return $this->render('ReporteBundle:EnProceso:index.html.twig', array(
            'usuarios'=>  $usuarios,
            'procesoTicket'=>$procesoTicket,
            'procesoActividad'=>$procesoActividad
        ));
        
    }
    
    public function ticketsCreadosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new ticketsCreados();
        $form   = $this->createTicketsCreados($entity);
        
        return $this->render('ReporteBundle:Default:ticketsCreados.html.twig', array('form' => $form->createView()));
    }
    
    public function procesarTicketsCreadosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
            
        $entity = new ticketsCreados();
        $form   = $this->createTicketsCreados($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data=$request->request->all();
            $data=$data['progis_reportebundle_tc'];
            $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($data['usuario']);

            $dql = "select x from TicketBundle:Ticket x where x.registradopor= :rp and x.fechasolicitud>= :fd and x.fechasolicitud<= :fh";
            $query = $em->createQuery($dql);
            $query->setParameter('rp',$data['usuario']);
            $query->setParameter('fd',new \DateTime($data['desde']));
            $query->setParameter('fh',new \DateTime($data['hasta']));
            $tickets = $query->getResult();
            
            
            return $this->render('ReporteBundle:Default:ticketsCreadosResultado.html.twig', array('form' => $form->createView(),'tickets'=>$tickets,'cantidad'=>count($tickets),'perfil'=>$perfil,'datos'=>$data));
        }else $this->get('session')->getFlashBag()->add('alert', 'Alerta! Verifique el formulario.');
        
        return $this->render('ReporteBundle:Default:ticketsCreados.html.twig', array('form' => $form->createView()));
    }
    
    private function createTicketsCreados(ticketsCreados $entity)
    {
        $form = $this->createForm(new ticketsCreadosType(), $entity, array(
            'action' => $this->generateUrl('reporte_procesarTicketsCreados'),
            'method' => 'POST',
        ));
        return $form;
    }
    
  
}
