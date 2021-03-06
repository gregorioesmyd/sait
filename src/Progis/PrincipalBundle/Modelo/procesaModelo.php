<?php

namespace Progis\PrincipalBundle\Modelo;

class procesaModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }
    
    public function infoTicket($consulta) {
        $respuesta['infotarjeta']=null;
        $respuesta['idtarjeta']=null;
        if(!empty($consulta)):
            $t=$consulta[0];
                     
            $respuesta['infotarjeta']='                
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th>Solicitud:</th>
                        <td colspan="3">'.$t->getTicket()->getSolicitud().'</td>
                    </tr>
                    <tr>
                        <th>Solicitante:</th>
                        <td>'.$t->getTicket()->getSolicitante()->getPrimerNombre().' '.$t->getTicket()->getSolicitante()->getPrimerApellido().'</td>
                        <th>Extensión</th>
                        <td>'.$t->getTicket()->getSolicitante()->getExtension().'</td>
                    </tr>
                    <tr>
                        <th>Fecha solicitud:</th>
                        <td>'.$t->getTicket()->getFechasolicitud()->format("d-m-Y").'</td>
                        <th>Hora solicitud:</th>
                        <td>'.$t->getTicket()->getHorasolicitud()->format("G:i:s").'</td>
                    </tr>
                </table>'; 
            $respuesta['idtarjeta']=$t->getTicket()->getId();
        endif;
        return $respuesta;
    }

    public function infoActividad($consulta){
        $respuesta['infotarjeta']=null;
        $respuesta['idtarjeta']=null;
        if(!empty($consulta)):
            $t=$consulta[0];
                     
            $respuesta['infotarjeta']='                
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <th>Descripcion:</th>
                        <td colspan="3">'.$t->getDescripcion().'</td>
                    </tr>
                    <tr>
                        <th>Proyecto:</th>
                        <td>'.$t->getObjetivo()->getProyecto()->getNombre().'</td>
                        <th>Objetivo:</th>
                        <td>'.$t->getObjetivo()->getNombre().'</td>
                    </tr>
                </table>'; 
            $respuesta['idtarjeta']=$t->getId();
        endif;
        return $respuesta;
    }

    //se llama desde el index de la actividad
    public function validaCulminado($bundle,$ubi,$idtarjeta,$idusuario){
        $em=$this->em;
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $consulta=null;
        if($bundle=='ticket'):
            
            $dql = "select p from TicketBundle:ProcesarTicket p join p.ticket t where p.ubicacion=4 and t.solucion is null and p.ticket= :idtarjeta";
            $query = $em->createQuery($dql);
            $query->setParameter('idtarjeta',$idtarjeta);
            $consulta = $query->getResult();
        endif;
        
        return $this->infoTicket($consulta);
    }
    
    //se llama desde el index de la actividad
    public function validaDependencia($bundle,$ubi,$idtarjeta,$idusuario){
        $em=$this->em;
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $consulta=null;
        if($bundle=='ticket'):
            $dql = "select p from TicketBundle:ProcesarTicket p where p.ubicacion=5 and p.justificacion=true and p.ticket= :idtarjeta";
            $query = $em->createQuery($dql);
            $query->setParameter('idtarjeta',$idtarjeta);
            $consulta = $query->getResult();
            return $this->infoTicket($consulta);
        elseif ($bundle=='proyecto'):
            $dql = "select p from ProyectoBundle:ProcesarActividad p where p.ubicacion=5 and p.justificacion=true and p.id= :idtarjeta";
            $query = $em->createQuery($dql);
            $query->setParameter('idtarjeta',$idtarjeta);
            $consulta = $query->getResult();
            return $this->infoActividad($consulta);
        endif;
        
        
    }
}