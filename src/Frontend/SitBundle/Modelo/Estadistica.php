<?php

namespace Frontend\SitBundle\Modelo;

class Estadistica
{
    function __construct($em) {
        $this->em = $em;
    }

    public function consulta($datos)
    {
        $em=$this->em;
        
        
        $dql = "select u.id,u.primerNombre,u.primerApellido from SitBundle:Ticket x join x.user u where u.id in (:idusuario) and x.fechasolicitud BETWEEN :desde AND :hasta order by u.id ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idusuario',$datos->getUsuario());
        $query->setParameter('desde',$datos->getFechadesde());
        $query->setParameter('hasta',$datos->getFechahasta());
        $ticket = $query->getResult();
        
        $resultado=array();
        foreach ($datos->getUsuario() as $u) {
          $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($u);
          $resultado[$perfil->getPrimerNombre().' '.$perfil->getPrimerApellido()]=0;
        }

        
        foreach ($ticket as $t) {
            $resultado[$t['primerNombre'].' '.$t['primerApellido']]=$resultado[$t['primerNombre'].' '.$t['primerApellido']]+1;
        }  
        
        return $resultado;
    }
   
}