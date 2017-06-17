<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EstatusController extends Controller
{
    
    public function pendientesAction($idusuario,$desde)
    {
        $em = $this->getDoctrine()->getManager();
        $perfil=  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        
        //verifico en proceso
        $dql = "select x from ProyectoBundle:ProcesarActividad x join x.responsable mp join mp.miembroespacio me where me.usuario= :usuario and x.ubicacion=2";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $actividadProceso = $query->getResult();
        
        $dql = "select x from TicketBundle:ProcesarTicket x join x.ticket t join x.responsable me where me.usuario= :usuario and x.ubicacion=2";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $ticketProceso = $query->getResult();
        
        $proyectoModelo=$this->get("proyectoModelo"); 
        $pendientes=$proyectoModelo->pendientes($perfil);
        

        
        
        return $this->render('PrincipalBundle:EstatusProceso:pendientes.html.twig',array(
            'perfil'=>$perfil,
            'pendientes'=>$pendientes,
            'actividadProceso'=>$actividadProceso,
            'ticketProceso'=>$ticketProceso,
            'desde'=>$desde
            ));
    }
    
    public function revisarAction($idusuario)
    {
        $em = $this->getDoctrine()->getManager();
        $miembroEspacio=  $em->getRepository('PrincipalBundle:Miembroespacio')->find($idusuario);
        $perfil =  $miembroEspacio->getUsuario();
        
        $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join x.responsable mp join mp.miembroespacio me where me.usuario= :usuario and x.ubicacion=3 order by x.fin, o.fechafinestimada asc";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $actividad = $query->getResult();
        
        return $this->render('PrincipalBundle:EstatusProceso:revisar.html.twig',array('perfil'=>$perfil,'actividad'=>$actividad));
    }
    
    

    
}
