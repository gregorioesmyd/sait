<?php

namespace Progis\PrincipalBundle\Modelo;

class validaProcesoTicketModelo
{
    
    function __construct($em) {
        $this->em = $em;
        $this->respuesta=array('error'=>null,'sms'=>null);
    }


    public function validarProceso($desde,$hasta,$idusuario) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;
        
        //valido que no se coloquen dos tickets en proceso
            $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable r  where r.id= :idusuario and p.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('idusuario',$idusuario);
            $ticketasignado = $query->getResult();

            if(!empty($ticketasignado)):
                $respuesta['error']=true;
                $respuesta['sms']='Ya posee un ticket en proceso.';
                return $respuesta;
            else:
                $respuesta['sms']='Se ha movido el ticket a proceso.';
            endif;
        //fin validación
        
        
        
        
    }
}