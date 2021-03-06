<?php

namespace Frontend\NominaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Administracion\UsuarioBundle\Resources\Misclases\Conexion;
use Symfony\Component\HttpFoundation\Request;

use Frontend\NominaBundle\Entity\Reporte\Historico;
use Frontend\NominaBundle\Form\Reporte\HistoricoType;

class DefaultController extends Controller
{
    public function indexAction()
    {

        return $this->render('NominaBundle:Default:index.html.twig');
    }

    public function formalimentacionAction(){

    	$tn=array('0'=>'Seleccione...', '21'=>'Trabajadores e','25'=>'Trabajadores o','14'=>'Trabajadores an','24'=>'Contratados','32'=>'Comisión de servicio','64'=>'Contratados-Multimedia','65'=>'Contratados-Imagen y Promoción','31'=>'Convenios Internacionales','41'=>'Pensionados','91'=>'Jubilados','73'=>'Empleados Multimedia','74'=>'Empelados Imagen y Promociones');
    	$anio=array('0'=>'Seleccione...', date("Y")-2=>date("Y")-2,date("Y")-1=>date("Y")-1,date("Y")=>date("Y"));
    	$mes=array('0'=>'Seleccione...', '1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre');
		$form = $this->createFormBuilder()
		        ->add('tiponomina', 'choice', array(
		            'choices'   => $tn))
		        ->add('anio', 'choice', array(
		            'choices'   => $anio))
		      	->add('mes', 'choice', array(
		            'choices'   => $mes))
		      ->getForm();

		return $this->render('NominaBundle:Default:formalimentacion.html.twig',array('form'=>$form->createView()));

    }


    public function txtalimentacionAction(Request $request){

        //RECIBO LOS DATOS DEL FORMULARIO
        $datos=$request->request->all();
        $datos=$datos['form'];

		$tn=array('0'=>'Seleccione...', '21'=>'Trabajadores e','25'=>'Trabajadores o','14'=>'Trabajadores an','24'=>'Contratados','32'=>'Comisión de servicio','64'=>'Contratados-Multimedia','65'=>'Contratadados-Imagen y Promoción','31'=>'Convenios internacionales','41'=>'Pensionados','91'=>'Jubilados');

		$a=new Conexion;
		$postgresql_sigefirrhh=$a->postgresql_sigefirrhh();

		$tipo='OBR';
		$query="
		select p.nacionalidad, p.cedula, sum(hq.monto_asigna)-sum(hq.monto_deduce) as neto 
		from nominaespecial ne, historicoquincena hq, trabajador t, personal p
		where 
		ne.id_nomina_especial=hq.id_nomina_especial and
		ne.id_frecuencia_pago=22 and ne.anio=".$datos['anio']." and ne.mes=".$datos['mes']." and
		hq.id_trabajador=t.id_trabajador and p.id_personal=t.id_personal and hq.id_tipo_personal=".$datos['tiponomina']."
		group by p.nacionalidad, p.cedula
		";

		$rs = pg_query($postgresql_sigefirrhh,$query);

		$cadena='';
		$cont=0;
		$total_monto=0;
		while($row=pg_fetch_array($rs)){ 

			$nac= trim($row['nacionalidad']);
		    $ced= trim($row['cedula']);
		    $mon= trim($row['neto']);
		    
		    //agrego ceros faltantes a las cedulas
		    $faltante=0;
		    $ceros='';
		    $l=strlen($ced);
		    $faltante= 10 - $l;
		    if($faltante!=0){
		        for($i=0;$i<$faltante;$i++){
		            $ceros .= '0';
		        }
		                    
		        $cedula = $ceros.$ced;
		    }
		    else $cedula = $ced;
			//		    


		    // agrego ceros faltantes al monto
		    $faltante=0;
		    $ceros='';
		    $mon = str_replace(",", ".", $mon);

			//monto total de todos los trabajadores
		    $total_monto=$mon+$total_monto;

		    $monto = number_format($mon, 2, "", ""); 
		    $l=strlen($monto);
		                
		    $faltante= 15 - $l;
	        for($i=0;$i<$faltante;$i++){
		        $ceros .= '0';
		    }

		    $monto = $ceros.$monto;
		    //
		      
		    //armo las lineas  
		    $cadena .= $nac.$cedula.$monto."\r\n";
		    
		    $cont++;
		}                   

		if($cont==0){
            $this->get('session')->getFlashBag()->add('alert', 'No existen datos para los parámetros seleccionados.');
            return $this->redirect($this->generateUrl('nomina_formalimentacion'));
        }

		//ENCABEZADO

		//total trabajadores
	    $faltante=0;
	    $ceros='';
		$l=strlen($cont);

		$faltante= 6 - $l;
		if($faltante!=0){
		    for($i=0;$i<$faltante;$i++){
		        $ceros .= '0';
		    }
		    $empleados = $ceros.$cont;
		}
		//

		//total MONTO
	    $faltante=0;
	    $ceros='';
		$total_monto = number_format($total_monto, 2, "", "");
		$l=strlen($total_monto);
		$faltante= 15 - $l;
		if($faltante!=0){
		    for($i=0;$i<$faltante;$i++){
		        $ceros .= '0';
		    }
		    $total = $ceros.$total_monto;
		}
		   
		$encabezado="ATMCCBDE900061".$empleados.$total.date("Ymd")."\r\n";

		$cadena_final =$encabezado.$cadena;

        header("Content-type: application/octet-stream");
        //indicamos al navegador que se está devolviendo un archivo
        header("Content-Disposition: attachment; filename=".$tn[$datos['tiponomina']].".txt");
        //con esto evitamos que el navegador lo grabe en su caché
        header("Pragma: no-cache");
        header("Expires: 0");
        //damos salida a la tabla
        echo $cadena_final;
        die;

    }
    
    public function formhistoricoAction(){
        
        $entity = new Historico();
        $form   = $this->createFormHistorico($entity);
        return $this->render('NominaBundle:Historico:formhistorico.html.twig',array('form'=>$form->createView()));
        
    }
    
    private function createFormHistorico(\Frontend\NominaBundle\Entity\Reporte\Historico $entity)
    {
        $form = $this->createForm(new HistoricoType(), $entity, array(
            'action' => $this->generateUrl('nomina_generaexcelhistorico'),
            'method' => 'POST',
        ));

        return $form;
    }
    
    public function generaexcelhistoricoAction(Request $request){

        
        $entity = new Historico();
        $form   = $this->createFormHistorico($entity);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $datos=$form->getData();
            
            
            $modelo = $this->get('Historico_Modelo');
            $consultainfo = $modelo->consulta($datos,'infocent');
            $consultasige = $modelo->consulta($datos,'sigefirrhh');
            $consultadata = $modelo->consulta($datos,'datapro');
            
            if(empty($consultainfo) && empty($consultasige) && empty($consultadata)){
                $this->get('session')->getFlashBag()->add('alert', 'No existen datos para los parámetros seleccionados.');
                return $this->redirect($this->generateUrl('nomina_formhistorico'));
            }

            $html= $this->render('NominaBundle:Reporte:Historico.html.twig',array('consultainfo'=>$consultainfo,'consultasige'=>$consultasige,'consultadata'=>$consultadata));
            
            header("Content-type: application/octet-stream");
            //indicamos al navegador que se está devolviendo un archivo
            header("Content-Disposition: attachment; filename=Historico-".$datos->getCedula().".xls");
            //con esto evitamos que el navegador lo grabe en su caché
            header("Pragma: no-cache");
            header("Expires: 0");

            echo $html->getContent();
            die;
            
            
        } else $this->get('session')->getFlashBag()->add('alert', '<b>Alerta!</b> Ha ocurrido un error en el formulario.');
        
        return $this->render('NominaBundle:Historico:formhistorico.html.twig',array('form'=>$form->createView()));
        
    }

}
