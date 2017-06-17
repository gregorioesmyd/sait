<?php
namespace Frontend\SitBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Frontend\SitBundle\Resources\Misclases\htmlreporte;

use Administracion\UsuarioBundle\Resources\Misclases\Funcion;

use Frontend\SitBundle\Entity\Reporte\Estadistica;
use Frontend\SitBundle\Form\Reporte\EstadisticaType;


/**
 * Categoria controller.
 *
 */
class ReporteController extends Controller
{

    /**
     * Lists all Categoria entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $unidad = $em->getRepository('SitBundle:Unidad')->findAll();

        $array['s']="Seleccione";
        foreach ($unidad as $value) {
            $array[$value->getId()]=$value->getDescripcion();
        }
        $form = $this->createFormBuilder()
            ->add('unidad', 'choice', array(
                'choices'   => $array,
            ))->getForm();
        
        return $this->render('SitBundle:Reporte:formreporte.html.twig',array('form'=>$form->createView()));

    }


    public function generarimagenesAction(Request $request)
    {
        $datos=$request->request->all();
        $datos=$datos['form'];

        $mes= array('s'=>'seleccione','01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');

        $em = $this->getDoctrine()->getManager();

        $fechadesde="01-".$datos['meses']."-".$datos['anios'];
        $dias=cal_days_in_month(CAL_GREGORIAN, $datos['meses'], $datos['anios']);
        $fechahasta=$dias."-".$datos['meses']."-".$datos['anios'];

        $dql = "select t from SitBundle:Ticket t join t.categoria c where t.unidad= :idunidad and (t.estatus=4 or t.estatus = 6 or t.estatus = 5) and t.fechasolicitud BETWEEN ?1 AND ?2 order by t.categoria,t.subcategoria,t.fechasolicitud, t.horasolicitud ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idunidad',$datos['unidad']);
        $query->setParameter(1, $fechadesde);
        $query->setParameter(2, $fechahasta);
        $ticket = $query->getResult();

        if(empty($ticket)){
            $this->get('session')->getFlashBag()->add('alert', 'No existen datos para la fecha seleccionada');
            return $this->redirect($this->generateUrl('reporte'));
        }


        foreach ($ticket as $value) {
            $categorias[$value->getCategoria()->getCategoria()]=0;
        }

        foreach ($ticket as $value) {
            $categorias[$value->getCategoria()->getCategoria()]=$categorias[$value->getCategoria()->getCategoria()]+1;
        }

        $grafico="";
        foreach ($categorias as $key => $value) {

            $grafico .="['".$key."',".$value."],";

        }
        $grafico = substr($grafico, 0, -1);


        return $this->render('SitBundle:Reporte:imagenesinforme.html.twig',array('datos'=>$datos,'datosgrafico'=>$grafico,'mes'=>$mes));


    }
    public function generarinformeAction(Request $request)
    {
        header('Content-type: application/vnd.ms-word');
        header("Content-Disposition: attachment; filename=informe.doc");
        header("Pragma: no-cache");
        header("Expires: 0");

        $datos=$request->request->all();
        $datos=$datos['form'];

        //equivalencia con el nivel organizacional
        $idunidad=$datos['unidad'];
        if($idunidad==1)$idunidad=53;
        else if($idunidad==2)$idunidad=50;
        else if($idunidad==3)$idunidad=52;
        else if($idunidad==4)$idunidad=51;

        $em = $this->getDoctrine()->getManager();
        //cuento las tareas del proyecto
        $dql = "select x from ProyectoBundle:Objetivo x join x.proyecto p join p.nivelorganizacional no  where no.id= :idnivel and p.estatus!=3 order by p.id asc";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idunidad);
        $tarea = $query->getResult();

        $em = $this->getDoctrine()->getManager();
        $a=new htmlreporte;
        $html=$a->informe($em,$datos,$tarea);
        if($html==null){
            $this->get('session')->getFlashBag()->add('alert', 'No existen datos para la fecha seleccionada');
            return $this->redirect($this->generateUrl('reporte'));
        }

        echo $html;
        die;

    }
    
    
    public function reporteestadisticaAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        foreach ($usuariounidad as $value) {
            $usuarios[$value->getId()]=$value->getPrimerNombre()." ".$value->getPrimerApellido();
        }
        
        $entity = new Estadistica();
        $form   = $this->createFormEstadistica($entity,$usuarios);

        return $this->render('SitBundle:Reporte:reporteestadistica.html.twig', array(
            'form'=>$form->createView(),
        ));
    }
    
    public function generareporteestadisticaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $idusuario = $this->get('security.context')->getToken()->getUser()->getId();
        $f=new Funcion; 
        $usuariounidad=$this->usuariounidad= $f->Usuariounidad($em,$idusuario);
        
        foreach ($usuariounidad as $value) {
            $usuarios[$value->getId()]=$value->getPrimerNombre()." ".$value->getPrimerApellido();
        }
        
        $entity = new Estadistica();
        $form   = $this->createFormEstadistica($entity,$usuarios);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
   
            $datos=$form->getData();


            $modelo = $this->get('Estadistica_Modelo');
            $consulta = $modelo->consulta($datos);

            $datografico=null;$muestra=0;
            foreach ($consulta as $key => $value) {
                if($value>0)$muestra=1;
                $datografico .="['".$key."',".$value."],";
            }
            $datografico=  substr($datografico, 0,-1);
            
        

            return $this->render('SitBundle:Reporte:estadistica.html.twig', array(
                'consulta' => $consulta,
                'datos' => $datos,
                'datografico'=>$datografico,
                'muestra'=>$muestra
                
            ));
            
            /*$html=$this->render('SitBundle:Reporte:estadistica.html.twig', array(
                'consulta' => $consulta,
                'datos' => $datos,
                'datografico'=>$datografico
                
            ));*/
            
            
            print_r($html->getContent());
            die;
            
            //GENERO EL PDF
            include("libs/MPDF/mpdf.php");
            $mpdf=new \mPDF();
            //izq - der - arr - aba
            //$mpdf->AddPage('P','','','','',10,10,0,0);
            $mpdf->AddPage('P','','','','',10,25,10,25,18,12);
            $stylesheet = file_get_contents('bundles/sit/css/reporte/estadistica.css');
            $mpdf->WriteHTML($stylesheet,1);    // The parameter 1 tells that this is css/style only and no body/html/text

            $mpdf->WriteHTML($html->getContent());
            $mpdf->Output("reporte".".pdf","D");
            exit;

            
            die;
            
        }
        
        return $this->render('SitBundle:Reporte:reporteestadistica.html.twig', array(
            'form'=>$form->createView(),
        ));
        
    }
    
    private function createFormEstadistica(Estadistica $entity,$usuariounidad)
    {
        $form = $this->createForm(new EstadisticaType($usuariounidad), $entity, array(
            'action' => $this->generateUrl('reporte_generaestadistica'),
            'method' => 'POST',
        ));

        return $form;
    }
}