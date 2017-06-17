<?php

namespace Progis\PrincipalBundle\Modelo;

class ordenModelo
{
    
    function __construct($em) {
        $this->em = $em;
        $this->respuesta=array('error'=>null,'sms'=>null,'numero'=>null);
    }


    //se llama desde el index de la actividad
    public function orden($recibe){
        $em=$this->em;

        //divido los datos enviados desde el ajax
        $datos=explode("||", $recibe);
        $ids = explode(",", $datos[0]);
        $bundleActual = $datos[1];

        //guardo el orden
        $cont=0;
        foreach ($ids as $id) {
            $cont++;
            
            $arrayId=  explode("-", $id);
            $id=$arrayId[0];
            
            if($bundleActual=='ticket'):
                $query = $em->createQuery('update TicketBundle:ProcesarTicket x set x.orden= :orden WHERE x.ticket= :id');
            elseif($bundleActual=='proyecto'):
                $query = $em->createQuery('update ProyectoBundle:ProcesarActividad x set x.orden= :orden WHERE x.id= :id');
            endif;
            
            $query->setParameter('orden', $cont);
            $query->setParameter('id', $id);
            $query->execute();
        }
        return;
    }
    
    //se llama desde el index de la actividad
    public function ordenPriometa($recibe){
        $em=$this->em;
        $respuesta=$this->respuesta;
        $tiempo=0;

        //divido los datos enviados desde el ajax
        $datos=explode("||", $recibe);
        $ids = explode(",", $datos[0]);
        $bundleActual = $datos[1];

        //guardo el orden
        $cont=0;$orden=array();
        foreach ($ids as $id) {
            $horas=0;$minutos=0;
            $cont++;
            
            $arrayId=  explode("-", $id);
            $id=$arrayId[0];
            
            
            if($bundleActual=='ticket'):
                $query = $em->createQuery('update TicketBundle:ProcesarTicket x set x.orden= :orden WHERE x.ticket= :id');
            elseif($bundleActual=='proyecto'):
                $entity = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
                $entity->setOrdenPriometa($cont);
            endif;
            $em->flush();
            
            $orden[]=$id."-".$cont;
        }
        
        
        
            $respuesta['numero']=$orden;
            
            return $respuesta;
        return;
    }
}