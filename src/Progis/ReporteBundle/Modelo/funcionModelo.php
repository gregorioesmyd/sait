<?php

namespace Progis\PrincipalBundle\Modelo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class funcionModelo extends Controller
{
    function __construct($em) {
        $this->em = $em;
    }
    
    
    
    
    //gestiono la manera en que se comunican los roles de progis con symfony
    public function gestionarProgis($usuario){
        
        $em=$this->em;
        
        //busco si posee rol de proyecto
        $dql = " select x from ProyectoBundle:Miembroproyecto x 
                 join x.miembroespacio me
                 where me.usuario= :usuario";

        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$usuario);
        $rolProyecto = $query->getResult();
        
        
        //busco si posee rol general
        $dql = "select x from PrincipalBundle:Miembroespacio x where x.poseerolgeneral=true and x.usuario= :usuario";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$usuario);
        $rolGeneral = $query->getResult();
        
        $rolProgis = $em->getRepository('UsuarioBundle:Rol')->findByRol('ROLE_PROGIS');
        if(empty($rolProyecto) and empty($rolGeneral)):
            $usuario->getUser()->removeRol($rolProgis[0]);
        else:
            $usuario->getUser()->removeRol($rolProgis[0]);
            $usuario->getUser()->addRol($rolProgis[0]);
        endif;
        
        $em->flush();
        
        /*
        
        //definicion
            $miembroEspacio = $em->getRepository('PrincipalBundle:Miembroespacio')->find($idMiembroEspacio);
            $rolProgis = $em->getRepository('UsuarioBundle:Rol')->findByRol('ROLE_PROGIS');
            $rolGeneral=$miembroEspacio->getRolgeneral();
            $usuario=$miembroEspacio->getUsuario();
        //fin definiciones
        
        //detecto si tiene el tipo de rol en otro espacio
        $dql = "select x from PrincipalBundle:Miembroespacio x join x.rolgeneral rg where x.usuario= :usuario and rg.rol like '%ROLE_%'";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$usuario);
        $rolesAsignados = $query->getResult();

        
        //si no tenia asignados y se le esta asignando le tildo el rol por defecto en symfony
        if(!empty($rolesAsignados)):
            $usuario->getUser()->removeRol($rolProgis[0]);
            $usuario->getUser()->addRol($rolProgis[0]);
        elseif(empty($rolesAsignados)):
            $usuario->getUser()->removeRol($rolProgis[0]);
        endif;
        
        $em->flush();*/
    }
    
    //gestiono la manera en que se comunican los roles de progis con symfony
    public function gestionarRolProyecto($usuario){
        
        $em=$this->em;

        //detecto si tiene el tipo de rol en otro proyecto y espacio para borrar el de progis symfony
        $dql = " select x from ProyectoBundle:Miembroproyecto x 
                 join x.rolproyecto rp 
                 join x.miembroespacio me

                 where 

                 x.id!=:idmiembroProyecto and me.usuario= :usuario";

        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$usuario);
        $rolProyecto = $query->getResult();
        
        /*
        //definicion
            $miembroProyecto= $em->getRepository('ProyectoBundle:Miembroproyecto')->find($idMiembroProyecto);
            $rolProgis = $em->getRepository('UsuarioBundle:Rol')->findByRol('ROLE_PROGIS');
            
            $rolProyecto=$miembroProyecto->getRolproyecto();
            $usuario=$miembroProyecto->getMiembroEspacio()->getUsuario();
        //fin definiciones
        
        //detecto si tiene el tipo de rol en otro proyecto
        $dql = "select x from ProyectoBundle:Miembroproyecto x join x.rolproyecto rp join x.miembroespacio me where me.usuario= :usuario and rp.rol like '%ROLE_%'";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$usuario);
        $rolesAsignados = $query->getResult();

        
        //si no tenia asignados y se le esta asignando le tildo el rol por defecto en symfony
        if(!empty($rolesAsignados)):
            $usuario->getUser()->removeRol($rolProgis[0]);
            $usuario->getUser()->addRol($rolProgis[0]);
        elseif(empty($rolesAsignados)):
            $usuario->getUser()->removeRol($rolProgis[0]);
        endif;
        
        $em->flush();*/
    }
    
    //otros usuarios que se encuentran en el nivel del usuario actual
    public function nivelusuarios($idnivelusuario) {
        $em = $this->em;
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.id in (:idnivel) and u.isActive=true and me.activo=true  order by p.primerNombre, p.primerApellido ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idnivelusuario);
        $a=$query->getResult();
        
        if(empty($a))$a==null;
        return $a;       
    }
    
    //busco que id de nivel organizacional tengo en los espacios que he sido agregado INLUYENDO EL ACTUAL
    public function idNivelMiembroEspacio($perfil) {
        $em = $this->em;
        
        $idnivel=null;
        
        //obtengo los roles del usuario actual
        $roles=$perfil->getUser()->getRol();
        
        foreach ($roles as $r) {
            if($r->getRol()=='ROLE_PROGIS_ADMIN')
                $idnivel[]=$perfil->getNivelorganizacional()->getId();
        }
        $miembroEspacio =  $em->getRepository('PrincipalBundle:Miembroespacio')->findByUsuario($perfil->getId());
        foreach ($miembroEspacio as $me) {
            if($idnivel!=$me->getNivelorganizacional()->getId())
                $idnivel[]=$me->getNivelorganizacional()->getId();
        }
        
        return $idnivel;
    }
    
    //esta consulta se muestra en la lista de miembros
    public function miembrosMiEspacio($perfil) {

        $em = $this->em;

        //busco a todas las personas que son miembros y tienen esos mismos niveles        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.id in (:idnivel) and u.isActive=true order by p.primerNombre, p.primerApellido ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$perfil->getNivelorganizacional());
        $miembroEspacio=$query->getResult();
        return $miembroEspacio;       
    }
    
    
}