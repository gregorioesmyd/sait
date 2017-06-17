<?php

namespace Frontend\ProyectoBundle\Modelo;

class tareaModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }

    //se llama desde el index de la actividad
    public function estatustarea($idtarea){
        $estatus=1;
        $em=$this->em;
        
        $dql = "select x from ProyectoBundle:Actividad x where x.tarea= :idtarea";
        $query = $em->createQuery($dql);
        $query->setParameter('idtarea',$idtarea);
        $act = $query->getResult();
        
        $nuevo=false;$proceso=false;$revision=false;$culminado=false;$dependencia=false;
        foreach ($act as $a) {
            
        if($a->getUbicacion()==1)$nuevo=true;  
            else if($a->getUbicacion()==2)$proceso=true;
            else if($a->getUbicacion()==3)$revision=true;
            else if($a->getUbicacion()==4)$culminado=true;
            else if($a->getUbicacion()==5)$dependencia=true;
        }
        
        //si hay en proceso
        if($proceso==true or $revision==true or $dependencia==true)$estatus=2;
        //si estan nuevos pero hay cerrados
        else if($proceso==false and $revision==false and $dependencia==false and $nuevo==true and $culminado==true)$estatus=2;
        //si solo hay cerrados culmino la tarea y guardo la fecha real
        else if($culminado==true and $nuevo==false and $proceso==false and $revision==false and $dependencia==false){
            
            $ffr=new \DateTime(\date("d-m-Y G:i:s"));

            //actualizo campos en ticket
            $query = $em->createQuery('update ProyectoBundle:Tarea x set x.fechafinreal= :ffr WHERE x.id = :idtarea and x.fechafinreal is null');
            $query->setParameter('ffr', $ffr);
            $query->setParameter('idtarea', $idtarea);
            $query->execute();  
            
            $estatus=3;
            
        }
        
        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Tarea x set x.estatus= :estatus WHERE x.id = :idtarea');
        $query->setParameter('estatus', $estatus);
        $query->setParameter('idtarea', $idtarea);
        $query->execute();  
    }
    
    //se llama desde el index de la actividad
    public function porcentajetarea($idtarea){
        //actividades
        //
        $em=$this->em;
        
        $dql = "select count(x) from ProyectoBundle:Actividad x where x.tarea= :idtarea and x.ubicacion=4";
        $query = $em->createQuery($dql);
        $query->setParameter('idtarea',$idtarea);
        $totalcerrado = $query->getSingleResult();
        
        $dql = "select count(x) from ProyectoBundle:Actividad x where x.tarea= :idtarea and x.ubicacion=3";
        $query = $em->createQuery($dql);
        $query->setParameter('idtarea',$idtarea);
        $totalrevision = $query->getSingleResult();
        
        $dql = "select count(x) from ProyectoBundle:Actividad x where x.tarea= :idtarea and (x.ubicacion<>4 and x.ubicacion<>3)";
        $query = $em->createQuery($dql);
        $query->setParameter('idtarea',$idtarea);
        $totalabierto = $query->getSingleResult();

        $total=$totalcerrado[1]+$totalabierto[1]+$totalrevision[1];
        
        if($total!=0){
            $porcentajecerrado=($totalcerrado[1]*100)/$total;

            //le doy la mitad del porcentaje por estar en revision
            $porcentajerevision=($totalrevision[1]*50)/$total;
            
            $porcentaje=$porcentajecerrado+$porcentajerevision;
            
        }
        else $porcentaje=0;
        
        

        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Tarea x set x.porcentaje= :porcentaje WHERE x.id = :idtarea');
        $query->setParameter('porcentaje', number_format($porcentaje,0));
        $query->setParameter('idtarea', $idtarea);
        $query->execute(); 

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
        
        if(!empty($tareas)):
            $contador=0;$suma=0;
            foreach ($tareas as $t) {

                $suma=$suma+$t->getPorcentaje();

                $contador++;
            }
        endif;


            $porcentaje=($suma * 100) / ($contador*100);

        
        //actualizo campos en ticket
        $query = $em->createQuery('update ProyectoBundle:Proyecto x set x.porcentaje= :porcentaje WHERE x.id = :idproyecto');
        $query->setParameter('porcentaje', number_format($porcentaje,0));
        $query->setParameter('idproyecto', $idproyecto);
        $query->execute(); 

    }
    

    

    
}