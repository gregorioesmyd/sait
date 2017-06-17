<?php

namespace Progis\ReporteBundle\Modelo;

class estadoProcesoModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }

    
    public function consulta($data,$nivel) {
        $em=$this->em;
        $consulta['ticket']=null;
        $consulta['actividad']=null;
        $consulta['metaActividad']=null;
        
        $idMe=array();
        foreach ($data->getMiembroespacio() as $me) {
            $idMe[]=$me->getId();
        }
        
        $ubi=array();
        foreach ($data->getEstatusproceso() as $ep) {
            $ubi[]=$ep;
        }
        
        if(in_array("t", $data->getTipo())):
            $dql = "select x from TicketBundle:ProcesarTicket x join x.ticket t where x.responsable in (:me) and x.ubicacion in (:ubi) and x.ubicacion!=4";
            $query = $em->createQuery($dql);
            $query->setParameter('me',$idMe);
            $query->setParameter('ubi',$ubi);
            $ticket = $query->getResult();
            
            $consulta['ticket']=$ticket;
        endif;
        
        if(in_array("p", $data->getTipo())):
            
            $dql = "select x from PrincipalBundle:MetaActividad x join x.meta m join x.actividad a join a.responsable mp where a.ubicacion in (:ubi) and mp.miembroespacio in (:me) and a.ubicacion!=4  order by m.fechadesde asc, m.fechahasta asc, a.ordenPriometa asc, a.ubicacion ASC ";
            $query = $em->createQuery($dql);
            $query->setParameter('me',$idMe);
            $query->setParameter('ubi',$ubi);
            $metaActividad = $query->getResult();
            $metas=array(0);
            foreach ($metaActividad as $m){
                $metas[]=$m->getActividad()->getId();
            }

            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join o.proyecto p join x.responsable mp where x.id not in (:metas) and x.ubicacion in (:ubi) and mp.miembroespacio in (:me) and x.ubicacion!=4 order by x.ubicacionPriometa DESC, x.ordenPriometa ASC,x.ubicacion ASC";
            $query = $em->createQuery($dql);
            $query->setParameter('me',$idMe);
            $query->setParameter('ubi',$ubi);
            $query->setParameter('metas',$metas);
            $actividad = $query->getResult();
            
            $consulta['actividad']=$actividad;
            $consulta['metaActividad']=$metaActividad;
            
        endif;

        return $consulta;




    }

}