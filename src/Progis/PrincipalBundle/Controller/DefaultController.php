<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progis\ProyectoBundle\Entity\Miembroproyecto;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    

    public function calendarioAction()
    {
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $proyectoModelo = $this->get('proyectoModelo');
        $funcionModelo = $this->get('funcionModelo');
        $session = $this->getRequest()->getSession();
        $proyecto=$proyectoModelo->listaProyecto($idusuario,$funcionModelo,$session,$this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'));


        $jsonProyecto=array();
        
        foreach ($proyecto as $p) {
            
            if($p->getFechainicioestimada()!=null):
                $fie=$p->getFechainicioestimada()->format("Y-m-d");
                $fie=strtotime($fie)*1000;
                $jsonProyecto[]=array('id'=>$p->getId(),'title'=>'Fecha estimada de inicio del proyecto: '.$p->getNombre(),'url'=>'http://aplicaciones.telesurtv.net/sait/web/app.php/progis/proyecto/'.$p->getId().'/consultaProyecto','class'=>'event-info','start'=>"$fie",'end'=>"$fie");
            endif;
        }
        
        foreach ($proyecto as $p) {
            
            if($p->getFechafinestimada()!=null):
                $fie=$p->getFechafinestimada()->format("Y-m-d");
                $fie=strtotime($fie)*1000;
                $jsonProyecto[]=array('id'=>$p->getId(),'title'=>'Fecha estimada de fin del proyecto: '.$p->getNombre(),'url'=>'http://aplicaciones.telesurtv.net/sait/web/app.php/progis/proyecto/'.$p->getId().'/consultaProyecto','class'=>'event-success','start'=>"$fie",'end'=>"$fie");
            endif;
        }
        
        
        $jsonProyecto=  json_encode($jsonProyecto);
        
        return $this->render('PrincipalBundle:Default:calendario.html.twig',array('jsonProyecto'=>$jsonProyecto));
    }
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $idnivelusuario= $this->idnivelusuario();
        
        //listado de proyectos de la unidad
        $dql = "select x from PrincipalBundle:Bitacora x join x.nivelorganizacional no where no.id in (:idnivel) order by x.fechahora DESC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idnivelusuario);
        $bitacora = $query->getResult();
        
        return $this->render('PrincipalBundle:Default:index.html.twig',array('bitacora'=>$bitacora));
    }
    
    public function tutorialAction()
    {
        //$em = $this->getDoctrine()->getManager();
        
        /*$proyectoModelo=$this->get("proyectoModelo");    
        $pendientes=$proyectoModelo->pendientes($perfil);
        $html=$this->renderView('PrincipalBundle:Correo:pendientes.html.twig',array('pendientes'=>$pendientes));
        
        echo $html;
        die;*/

        
                
        /*$em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $admin=false;if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $seguridad=$this->get("seguridadModelo");
        $seguridad->initSession($perfil,$this->getRequest()->getSession(),$admin);*/
        return $this->render('PrincipalBundle:Default:tutorial.html.twig');
    }
}
