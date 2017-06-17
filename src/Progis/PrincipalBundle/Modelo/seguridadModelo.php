<?php

namespace Progis\PrincipalBundle\Modelo;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class seguridadModelo
{
    
    function __construct($em) {
        $this->em = $em;
        $this->rolGeneral=array();
        $this->rolGeneralNivel=array();
        $this->rolProyecto=array();
        $this->rolProyectoGeneral=array();
    }
    
    //VALIDACIONES DE ROLES GENERALES PARA VERIFICAR QUE EL USUARIO CONTENGA AL MENOS UNO DE ESTOS ROLES
    
    //acceso a las definiciones
    public function validaRolDefiniciones($session,$admin) {
        $rolGeneral=$session->get('rolGeneral');
        
        if(
               $rolGeneral['ROLE_PROGIS_DEFINICIONES']==true 
            or $rolGeneral['ROLE_PROGIS_SUPERVISOR']==true 
            or $admin==true
        )
            return;
        else 
            throw new AccessDeniedException();
    }
    
    //acceso a las definiciones
    public function validaRolReporte($session,$admin) {
        $rolGeneral=$session->get('rolGeneral');
        if(
               $rolGeneral['ROLE_PROGIS_REPORTE']==true 
            or $rolGeneral['ROLE_PROGIS_SUPERVISOR']==true 
            or $admin==true
        )
            return;
        else 
            throw new AccessDeniedException();
    }
    
    //cualquier rol de ticket
    public function validaRolTicket($session,$admin) {
        $rolGeneral=$session->get('rolGeneral');
        
        if(
               $rolGeneral['ROLE_PROGIS_TICKET_ADMIN']==true 
            or $rolGeneral['ROLE_PROGIS_TICKET_CONSULTA']==true 
            or $rolGeneral['ROLE_PROGIS_TICKET_TECNICO']==true 
            or $rolGeneral['ROLE_PROGIS_SUPERVISOR']==true     
            or $admin==true
        )
            return;
        else 
            throw new AccessDeniedException();
    }
    
    //cualquier rol de proyecto
    public function validaRolProyecto($session,$admin) {
        $rolGeneral=$session->get('rolGeneral');
        $rolProyectoGeneral=$session->get('rolProyectoGeneral');
        if(
               $rolGeneral['ROLE_PROGIS_PROYECTO_ADMIN']==true 
            or $rolGeneral['ROLE_PROGIS_PROYECTO_CONSULTA']==true 
            or $rolGeneral['ROLE_PROGIS_PROYECTO_TECNICO']==true 
            or $rolGeneral['ROLE_PROGIS_SUPERVISOR']==true     
            or $rolProyectoGeneral['ROLE_PROGIS_PROYECTO_ESP_ADMIN']==true 
            or $rolProyectoGeneral['ROLE_PROGIS_PROYECTO_ESP_CONSULTA']==true 
            or $rolProyectoGeneral['ROLE_PROGIS_PROYECTO_ESP_TECNICO']==true  
            or $admin==true
        )
            return;
        else 
            throw new AccessDeniedException();
    }
    
    //FIN
    

    public function initSession($perfil,$session,$admin){
        $em=  $this->em;
        
        $dql = "select me from PrincipalBundle:Miembroespacio me where me.usuario= :idperfil and me.activo=true";
        $query = $em->createQuery($dql);
        $query->setParameter('idperfil',$perfil->getId());
        $miembroEspacio=$query->getResult();
        
        $rolGeneral = $em->getRepository('PrincipalBundle:Rolgeneral')->findAll();
        $rolProyecto = $em->getRepository('ProyectoBundle:Rolproyecto')->findAll();
        
        //valido si es admin y no esta agregado como miembro se su propia unidad inicializo los roles generales del nivel en false
        //para que no me de error cada vez que pregunte en el resto de la aplicacion
        if($admin==true):
            $miembroMiPropioEspacio = $em->getRepository('PrincipalBundle:Miembroespacio')->findBy(array('nivelorganizacional'=>$perfil->getNivelorganizacional(),'usuario'=>$perfil));
            if(empty($miembroMiPropioEspacio)):
                foreach ($rolGeneral as $r){
                    $this->rolGeneralNivel[$perfil->getNivelorganizacional()->getId()][$r->getRol()]=false;
                } 
            endif;
        endif;
        
        //inicializo roles generales
        foreach ($rolGeneral as $r){
            $this->rolGeneral[$r->getRol()]=false;
        }
        foreach ($rolProyecto as $r){
            $this->rolProyectoGeneral[$r->getRol()]=false;
        }
        
        //inicializo especificos
        foreach ($miembroEspacio as $me) {

            $miembroProyecto = $em->getRepository('ProyectoBundle:Miembroproyecto')->findByMiembroespacio($me);
            
            foreach ($rolGeneral as $r){
                $this->rolGeneralNivel[$me->getNivelorganizacional()->getId()][$r->getRol()]=false;
            }
            
            foreach ($miembroProyecto as $mp){
                foreach ($rolProyecto as $r){
                    $this->rolProyecto[$mp->getProyecto()->getId()][$r->getRol()]=false;    
                }
            } 
        }
        //fin roles especiales y especificos
        
        
        //coloco true si los roles realmente estan asignados al usuario
        foreach ($miembroEspacio as $me) {
            $miembroProyecto = $em->getRepository('ProyectoBundle:Miembroproyecto')->findByMiembroespacio($me);
            
           foreach ($me->getRolgeneral() as $r){
                $this->rolGeneral[$r->getRol()]=true;
                $this->rolGeneralNivel[$me->getNivelorganizacional()->getId()][$r->getRol()]=true;
            }
            foreach ($miembroProyecto as $mp){
                $this->rolProyecto[$mp->getProyecto()->getId()][$mp->getRolproyecto()->getRol()]=true;
                $this->rolProyectoGeneral[$mp->getRolproyecto()->getRol()]=true;
            }
        }
        

        $session->set('nomape',$perfil->getPrimerNombre().' '.$perfil->getPrimerApellido());
        $session->set('cedula',$perfil->getCedula());
        $session->set('nivel',$perfil->getNivelorganizacional()->getDescripcion());
        $session->set('idnivel',$perfil->getNivelorganizacional()->getId());
        $session->set('foto',$perfil->getFoto());
        $session->set('rolGeneral',$this->rolGeneral);
        $session->set('rolGeneralNivel',$this->rolGeneralNivel);
        $session->set('rolProyecto',$this->rolProyecto);
        $session->set('rolProyectoGeneral',$this->rolProyectoGeneral);
    }
}