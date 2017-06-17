<?php

namespace Progis\PrincipalBundle\Modelo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class funcionModelo extends Controller
{
    function __construct($em) {
        $this->em = $em;
    }
    
    //otros usuarios que se encuentran en el nivel del usuario actual
   /* public function nivelusuarios($idnivelusuario) {
        $em = $this->em;
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.id in (:idnivel) and u.isActive=true and me.activo=true  order by p.primerNombre, p.primerApellido ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idnivelusuario);
        $a=$query->getResult();
        
        if(empty($a))$a==null;
        return $a;       
    }*/
    
    //busco que id de nivel organizacional tengo en los espacios que he sido agregado INLUYENDO EL ACTUAL
    public function idNivelMiembroEspacio($perfil) {
        $em = $this->em;
        
        $idnivel=null;
        
        //obtengo los roles del usuario actual
        /*$roles=$perfil->getUser()->getRol();
        
        foreach ($roles as $r) {
            if($r->getRol()=='ROLE_PROGIS_ADMIN')
                $idnivel[]=$perfil->getNivelorganizacional()->getId();
        }*/
        
        $dql = "select me from PrincipalBundle:Miembroespacio me where me.usuario= :idperfil and me.activo=true";
        $query = $em->createQuery($dql);
        $query->setParameter('idperfil',$perfil->getId());
        $miembroEspacio=$query->getResult();

        foreach ($miembroEspacio as $me) {
            if($idnivel!=$me->getNivelorganizacional()->getId())
                $idnivel[]=$me->getNivelorganizacional()->getId();
        }
        
        return $idnivel;
    }
    
    //esta consulta se muestra en la lista de miembros de mi nivel
    public function miembrosMiEspacio($perfil) {

        $em = $this->em;

        //busco a todas las personas que son miembros y tienen esos mismos niveles        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.id in (:idnivel) and u.isActive=true order by p.id ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$perfil->getNivelorganizacional());
        $miembroEspacio=$query->getResult();
        return $miembroEspacio;       
    }
    
    //esta consulta se muestra en la lista de miembros y todos los que pertenecesn a las unidades que estan por debajo
    public function miembrosMiEspacioEscalera($perfil) {

        $em = $this->em;

        //busco a todas las personas que son miembros y tienen esos mismos niveles        
        $dql = "select me from PrincipalBundle:Miembroespacio me join me.nivelorganizacional no join me.usuario p join p.user u where no.codigo like :codNivel and u.isActive=true and me.activo=true and me.mostrarEnReporte=true order by p.nivelorganizacional DESC";
        $query = $em->createQuery($dql);
        $query->setParameter('codNivel',"%".$perfil->getNivelorganizacional()->getCodigo()."%");
        $miembroEspacio=$query->getResult();
        return $miembroEspacio;       
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
        
    }
    
    public function correo($subject,$de,$para,$cuerpo) {
        
            $message = \Swift_Message::newInstance() 
            ->setSubject($subject) 
            ->setFrom($de.'@telesurtv.net')   
            ->setTo($para)   
            ->setBody($cuerpo, 'text/html');
            return $message;
            
        
    }
}