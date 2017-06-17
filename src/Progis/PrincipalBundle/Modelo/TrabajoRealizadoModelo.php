<?php

namespace Progis\PrincipalBundle\Modelo;
use Progis\PrincipalBundle\Entity\TrabajoRealizado;

class TrabajoRealizadoModelo
{
    
    function __construct($em,$container) {
        $this->em = $em;
        $this->container=$container;
    }
    


    function tiempoRealEnHoras($tiemporeal){
        $horas=0;
        
        $t=  explode("-", $tiemporeal);
        $horas +=$t[0]/24;
        $horas +=$t[1];
        $horas +=$t[2]/60;
        $horas +=$t[3]/3600;
        
        return $horas;
    }
    
   
    //se llama desde el index de la actividad
    public function guardaTicket($ticket){
        
        
        //if($ticket->getComienzo()->format("Y-m-d")!=$ticket->getFin()->format("Y-m-d")){echo "ajaaaa";return;}
        
        $em=$this->em;
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $trabajoTicket = $em->getRepository('PrincipalBundle:TrabajoTicket')->findBy(array("ticket"=>$ticket,"fecha"=>$fa));
        
        $tiempototal=$this->calcularTiempo($ticket);
        $tt= $this->tiempoRealEnHoras($tiempototal);
        
        
        
        if(empty($trabajoTicket)):
            $a=new \Progis\PrincipalBundle\Entity\TrabajoTicket;
            $a->setTicket($ticket);
            $a->setResponsable($ticket->getResponsable());
            $a->setTipo('t');
            $a->setFecha($fa);
            $a->setHoras($tt);
            $em->persist($a);
            $em->flush();
        else:
            
            $tiempoGuardado=$trabajoTicket[0]->getHoras();

            $horasTotales=$tiempoGuardado+$tt;
            
            
            $trabajoTicket[0]->setHoras($horasTotales);
            $em->flush();
            
        endif;
        
        
        
 
    }
    
    //se llama desde el index de la actividad
    public function guardaActividad($actividad){
        //if($actividad->getComienzo()->format("Y-m-d")!=$actividad->getFin()->format("Y-m-d"))return;
                
        $em=$this->em;
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $trabajoActividad = $em->getRepository('PrincipalBundle:TrabajoActividad')->findBy(array("actividad"=>$actividad,"fecha"=>$fa));
        
        $tiempototal=$this->calcularTiempo($actividad);
        $tt= $this->tiempoRealEnHoras($tiempototal);
        
        if(empty($trabajoActividad)):
            $a=new \Progis\PrincipalBundle\Entity\TrabajoActividad;
            $a->setActividad($actividad);
            $a->setResponsable($actividad->getResponsable()->getMiembroespacio());
            $a->setTipo('a');
            $a->setFecha($fa);
            $a->setHoras($tt);
            $em->persist($a);
            $em->flush();
        else:
            
            $tiempoGuardado=$trabajoActividad[0]->getHoras();

            $horasTotales=$tiempoGuardado+$tt;
            
            
            $trabajoActividad[0]->setHoras($horasTotales);
            $em->flush();
            
        endif;
         
    }
    
    //calcula tiempo que tardan las tarjetas
    public function calcularTiempo($p) {
        
        //calculo el tiempo de duracion con la fecha comienzo y el tiempo actual
        $comienzo=new \DateTime($p->getComienzo()->format("d-m-Y G:i:s"));
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $intervalo=$comienzo->diff($fa);

        //verifico si ya hay un tiempo real guardado y lo sumo
        $tiempo=$intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s;
        
        return $tiempo;
    }
}