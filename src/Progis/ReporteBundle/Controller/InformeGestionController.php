<?php

namespace Progis\ReporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Progis\ReporteBundle\Entity\informeGestion;
use Progis\ReporteBundle\Form\informeGestionType;

use Symfony\Component\HttpFoundation\Request;

class InformeGestionController extends Controller
{
    public function seguridad() {

        $admin=false;
        if($this->get('security.context')->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;
        
        $session = $this->getRequest()->getSession();
        $seguridad=$this->get('seguridadModelo');
        $seguridad->validaRolReporte($session,$admin);
    }

    private function createCreateFormInformeGestion(informeGestion $entity)
    {
        $form = $this->createForm(new informeGestionType(), $entity, array(
            'action' => $this->generateUrl('reporte_imagenInformeGestion'),
            'method' => 'POST',
        ));
        return $form;
    }
    
    public function informeGestionAction()
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new informeGestion();
        $form   = $this->createCreateFormInformeGestion($entity);
        
        return $this->render('ReporteBundle:InformeGestion:form.html.twig', array('form'   => $form->createView()));
    }
    
    public function imagenInformeGestionAction(Request $request)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
            
        $entity = new informeGestion();
        $form   = $this->createCreateFormInformeGestion($entity);
        
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data=$form->getData();
            
            $fechadesde="01-".$data->getMes()."-".$data->getAnio();
            $dias=cal_days_in_month(CAL_GREGORIAN, $data->getMes(), $data->getAnio());
            $fechahasta=$dias."-".$data->getMes()."-".$data->getAnio();
            
            //busco los tickets cerrados
            $dql = "select x from TicketBundle:ProcesarTicket x join x.ticket t join x.subcategoria sc where t.fechasolicitud >= :desde and t.fechasolicitud <= :hasta and t.nivelorganizacional= :nivel and t.estatus=4 order by sc.categoria ASC, t.fechasolicitud, t.horasolicitud ASC";
            $query = $em->createQuery($dql);
            $query->setParameter("nivel",$perfil->getNivelorganizacional());
            $query->setParameter("desde", $fechadesde);
            $query->setParameter("hasta", $fechahasta);
            $ticket = $query->getResult();

            //valido que la consulta traiga datos
            if(empty($ticket)){
                $this->get('session')->getFlashBag()->add('alert', 'No existen datos para la fecha seleccionada');
                return $this->redirect($this->generateUrl('reporte_informeGestion'));
            }

            //inicializo categoria en 0
            foreach ($ticket as $value) {
                $categorias[$value->getSubcategoria()->getCategoria()->getCategoria()]=0;
            }

            //cuento la cantidad de tickets por categorias
            foreach ($ticket as $value) {
                $categorias[$value->getSubcategoria()->getCategoria()->getCategoria()]=$categorias[$value->getSubcategoria()->getCategoria()->getCategoria()]+1;
            }

            //organizo datos para el grafico
            $grafico="";
            foreach ($categorias as $key => $value) {

                $grafico .="['".substr($key,0,70)."...',".$value."],";

            }
            $grafico = substr($grafico, 0, -1);
        
            
            $meses= array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
            return $this->render('ReporteBundle:InformeGestion:imagen.html.twig', array('meses' => $meses,'data'=>$data,'grafico'=>$grafico));
        }
        
        return $this->render('ReporteBundle:InformeGestion:form.html.twig', array('form'   => $form->createView()));
        
    }
    
    public function procesarInformeGestionAction(Request $request)
    {
        $this->seguridad();
        
        $em = $this->getDoctrine()->getManager();
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        
        $data=$request->request->all();
        $data=$data['form'];
        
        header('Content-type: application/vnd.ms-word');
        header("Content-Disposition: attachment; filename=".strtoupper(str_replace(" ","-",$perfil->getNivelorganizacional()->getDescripcion()))."-".$data['mes']."-".$data['anio'].".rtf");
        header("Pragma: no-cache");
        header("Expires: 0");

        $dql = "select x from ProyectoBundle:Objetivo x join x.proyecto p where p.nivelorganizacional= :nivel and p.informegestion=true order by p.nombre ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('nivel',$perfil->getNivelorganizacional());
        $objetivo = $query->getResult();

        $meses= array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
        
        $fechadesde="01-".$data['mes']."-".$data['anio'];
        $dias=cal_days_in_month(CAL_GREGORIAN, $data['mes'], $data['anio']);
        $fechahasta=$dias."-".$data['mes']."-".$data['anio'];

        //busco los tickets cerrados
        $dql = "select count(x) from TicketBundle:Ticket x where x.fechasolicitud >= :desde and x.fechasolicitud <= :hasta and x.nivelorganizacional= :nivel and x.estatus=4";
        $query = $em->createQuery($dql);
        $query->setParameter("nivel",$perfil->getNivelorganizacional());
        $query->setParameter("desde", $fechadesde);
        $query->setParameter("hasta", $fechahasta);
        $cantidadTicketAtendido = $query->getResult();
            
        $html= $this->render('ReporteBundle:InformeGestion:reporte.html.twig', array('objetivo'=>$objetivo,'perfil'=>$perfil,'meses'=>$meses,'data'=>$data,'cantidadTicketAtendido'=>$cantidadTicketAtendido[0][1]));
        echo $html->getContent();
        die;
    }
}

/*
        $html= $this->render('PrincipalBundle:TrabajoRealizado:miIndexPdf.html.twig',array('trabajoRealizado'=>$trabajoRealizado,'data'=>$data,'usuarios'=>$usuarios));
        
        include("libs/MPDF/mpdf.php");
        $mpdf=new \mPDF();
        //izq - der - arr - aba
        //$mpdf->AddPage('P','','','','',10,10,0,0);
        $mpdf->AddPage('P','','','','',10,10,10,25,18,12);
        //$stylesheet = file_get_contents('libs/bootstrap3/css/bootstrap.min.css');
        $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text

        $mpdf->WriteHTML($html->getContent());
        $mpdf->Output("estadoProy".".pdf","D");
        exit;
        die;
        echo $html->getContent();
        die;
 *  */