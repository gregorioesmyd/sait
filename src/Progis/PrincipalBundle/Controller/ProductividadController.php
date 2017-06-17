<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductividadController extends Controller
{
    
    public function indexAction() {
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $admin=false;if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $seguridad=$this->get("seguridadModelo");
        $seguridad->initSession($perfil,$this->getRequest()->getSession(),$admin);
        
        
        //para obtener los usuarios del espacio de trabajo
        $funcionModelo = $this->get('funcionModelo');
        $miembroespacio=$funcionModelo->miembrosMiEspacioEscalera($perfil);
        //fin

        $usuarios=array();$ids=array();
        foreach ($miembroespacio as $m) {
            $usuarios[]=$m->getUsuario();
        }
        $usuarios=array_unique($usuarios);
        
        //consulto cantidades y tiempos
        $fechaDesde="01-".\date("m")."-".\date("Y");
        $dias=cal_days_in_month(CAL_GREGORIAN, \date("m"), \date("Y"));
        $fechaHasta=$dias."-".\date("m")."-".\date("Y");
        
        $productividadModelo=  $this->get("productividadModelo");
        $productividad=$productividadModelo->productividad($usuarios,$fechaDesde,$fechaHasta);
        
        $meses=array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');

        return $this->render('PrincipalBundle:Productividad:index.html.twig', array(
            'usuarios' => $usuarios,
            'perfil'=>$perfil,
            'meses'=>$meses,
            'productividad'=>$productividad

        ));
        
        die;
    }
}
