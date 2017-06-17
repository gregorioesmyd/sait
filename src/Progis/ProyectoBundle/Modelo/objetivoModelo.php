<?php

namespace Progis\ProyectoBundle\Modelo;

class objetivoModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }
    
    public function cantidadComentarioActividad($actividades) 
    {
        $arrayCantidad=array();
        
        if(!empty($actividades)):
            $em = $this->em;
            $dql = "select x from PrincipalBundle:ComentarioProcesarActividad x  where x.procesaractividad in (:actividades)";
            $query = $em->createQuery($dql);
            $query->setParameter('actividades',$actividades);
            $entities = $query->getResult();


            if(!empty($entities)):
                foreach ($entities as $ca) {
                    $arrayCantidad[$ca->getProcesaractividad()->getId()]=0;
                }

                foreach ($entities as $ca) {
                    $arrayCantidad[$ca->getProcesaractividad()->getId()]=$arrayCantidad[$ca->getProcesaractividad()->getId()]+1;
                }  
            endif;
        endif;

        return $arrayCantidad;
        
    }

    public function cantidadComentarioObjetivos($objetivos) 
    {
        if(empty($objetivos))return null;
        
        $arrayCantidad=array();
        
        $em = $this->em;
        $dql = "select x from PrincipalBundle:ComentarioProcesarActividad x join x.procesaractividad pa where pa.objetivo in (:objetivos)";
        $query = $em->createQuery($dql);
        $query->setParameter('objetivos',$objetivos);
        $entities = $query->getResult();
        
        foreach ($entities as $ca) {
            $arrayCantidad[$ca->getProcesaractividad()->getObjetivo()->getId()]=0;
        }
        
        foreach ($entities as $ca) {
            $arrayCantidad[$ca->getProcesaractividad()->getObjetivo()->getId()]=$arrayCantidad[$ca->getProcesaractividad()->getObjetivo()->getId()]+1;
        }
        
        return $arrayCantidad;
        
    }
    
    //actualizo los estatus del objetivo al mover las actividades asi como las fechas
    public function estatusobjetivo($idobjetivo,$proyectoModelo){
        $estatus=1;
        $em=$this->em;
        
        $objetivo = $em->getRepository('ProyectoBundle:Objetivo')->find($idobjetivo);
        
        $dql = "select x from ProyectoBundle:ProcesarActividad x where x.objetivo= :idobjetivo";
        $query = $em->createQuery($dql);
        $query->setParameter('idobjetivo',$idobjetivo);
        $act = $query->getResult();
        
        $nuevo=false;$proceso=false;$revision=false;$culminado=false;$dependencia=false;$progreso=false;
        foreach ($act as $a) {
            
            if($a->getTiemporeal()!=null)$progreso=true;

            if($a->getUbicacion()==1)$nuevo=true;  
                else if($a->getUbicacion()==2)$proceso=true;
                else if($a->getUbicacion()==3)$revision=true;
                else if($a->getUbicacion()==4)$culminado=true;
                else if($a->getUbicacion()==5)$dependencia=true;
        }
        
        //si hay en proceso
        if(($nuevo==true and $progreso==true) or $proceso==true or $revision==true or ($dependencia==true and $progreso==true))$estatus=2;

        //si todas la actividades se culminan guardo fecha fin real
        elseif($culminado==true and $nuevo==false and $proceso==false and $revision==false and $dependencia==false){

            $ffr=new \DateTime(\date("d-m-Y G:i:s"));
            $objetivo->setFechafinreal($ffr);
            $em->flush();

            $estatus=3;
            
        }

        //si cambia el estado de nuevo a progreso guardo la fecha de inicio real
        if($objetivo->getEstatus()==1 and $estatus==2):
            $fir=new \DateTime(\date("d-m-Y G:i:s"));
            $objetivo->setFechainicioreal($fir);
            $em->flush();
        endif;
        
        $objetivo->setEstatus($estatus);
        $em->flush();  
        
        $proyectoModelo->estatusproyecto($objetivo->getProyecto()->getId());
    }
    
    
    //total de objetivos para un proyecto
    public function totalobjetivo($proyectos) {
        $em = $this->em;
        
        $totalobjetivo=array();
        foreach ($proyectos as $p){
            $dql = "select count(x) from ProyectoBundle:Objetivo x where x.proyecto=:proyecto";
            $query = $em->createQuery($dql);
            $query->setParameter('proyecto', $p);
            $objetivos = $query->getResult();
            
            $totalobjetivo[$p->getId()]=$objetivos[0][1];
        }
        //cuento las objetivos del proyecto

        return $totalobjetivo;
    }
    

    
    //se llama desde el index de la actividad
    public function porcentajeobjetivo($idobjetivo,$proyectoModelo){
        //actividades
        $em=$this->em;
        
        $dql = "select x from ProyectoBundle:ProcesarActividad x where x.objetivo= :idobjetivo order by x.ubicacion asc";
        $query = $em->createQuery($dql);
        $query->setParameter('idobjetivo',$idobjetivo);
        $pa = $query->getResult();
        
        $cerrado=0;$abierto=0;$revision=0;
        foreach ($pa as $p) {
            if($p->getUbicacion()==1 or $p->getUbicacion()==2 or $p->getUbicacion()==5) $abierto++;
            else if($p->getUbicacion()==3) $revision++;
            else if($p->getUbicacion()==4) $cerrado++;
        }
        $total=$abierto+$revision+$cerrado;
        
        if($total!=0){
            $porcentajecerrado=($cerrado*100)/$total;

            //le doy la mitad del porcentaje por estar en revision
            $porcentajerevision=($revision*50)/$total;
            
            $porcentaje=$porcentajecerrado+$porcentajerevision;
        }
        else $porcentaje=0;
        
        $objetivo = $em->getRepository('ProyectoBundle:Objetivo')->find($idobjetivo);
        $objetivo->setPorcentaje(number_format($porcentaje,0));
        $em->flush();
        
        $proyectoModelo->porcentajeproyecto($objetivo->getProyecto()->getId());
    }
}