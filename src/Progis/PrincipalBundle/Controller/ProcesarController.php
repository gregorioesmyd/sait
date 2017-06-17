<?php

namespace Progis\PrincipalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcesarController extends Controller
{
    public function procesarUbicacionAction(Request $request,$ubicacion)
    {
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        
        $ubicacionModelo = $this->get('ubicacionModelo');
        $bitacoraModelo = $this->get('bitacoraModelo');
        $proyectoModelo = $this->get('proyectoModelo');
        $objetivoModelo = $this->get('objetivoModelo');
        $trabajoRealizadoModelo = $this->get('trabajoRealizadoModelo');
        $metaModelo = $this->get('metaModelo');

        //pregunto si el usuario es administrador
        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN') or $this->get('security.context')->isGranted('ROLE_PROGIS_HELPDESK')) $admin=true;
        
        
        
        
        $respuesta=$ubicacionModelo->ubicacion($request->getContent(),$ubicacion,$idusuario,$objetivoModelo,$bitacoraModelo,$admin,$proyectoModelo,$metaModelo,$trabajoRealizadoModelo);
        
        return new JsonResponse($respuesta);
    }
    
    public function procesarUbicacionPriometaAction(Request $request,$ubicacion)
    {
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        
        $ubicacionModelo = $this->get('ubicacionModelo');

        $respuesta=$ubicacionModelo->ubicacionPriometa($request->getContent(),$ubicacion,$idusuario);
        
        return new JsonResponse($respuesta);
    }
    
    public function procesarOrdenAction(Request $request)
    {   
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $ordenModelo = $this->get('ordenModelo');
        $respuesta=$ordenModelo->orden($request->getContent());
        
        return new JsonResponse($respuesta);
    }
    
    public function procesarOrdenPriometaAction(Request $request)
    {   
        $ordenModelo = $this->get('ordenModelo');
        $respuesta=$ordenModelo->ordenPriometa($request->getContent());
        
        return new JsonResponse($respuesta);
    }
    
    public function procesarValidarAction(Request $request)
    {   
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $procesaModelo = $this->get('procesaModelo');
        
        $recibe=explode("||",$request->getContent());
        $bundleActual=$recibe[0];
        $ubi=$recibe[1];
        $idtarjeta=$recibe[2];

        
        if($ubi=='culminado')
            $respuesta=$procesaModelo->validaCulminado($bundleActual,$ubi,$idtarjeta,$idusuario);
        elseif($ubi=='dependencia')
            $respuesta=$procesaModelo->validaDependencia($bundleActual,$ubi,$idtarjeta,$idusuario);
        
        
        return new JsonResponse($respuesta);
    }
    
    public function verificaSessionAction()
    {
        $session=$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY');
        $respuesta['session']=$session;
        
        return new JsonResponse($respuesta);
    }
    
}
