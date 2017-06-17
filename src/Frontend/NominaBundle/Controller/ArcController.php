<?php

namespace Frontend\NominaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Administracion\UsuarioBundle\Resources\Misclases\Conexion;
use Symfony\Component\HttpFoundation\Request;

class ArcController extends Controller
{
    public function indexAction()
    {
        $anios = array();
        $anioAnt = (int) date('Y') - 1;
        for ($i = 2013; $i <= $anioAnt; $i++) { 
            $anios[] = $i;
        }
        return $this->render('NominaBundle:ARC:index.html.twig', array('anios' => $anios));
    }

    public function generarAction(Request $request)
    {
        $anio = (int) $request->request->get('anio');
        /**
         * Validar que el parametro año recibido este dentro del rango valido
         */
        if ( $this->isValidate($anio) ) 
        {
            $out = $this->getContenido($anio);
        }


        $this->get('session')->getFlashBag()->set(
                'alert',
                array(
                    'title' => 'Error en el parametro año! Contacte al administrador.'
                )
            );
        return $this->redirect($this->generateUrl('nomina_planilla_arc'), 301);
    }

    /**
     * Funcion para validar que el año recibido
     * este dentro del rango valido
     * @param  [type]  $anio recibido por el formulario
     * @return boolean       true si el año es valido
     */
    public function isValidate($anio) 
    {
        $flag = false;
        $anioAnt = (int) date('Y') - 1;
        if($anio >= 2013 && $anio <= $anioAnt && checkdate(12, 31, $anio))
        {
            $flag = true;
        }
        return $flag;
    }

    public function getContenido($anio)
    {
        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $em        = $this->getDoctrine()->getManager();
        $entity    = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);

        /**
         * Obtener Datos Sigefirrhh
         */
        $a=new Conexion;
        $postgresql_sigefirrhh=$a->postgresql_sigefirrhh();
        $queryDetalle= $this->getQueryDetalle($anio, $entity->getCedula());
        $result = pg_query($postgresql_sigefirrhh,$queryDetalle);

        if (!is_null($result)) {
            while ($row_detalle = pg_fetch_assoc($result)) {
              $row_tmp_detalle [] = $row_detalle;
            }
        } 

        if (count($row_tmp_detalle) == 0) {
        	$this->get('session')->getFlashBag()->set(
                'alert',
                array(
                    'title' => 'No posee datos para el año' .$anio. '.'
                )
            );
        	return $this->redirect($this->generateUrl('nomina_planilla_arc'), 301);
        }

        $meses = array('enero','febrero','marzo','abril','mayo','junio','julio', 'agosto','septiembre','octubre','noviembre','diciembre');
        $nroRow = count($row_tmp_detalle);
        if ($nroRow > 1) {
            $detalle = array();
            foreach ($meses as $key => $mes) {
                $detalle["devengado_$mes"] = 0;
                $detalle["porcentaje_$mes"] = 0;
                $detalle["retencion_$mes"] = 0;
                foreach ($row_tmp_detalle as $key => $value) {
                    $detalle["devengado_$mes"] = $detalle["devengado_$mes"] + $value["devengado_$mes"];
                    $detalle["porcentaje_$mes"] = $detalle["porcentaje_$mes"] + $value["porcentaje_$mes"];
                    $detalle["retencion_$mes"] = $detalle["retencion_$mes"] + $value["retencion_$mes"];
                }
            }
            $detalle['anio'] = $row_tmp_detalle[0]['anio'];
            $detalle['cedula'] = $row_tmp_detalle[0]['cedula'];
            $detalle['primer_nombre'] = $row_tmp_detalle[0]['primer_nombre'];
            $detalle['primer_apellido'] = $row_tmp_detalle[0]['primer_apellido'];
            $detalle['segundo_apellido'] = $row_tmp_detalle[0]['segundo_apellido'];
            $detalle['segundo_nombre'] = $row_tmp_detalle[0]['segundo_nombre'];
            $detalle['numero_rif'] = $row_tmp_detalle[0]['numero_rif'];
        } else {
            $detalle = $row_tmp_detalle[0];
        }

        $queryAcum= $this->getQueryAcum($anio, $entity->getCedula());
        $result = pg_query($postgresql_sigefirrhh,$queryAcum);

        if (!is_null($result)) {
            while ($row_result = pg_fetch_assoc($result)) {
                $row_result_acum[$row_result['descon']] = $row_result['total'];
            }
        }

        /**
         * Renderizar Contenido HTML
         */
        
        $html=$this->render('NominaBundle:ARC:planilla.html.twig', array(
            'detalle' => $detalle,
            'acum' => $row_result_acum,
            'meses' => $meses
        ));

        /*print_r($html->getContent());
        die;*/
        
        /**
         * Generar PDF
         */
        include("libs/MPDF/mpdf.php");
        $mpdf=new \mPDF('utf-8', 'LETTER');
        $stylesheet = file_get_contents('css/reportes/planilla_arc/planilla.css');
        $mpdf->WriteHTML($stylesheet,1);
 
        $mpdf->WriteHTML($html->getContent());
        $mpdf->Output("reporte".".pdf","D");
        exit();
    }

    public function getQueryDetalle($anio, $cedula)
    {
        $query = "SELECT  devengado_enero, porcentaje_enero, retencion_enero,
                        devengado_febrero, porcentaje_febrero, retencion_febrero,      
                        devengado_marzo, porcentaje_marzo, retencion_marzo,
                        devengado_abril, porcentaje_abril, retencion_abril,
                        devengado_mayo, porcentaje_mayo, retencion_mayo,
                        devengado_junio, porcentaje_junio, retencion_junio,
                        devengado_julio, porcentaje_julio, retencion_julio,
                        devengado_agosto, porcentaje_agosto, retencion_agosto,
                        devengado_septiembre, porcentaje_septiembre, retencion_septiembre,
                        devengado_octubre, porcentaje_octubre, retencion_octubre,
                        devengado_noviembre, porcentaje_noviembre, retencion_noviembre,
                        devengado_diciembre, porcentaje_diciembre, retencion_diciembre,
                        anio, tr.cedula, pe.primer_apellido, pe.segundo_apellido, pe.primer_nombre,
                        pe.segundo_nombre as segundo_nombre, r.nombre as region, tr.id_trabajador, pe.numero_rif
                    
                    FROM planillaArc pa, trabajador tr, personal pe, dependencia d, sede s, 
                    region r, tipopersonal tp
                    
                    WHERE anio = ". $anio ."
                        AND tr.id_tipo_personal = tp.id_tipo_personal
                        AND pa.id_trabajador = tr.id_trabajador
                        AND tr.id_personal = pe.id_personal
                        AND tr.id_dependencia = d.id_dependencia
                        AND d.id_sede = s.id_sede
                        AND s.id_region = r.id_region
                        AND tr.cedula = ". $cedula ."

                    ORDER BY r.cod_region, tr.cedula";

        return $query;
    }


    public function getQueryAcum($anio, $cedula)
    {
        $query = "SELECT  max(tr.fecha_ingreso) as ingresoorganismo, 
                        max(e.cedula) as cedula, e.primer_nombre as primer_nombre, 
                        e.segundo_nombre as segundo_nombre,  e.primer_apellido as primer_apellido, 
                        e.segundo_apellido as segundo_apellido, j.nombre as nombreunidad, 
                        c.cod_concepto as codcon, c.descripcion as  descon, 
                        (sum(a.monto_asigna) + sum(a.monto_deduce)) as total       
                                      
                    FROM historicoquincena a,   historiconomina b, trabajador tr,concepto c, tipoPersonal d, 
                       personal e, conceptoTipoPersonal f, grupoNomina g, 
                       dependencia j, cargo k, frecuenciaTipoPersonal ftp, frecuenciaPago fp
                     
                    WHERE 
                      a.anio = ".$anio."
                      AND e.cedula = ".$cedula."
                      AND a.numero_nomina = 0
                      AND g.id_grupo_nomina = a.id_grupo_nomina
                      AND d.id_tipo_personal = a.id_tipo_personal
                      AND b.id_tipo_personal = d.id_tipo_personal
                      AND b.id_trabajador = a.id_trabajador
                      AND a.id_historico_nomina = b.id_historico_nomina
                      AND b.id_trabajador = tr.id_trabajador
                      AND e.id_personal = tr.id_personal
                      AND j.id_dependencia =  b.id_dependencia
                      AND k.id_cargo = tr.id_cargo
                      AND f.id_concepto_tipo_personal = a.id_concepto_tipo_personal
                      AND c.id_concepto = f.id_concepto
                      AND a.id_frecuencia_tipo_personal = ftp.id_frecuencia_tipo_personal
                      AND ftp.id_frecuencia_pago  = fp.id_frecuencia_pago
                      AND c.cod_concepto in ('5001','5002','5003','5004')

                    GROUP BY    tr.fecha_ingreso, e.cedula, e.primer_nombre, e.segundo_nombre, 
                    e.primer_apellido, e.segundo_apellido, j.nombre, c.cod_concepto,  c.descripcion
                    
                    ORDER BY  e.cedula, c.cod_concepto ASC";

        return $query;
    }

}
