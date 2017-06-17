<?php

namespace Frontend\ProyectoBundle\Modelo;

class proyectoModelo
{
    function __construct($em) {
        $this->em = $em;
    }

    //se llama desde el index de la actividad
    public function estatusproyecto($idproyecto){
        $estatus=5;
        
        $em=$this->em;
        
        //busco si hay en proceso
        $dql = "select x from ProyectoBundle:Tarea x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $pro = $query->getResult();
        
        $nuevo=false;$proceso=false;$culminado=false;
        foreach ($pro as $a) {
            if($a->getEstatus()==1)$nuevo=true;  
            if($a->getEstatus()==2)$proceso=true;
            if($a->getEstatus()==3)$culminado=true;
        }
        
        //si hay en proceso
        if($proceso==true or $nuevo==true and $culminado==true)$estatus=2;
        //si estan nuevos pero hay cerrados
        else if($nuevo==true and $proceso==false and $culminado==false)$estatus=1;
        //si solo hay cerrados
        else if($culminado==true and $proceso==false and $nuevo==false)$estatus=3;

        
        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Proyecto x set x.estatus= :estatus WHERE x.id = :idproyecto');
        $query->setParameter('estatus', $estatus);
        $query->setParameter('idproyecto', $idproyecto);
        $query->execute();  
        
    }
    
    //se llama desde el index de la actividad
    public function porcentajeproyecto($idproyecto){

        //actividades
        $em=$this->em;
        
        
        $dql = "select x from ProyectoBundle:Tarea x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $tareas = $query->getResult();  
        
        
        $suma=0;$contador=0;
        if(!empty($tareas)):
            $contador=0;$suma=0;
            foreach ($tareas as $t) {

                $suma=$suma+$t->getPorcentaje();

                $contador++;
            }
        endif;

        if($suma!=0 && $contador!=0)
            $porcentaje=($suma * 100) / ($contador*100);
        else $porcentaje=0;

        
        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Proyecto x set x.porcentaje= :porcentaje WHERE x.id = :idproyecto');
        $query->setParameter('porcentaje', number_format($porcentaje,0));
        $query->setParameter('idproyecto', $idproyecto);
        $query->execute(); 

    }
    

    

    
}