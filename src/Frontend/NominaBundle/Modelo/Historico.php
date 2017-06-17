<?php

namespace Frontend\NominaBundle\Modelo;
use Administracion\UsuarioBundle\Resources\Misclases\Conexion;

class Historico
{
    function __construct($em) {
        $this->em = $em;
    }

    public function consulta($datos,$sistema)
    {
        $em=  $this->em;
        $a=new Conexion;
        $data=null;
        ////////////////////////////////////////////////////////////////////////DATAPRO SE PUEDE CONSULTAR ENTRE 2005 Y 2007
        if($sistema=='datapro'){
            if($datos->getAniodesde()<2008){
                
                if($datos->getAniohasta()>2007)
                    $aniohasta=2007;
                else $aniohasta=$datos->getAniohasta();
                
                $datapro_139=$a->datapro_139();
                
                $query="
                    
                select nomina as tipopersonal,cedula,anio,mes,nombre as primer_nombre,apellido as primer_apellido,'' as segundo_apellido,fecha_ingreso as ingresoorganismo,cargo,departamento as nombreunidad,cod_concepto as codcon,desc_concepto as descon,sum(monto) as total,asigdedu as tipo
                from historico 
                where anio>=".$datos->getAniodesde()." and mes>=".$datos->getMesdesde()." and anio<=".$aniohasta." and mes<=".$datos->getMeshasta()." and cedula like '%".$datos->getCedula()."%'
                group by nomina,cedula,anio,mes,nombre,apellido,fecha_ingreso,cargo,departamento,cod_concepto,desc_concepto,asigdedu 
                order by anio,mes,cod_concepto

                ";

                $rs = pg_query($datapro_139,$query);  
                while ($row=pg_fetch_array($rs)){
                    $data[]=$row;
                }   

                return $data;
                
            }else return null; 
        }
        ////////////////////////////////////////////////////////////////////////INFOCENT SE PUEDE CONSULTAR ENTRE 2008 Y 2011
        elseif($sistema=='infocent'){
            if($datos->getAniodesde()<2012){
                
                if($datos->getAniohasta()>2011)
                    $aniohasta=2011;
                else $aniohasta=$datos->getAniohasta();
                

                /*$anios=null;
                for($i=$datos->getAniodesde();$i<=$datos->getAniohasta();$i++){
                    if($i<2012)
                    $anios .=$i.',';
                }
                $anios=  substr($anios, 0,-1);


                $meses=null;
                for($i=$datos->getMesdesde();$i<=$datos->getMeshasta();$i++){
                    $meses .=$i.',';
                }
                $meses=  substr($meses, 0,-1);  */          


                $infocent_139=$a->infocent_139();

                $query="
                select a.tnom_tipnom as tipopersonal,trim(c.cedula)as cedula,A.FPRO_ANOCAL as anio, A.MESCAL as mes, c.nombr1 as primer_nombre, c.apell1 as primer_apellido, c.apell2 as segundo_apellido, c.fecing as ingresoorganismo, d.descar as cargo, e.desdep as nombreunidad,a.cto_codcto as codcon, b.descto as descon, round(sum(a.moncto),2) as total, case B.FUNCTO when '1' then 'asignacion' else 'deduccion' end as tipo
                from nmm024 a, nmt027 b, nmm001 c, nmt004 d, nmt019 e       
                where
                A.FPRO_ANOCAL>=".$datos->getAniodesde()."
                and A.mescal>= ".$datos->getMesdesde()."
                and A.FPRO_ANOCAL<=".$aniohasta."
                and A.mescal<= ".$datos->getMeshasta()."
                and c.cedula like '%".$datos->getCedula()."%'
                and a.proc_tippro = 1    
                and a.cto_codcto=b.codcto
                and a.tnom_tipnom=b.tnom_tipnom
                and a.trab_fictra=c.fictra
                and c.cgo_carocu=d.codcar
                and a.dpto_coddep=e.coddep
                group by a.tnom_tipnom,a.cto_codcto, b.descto, c.cedula, c.nombr1, c.apell1, c.apell2, c.fecing, d.descar, e.desdep, A.FPRO_ANOCAL, A.MESCAL,A.TRAB_FICTRA,B.FUNCTO
                order by  A.FPRO_ANOCAL, A.MESCAL  ASC

                ";

                $rs = pg_query($infocent_139,$query);  
                while ($row=pg_fetch_array($rs)){
                    $data[]=$row;
                }   

                return $data;
            }else return null;    
        }
        ////////////////////////////////////////////////////////////////////////SIGEFIRRHH SE PUEDE CONSULTAR DESDE 2012 EN ADELANTE
        else if($sistema="sigefirrhh"){
        

            if($datos->getAniohasta()>2011){
                if($datos->getAniodesde()<2012)
                    $aniodesde=2012;
                else $aniodesde=$datos->getAnioDesde();
            
                $postgresql_sigefirrhh=$a->postgresql_sigefirrhh();

                $query="
                    SELECT  max(d.nombre) as tipopersonal, max(e.cedula) as cedula,a.anio,a.mes,
                    e.primer_nombre as primer_nombre,
                    e.primer_apellido as primer_apellido, e.segundo_apellido as segundo_apellido,
                    max(tr.fecha_ingreso) as ingresoorganismo,k.descripcion_cargo as cargo,
                    j.nombre as nombreunidad, c.cod_concepto as codcon,
                     c.descripcion as  descon,
                    (sum(a.monto_asigna)+sum(a.monto_deduce)) as total, case sum(a.monto_asigna) when 0 then 'deduccion' else 'asignacion' end as tipo

                    FROM historicoquincena a,   historiconomina b, trabajador tr,concepto c, tipoPersonal d, 
                    personal e, conceptoTipoPersonal f, grupoNomina g, 
                    dependencia j, cargo k, frecuenciaTipoPersonal ftp, frecuenciaPago fp

                    WHERE 
                    a.anio>=".$aniodesde."
                    and a.mes>= ".$datos->getMesdesde()."
                    and a.anio<= ".$datos->getAniohasta()."
                    and a.mes<= ".$datos->getMeshasta()."                 
                    AND e.cedula      =  ".$datos->getCedula()."
                    AND a.numero_nomina     =  0
                    AND g.id_grupo_nomina             =  a.id_grupo_nomina
                    AND d.id_tipo_personal             =  a.id_tipo_personal
                    AND b.id_tipo_personal            =  d.id_tipo_personal
                    AND b.id_trabajador         =  a.id_trabajador
                    AND a.id_historico_nomina=b.id_historico_nomina
                    AND b.id_trabajador=tr.id_trabajador
                    AND e.id_personal         =  tr.id_personal
                    AND j.id_dependencia          =  b.id_dependencia
                    AND k.id_cargo              =  tr.id_cargo
                    AND f.id_concepto_tipo_personal =  a.id_concepto_tipo_personal
                    AND c.id_concepto                 =  f.id_concepto
                    AND a.id_frecuencia_tipo_personal = ftp.id_frecuencia_tipo_personal
                    AND ftp.id_frecuencia_pago  = fp.id_frecuencia_pago

                    group by d.nombre, 
                            tr.fecha_ingreso,
                            e.cedula, e.primer_nombre, e.segundo_nombre,
                      e.primer_apellido, e.segundo_apellido,
                            k.descripcion_cargo,
                            j.nombre, c.cod_concepto,
                            c.descripcion,a.anio,a.mes

                    order by  a.anio,a.mes,d.nombre,e.cedula, c.cod_concepto ASC
                ";

                $rs = pg_query($postgresql_sigefirrhh,$query);
                while ($row=pg_fetch_array($rs)){
                    $data[]=$row;
                }

                return $data;
            } else return null;
        }
    }
}