<?php

namespace Progis\ProyectoBundle\Modelo;

class proyectoModelo
{
    function __construct($em) {
        $this->em = $em;
    }
    
    public function cantidadComentarioProyecto($proyectos) 
    {
        $arrayCantidad=array();
        
        if(!empty($proyectos)):
            $em = $this->em;
            $dql = "select x from PrincipalBundle:ComentarioProyecto x where x.proyecto in (:proyectos) ";
            $query = $em->createQuery($dql);
            $query->setParameter('proyectos',$proyectos);
            $entities = $query->getResult();

            foreach ($entities as $cp) {
                $arrayCantidad[$cp->getProyecto()->getId()]=0;
            }

            foreach ($entities as $cp) {
                $arrayCantidad[$cp->getProyecto()->getId()]=$arrayCantidad[$cp->getProyecto()->getId()]+1;
            }
        endif;

        
        return $arrayCantidad;
        
    }
    public function listaProyecto($idusuario,$funcionModelo,$session,$admin) 
    {
        $em = $this->em;
        $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($idusuario);
        $idNivelMiembroEspacio=$funcionModelo->idNivelMiembroEspacio($perfil);
        $rolGeneralNivel=$session->get('rolGeneralNivel');

        
        //verifico en que niveles tengo rol de proyecto general
        
        $idNivelGen=null;
        
        //si es admin asigno el nivel de la unidad por si no es miembro de su propio espacio
        if($admin==true){
            $idNivelGen[]=$perfil->getNivelorganizacional()->getId();
        }
        
        if(!empty($rolGeneralNivel) and $idNivelMiembroEspacio!=null):
            foreach ($idNivelMiembroEspacio as $inme) {
                if(
                       $rolGeneralNivel[$inme]["ROLE_PROGIS_PROYECTO_ADMIN"]==true
                    or $rolGeneralNivel[$inme]["ROLE_PROGIS_PROYECTO_CONSULTA"]==true
                    or $rolGeneralNivel[$inme]["ROLE_PROGIS_PROYECTO_TECNICO"]==true
                    or $rolGeneralNivel[$inme]["ROLE_PROGIS_SUPERVISOR"]==true
                  ):
                    $idNivelGen[]=$inme;
                endif;
            }
        endif;
        
        if(!empty($idNivelGen))
        $idNivelGen = array_unique($idNivelGen);
        
        $entities=array(0);
        //consulto proyectos generales
        $dql = "select x from ProyectoBundle:Proyecto x join x.nivelorganizacional no where no.id in (:idnivel) order by x.estatus asc,x.fechafinestimada ASC,x.fechafinreal ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$idNivelGen);
        $entities = $query->getResult();


        if(empty($entities))$proGen=array(0);else $proGen=$entities;
        
        //consulto proyectos específicos    
        $dql = "select x from ProyectoBundle:Miembroproyecto x join x.proyecto p join x.miembroespacio me join me.usuario u where x.proyecto not in (:proyecto) and u.id= :idusuario and me.activo=true";
        $query = $em->createQuery($dql);
        $query->setParameter('idusuario',$idusuario);
        $query->setParameter('proyecto',$proGen);
        $consulta= $query->getResult();

        foreach ($consulta as $x) {
            $entities[]=$x->getProyecto();
        }

         return $entities;
    }
    
    public function miembroProyecto($proyectos) {
        $em = $this->em;
        
        $miembroProyecto=array();
        foreach ($proyectos as $p){
            $dql = "select x from ProyectoBundle:Miembroproyecto x where x.proyecto=:proyecto";
            $query = $em->createQuery($dql);
            $query->setParameter('proyecto',$p);
            $consulta= $query->getResult();
            
            foreach ($consulta as $x) {
                $miembroProyecto[$x->getProyecto()->getId()][]=$x;
            }
        }
        
        return $miembroProyecto;
        
    }

    //se llama desde el index de la actividad
    public function estatusproyecto($idproyecto){
        $estatus=1;

        $em=$this->em;
        
        //busco si hay en proceso
        $dql = "select x from ProyectoBundle:Objetivo x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $objetivo = $query->getResult();
        
        $nuevo=false;$proceso=false;$culminado=false;
        foreach ($objetivo as $a) {
            if($a->getEstatus()==1)$nuevo=true;  
            if($a->getEstatus()==2)$proceso=true;
            if($a->getEstatus()==3)$culminado=true;
        }
        
        //nuevo
        if($nuevo==true and $proceso==false and $culminado==false)$estatus=1;
        
        //en proceso
        else if($proceso==true and $nuevo==true and $culminado==true)$estatus=2;
        else if($proceso==true and $nuevo==false and $culminado==false)$estatus=2;
        else if($proceso==true and $nuevo==false and $culminado==true)$estatus=2;
        else if($proceso==false and $nuevo==true and $culminado==true)$estatus=2;
        else if($proceso==true and $nuevo==true and $culminado==false)$estatus=2;
        
        //culminado
        else if($culminado==true and $proceso==false and $nuevo==false)$estatus=3;

        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $proyecto->setEstatus($estatus);
        $em->flush();
    }
    
    //se llama desde el index de la actividad
    public function porcentajeproyecto($idproyecto){

        //actividades
        $em=$this->em;
        
        $dql = "select x from ProyectoBundle:Objetivo x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $objetivos = $query->getResult();  
        
        $suma=0;$contador=0;
        if(!empty($objetivos)):
            foreach ($objetivos as $o) {

                $suma=$suma+$o->getPorcentaje();

                $contador++;
            }
        endif;

        if($suma!=0 && $contador!=0)
            $porcentaje=($suma * 100) / ($contador*100);
        else $porcentaje=0;

        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $proyecto->setPorcentaje(number_format($porcentaje,0));
        $em->flush();


    }
    
    public function fechaInicioProyecto($idproyecto){

        $em=$this->em;
        
        //busco la fecha mas baja de los objetivos para que sea la fecha de inicio del proyecto
        $dql = "select min(x.fechainicioestimada),min(x.fechainicioreal) from ProyectoBundle:Objetivo x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $objetivo = $query->getResult();
        
        if(!empty($objetivo)):
            $fechainicioestimada=$objetivo[0][1];
            $fechainicioreal=$objetivo[0][2];
        else:
            $fechainicioestimada=null;
            $fechainicioreal=null;
        endif;

        $fechainicioestimada=new \DateTime(\date($fechainicioestimada));
        $fechainicioreal=new \DateTime(\date($fechainicioreal));

        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $proyecto->setFechainicioestimada($fechainicioestimada);
        $proyecto->setFechainicioreal($fechainicioreal);
        $em->flush();

    }
    
    //se llama al crear o borrar una actividad
    public function fechaFinProyecto($idproyecto){

        $em=$this->em;
        
        //busco la fecha mas alta de los objetivos para que sea la fecha de fin del proyecto
        $dql = "select max(x.fechafinestimada),max(x.fechafinreal) from ProyectoBundle:Objetivo x where x.proyecto= :idproyecto";
        $query = $em->createQuery($dql);
        $query->setParameter('idproyecto',$idproyecto);
        $objetivo = $query->getResult();
        
        if(!empty($objetivo)):
            $fechafinestimada=$objetivo[0][1];
            $fechafinreal=$objetivo[0][2];
        else:
            $fechafinestimada=null;
            $fechafinreal=null;
        endif;

        $fechafinestimada=new \DateTime(\date($fechafinestimada));
        $fechafinreal=new \DateTime(\date($fechafinreal));

        
        $proyecto = $em->getRepository('ProyectoBundle:Proyecto')->find($idproyecto);
        $proyecto->setFechafinestimada($fechafinestimada);
        $proyecto->setFechafinreal($fechafinreal);
        $em->flush();


    }
    
    public function pendientes($perfil){
        $em=$this->em;
        
        $pendientes['metas']=null;
        $pendientes['actividades']=null;
        
        $dql = "select x from PrincipalBundle:MetaActividad x join x.meta m join x.actividad a join m.miembroespacio me where me.usuario= :usuario and a.ubicacion!=3 and a.ubicacion!=4 order by m.fechahasta ASC, a.ordenPriometa asc, a.ubicacion asc ";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $pendientes['metas'] = $query->getResult();
        $metas=array(0);
        foreach ($pendientes['metas'] as $m){
            $metas[]=$m->getActividad()->getId();
        }
        
        $dql = "select x from ProyectoBundle:ProcesarActividad x join x.objetivo o join x.responsable mp join mp.miembroespacio me where x.id not in (:metas) and me.usuario= :usuario and x.ubicacion!=3 and x.ubicacion!=4 order by o.fechafinestimada asc";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $query->setParameter('metas',$metas);
        $pendientes['actividades'] = $query->getResult();
        
        $dql = "select x from TicketBundle:ProcesarTicket x join x.ticket t join x.responsable mp where mp.usuario= :usuario and x.ubicacion!=4 order by t.fechasolicitud desc";
        $query = $em->createQuery($dql);
        $query->setParameter('usuario',$perfil);
        $tickets = $query->getResult();
        
        $pendientes['tickets']=$tickets;
        
        return $pendientes;
        
    }
}