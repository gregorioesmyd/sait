<?php

namespace Progis\ReporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\ReporteBundle\Entity\estadoProceso;
use Progis\ReporteBundle\Form\estadoProcesoType;

use Symfony\Component\HttpFoundation\Request;

class EstadoProcesoController extends Controller
{
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolReporte($session,$admin);
    }
    
    private function createCreateFormEstadoProceso(estadoProceso $entity,$idNivel)
    {
        $form = $this->createForm(new estadoProcesoType($idNivel), $entity, array(
            'action' => $this->generateUrl('reporte_procesarEstadoProceso',array('idnivel'=>$idNivel)),
            'method' => 'POST',
        ));
        return $form;
    }
    
    public function estadoProcesoInicioAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $dql = "select x from UsuarioBundle:Nivelorganizacional x where x.codigo like :codNiv";
        $query = $em->createQuery($dql);
        $query->setParameter("codNiv",$perfil->getNivelorganizacional()->getCodigo()."%");
        $niveles = $query->getResult();
        
        $dql = "select x from UsuarioBundle:Perfil x join x.cargo c where x.nivelorganizacional in (:nivel) and x.jefe=true and c.descripcion like '%JEFE%' or  c.descripcion like '%DIRECTOR%'";
        $query = $em->createQuery($dql);
        $query->setParameter("nivel",$niveles);
        $perfil = $query->getResult();
        
        return $this->render('ReporteBundle:EstadoProceso:inicio.html.twig', array('niveles'   => $niveles,'perfil'=>$perfil));
    }
    
    
    public function estadoProcesoAction($idnivel)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new estadoProceso();
        $form   = $this->createCreateFormEstadoProceso($entity,$idnivel);
        
        return $this->render('ReporteBundle:EstadoProceso:form.html.twig', array('form'   => $form->createView()));
    }
    
    public function procesarEstadoProcesoAction(Request $request,$idnivel)
    {
        $this->seguridad();
        $muestra=$request->get("muestra");

        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new estadoProceso();
        $form   = $this->createCreateFormEstadoProceso($entity,$idnivel);
        
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            $data=$form->getData();

            $estadoProcesoModelo=  $this->get("estadoProcesoModelo");
            $consulta=$estadoProcesoModelo->consulta($data,$perfil->getNivelorganizacional());

            if($muestra==0)
                return $this->render('ReporteBundle:EstadoProceso:vista.html.twig', array('data'=>$data,'form'   => $form->createView(),'consulta'=>$consulta,'miembroEspacio'=>$data->getMiembroespacio(),'perfil'=>$perfil,'form'   => $form->createView(),'idnivel'=>$idnivel));
            else
                $html= $this->render('ReporteBundle:EstadoProceso:reporte.html.twig', array('data'=>$data,'form'   => $form->createView(),'consulta'=>$consulta,'miembroEspacio'=>$data->getMiembroespacio(),'perfil'=>$perfil));
            
       }else return $this->render('ReporteBundle:EstadoProceso:form.html.twig', array('form'   => $form->createView()));
        
        //echo $html->getContent();
        //die;
        include("libs/MPDF/mpdf.php");
        $mpdf=new \mPDF();
        //izq - der - arr - aba
        //$mpdf->AddPage('P','','','','',10,10,0,0);
        $mpdf->AddPage('P','','','','',10,25,10,25,18,12);
        $stylesheet = file_get_contents('libs/bootstrap3/css/bootstrap.min.css');
        $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text

        $mpdf->WriteHTML($html->getContent());
        $mpdf->Output("estadoProy".".pdf","D");
        exit;

    }
}
