<?php

namespace Progis\TicketBundle\Modelo;

class ticketModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }
    
    public function cantidadComentarioTicket($tickets) 
    {
        $arrayCantidad=array();
        
        if(!empty($tickets)):
            $em = $this->em;
            $dql = "select x from PrincipalBundle:ComentarioTicket x where x.ticket in (:tickets)";
            $query = $em->createQuery($dql);
            $query->setParameter('tickets',$tickets);
            $entities = $query->getResult();


            if(!empty($entities)):
                foreach ($entities as $ct) {
                    $arrayCantidad[$ct->getTicket()->getId()]=0;
                }

                foreach ($entities as $ct) {
                    $arrayCantidad[$ct->getTicket()->getId()]=$arrayCantidad[$ct->getTicket()->getId()]+1;
                }  
            endif;
        endif;

        return $arrayCantidad;
        
    }

    
    //una vez obtenidos los id de la unidades a las que pertenece el usuario
    //Busco los usuarios que tienen rol de tickets y se encuentran en los espacios donde el usuario actual esta.
    public function miembroTicket($funcionModelo,$perfil){

        $em = $this->em;
        
        $idnivel=  $funcionModelo->idNivelMiembroEspacio($perfil);

        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u join me.rolgeneral r where no.id in (:idnivel) and u.isActive=true and me.activo=true and r.rol like 'ROLE_PROGIS_TICKET%' and r.rol!= 'ROLE_PROGIS_TICKET_CONSULTA' order by p.primerNombre, p.primerApellido ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idnivel);
        $miembroTicket=$query->getResult();
        
        return $miembroTicket;  
    }
    
    //obtengo las unidades donde el usuario actual tiene perfil de ticket
    public function idNivelesUsuarioRolTicket($perfil){

        $em = $this->em;
        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.rolgeneral r where me.usuario=:perfil and me.activo=true and (r.rol like 'ROLE_PROGIS_TICKET%' or r.rol='ROLE_PROGIS_SUPERVISOR')";
        $query = $em->createQuery($dql);
        $query->setParameter('perfil',$perfil);
        $miembroTicket=$query->getResult();
        
        $idnivel=array();
        
        //obtengo los roles del usuario actual
        $roles=$perfil->getUser()->getRol();
        
        foreach ($roles as $r) {
            if($r->getRol()=='ROLE_PROGIS_ADMIN')
                $idnivel[]=$perfil->getNivelorganizacional()->getId();
        }
        
        foreach ($miembroTicket as $mt) {
            if(!in_array($mt->getNivelorganizacional()->getId(),$idnivel))
                $idnivel[]=$mt->getNivelorganizacional()->getId();
        }
        
        return $idnivel;
    }
    
    //extraigo los id de la unidades de los miembros de tickets
   /* public function idNivelMiembroTicket($miembroTicket,$perfil) {
        $em = $this->em;
        
        $idnivel=array();
        
        //obtengo los roles del usuario actual
        $roles=$perfil->getUser()->getRol();
        
        foreach ($roles as $r) {
            if($r->getRol()=='ROLE_PROGIS_ADMIN')
                $idnivel[]=$perfil->getNivelorganizacional()->getId();
        }
        
        foreach ($miembroTicket as $mt) {
            if(!in_array($mt->getNivelorganizacional()->getId(),$idnivel))
                $idnivel[]=$mt->getNivelorganizacional()->getId();
        }
        
        return $idnivel;
    }*/


}