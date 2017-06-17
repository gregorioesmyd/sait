<?php

namespace Progis\PrincipalBundle\Modelo;
use Progis\PrincipalBundle\Entity\MetaActividad;

class ubicacionModelo
{
    
    function __construct($em) {
        $this->em = $em;
        $this->respuesta=array('error'=>null,'sms'=>null,'numero'=>null);
    }

    public function guardaTiempoTotal($p,$tiempototal,$bundleActual) {
        $em=$this->em;
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $p->setFin($fa);
        $p->setTiemporeal($tiempototal);
        $em->flush();
        return;
    }

    public function nuevo($desde,$idusuario,$id,$bundleActual,$bitacoraModelo) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;
        
        if($bundleActual=='ticket')
            $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id);
        elseif($bundleActual=='proyecto')
            $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        
        if($desde=='proceso'):
            $tiempototal=$this->calcularTiempo($p);
        
            //guardo fecha fin y tiempo real del ticket
            $this->guardaTiempoTotal($p, $tiempototal,$bundleActual);
            $tr=  explode("-", $tiempototal);
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
            $respuesta['sms']='La tarjeta se ha pausado.';
        
        elseif($p->getTiemporeal()!=null):
        
            $tr=  explode("-", $p->getTiemporeal());
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
            $respuesta['sms']='La tarjeta se ha pausado.';
        
        else:
        
            $respuesta['sms']='La tarjeta se ha ubicado en nuevo.';
        
        endif;
        
        return $respuesta;
    }
    
    public function proceso($desde,$idusuario,$id,$bundleActual,$bitacoraModelo) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;

        //valido que no se coloquen dos tickets en proceso
            $dql = "select p from TicketBundle:ProcesarTicket p join p.responsable me join me.usuario u  where u.id= :idusuario and p.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('idusuario',$idusuario);
            $tarjetaAsignadaTicket = $query->getResult();

            $dql = "select p from ProyectoBundle:ProcesarActividad p join p.responsable mp join mp.miembroespacio me join me.usuario u where u.id= :idusuario and p.ubicacion=2";
            $query = $em->createQuery($dql);
            $query->setParameter('idusuario',$idusuario);
            $tarjetaAsignadaProyecto = $query->getResult();
        //fin

        if(!empty($tarjetaAsignadaTicket)):
            $respuesta['error']=true;
            $respuesta['sms']='Ya posee un ticket en proceso, puede visualizarlo <a target="__blank" href="/sait/web/app_dev.php/progis/ticket/procesarTicket">aquí</a>';
            return $respuesta;
        elseif(!empty($tarjetaAsignadaProyecto)):
            $respuesta['error']=true;
            $respuesta['sms']='Ya posee una actividad en proceso, puede visualizarla <a target="__blank" href="/sait/web/app_dev.php/progis/proyecto/procesarActividad/'.$tarjetaAsignadaProyecto[0]->getObjetivo()->getId().'">aquí</a>.';
            return $respuesta;
        else:

            //guardo fecha comienzo
            $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
            if($bundleActual=='ticket'):
                $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id);
            elseif($bundleActual=='proyecto'):
                $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
            endif;
            
            $p->setComienzo($fa);
            $em->flush();

            $respuesta['sms']='Se ha ubicado la tarjeta en proceso.';
            $respuesta['cuentaregresiva']=$this->calculaCuentaRegresiva($p,$bundleActual);
        endif;
        //fin validación
        
        return $respuesta;
    }
    
    public function dependencia($desde,$idusuario,$id,$bundleActual,$bitacoraModelo) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;
        
        if($bundleActual=='ticket'):
            $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id);
        elseif($bundleActual=='proyecto'):
            $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        endif;

            $p->setJustificacion(true);
            $em->flush();
        
        if($desde=='proceso'):
            $tiempototal=$this->calcularTiempo($p);
            //guardo fecha fin y tiempo real del ticket
            $this->guardaTiempoTotal($p, $tiempototal,$bundleActual);
            $tr=  explode("-", $tiempototal);
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
        
        elseif($p->getTiemporeal()!=null):
        
            $tr=  explode("-", $p->getTiemporeal());
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
        
        endif;

        $respuesta['sms']='La tarjeta se ha pausado en dependencia, justifique en los comentarios porque.';
        
        return $respuesta;

        
    }
    
    public function revision($desde,$idusuario,$id,$bundleActual,$bitacoraModelo) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;
        
        $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        
        if($desde=='proceso'):
            $tiempototal=$this->calcularTiempo($p);
        
            //guardo fecha fin y tiempo real del ticket
            $this->guardaTiempoTotal($p, $tiempototal,$bundleActual);
            $tr=  explode("-", $tiempototal);
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
        
        elseif($p->getTiemporeal()!=null):
        
            $tr=  explode("-", $p->getTiemporeal());
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
        elseif($p->getTiemporeal()==null):
            $respuesta['error']=true;
            $respuesta['sms']="No se puede colocar en revisión una tarjeta que no tiene progreso.";
            return $respuesta;
        endif;

        $respuesta['sms']='Espere la revisión por parte del encargado de proyecto.';
        
        return $respuesta;
    }
    
    
    public function culminado($desde,$idusuario,$id,$bundleActual,$bitacoraModelo,$proyectoModelo) {
        //inicializo respuestas
        $respuesta=  $this->respuesta;
        $em=$this->em;
        
        if($bundleActual=='ticket'):
            $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id);
        
            if($desde=='proceso'):
                $tiempototal=$this->calcularTiempo($p);

                //guardo fecha fin y tiempo real del ticket
                $this->guardaTiempoTotal($p, $tiempototal,$bundleActual);
                $tr=  explode("-", $tiempototal);
                //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
                $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
                $respuesta['retardo']=  $this->retardo($p);
            endif;
                
        endif;

        if($bundleActual=='proyecto')
            $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id);
        
        //si tiene progreso procedo a cerrarla y calculo si tuvo retardo
        if($p->getTiemporeal()!=null):
            $tr=  explode("-", $p->getTiemporeal());
            //$respuesta['tiemporeal']="D:".$tr[0]." - H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['tiemporeal']="H:".$tr[1]." - M:".$tr[2]." - S:".$tr[3];
            $respuesta['retardo']=  $this->retardo($p);
       


        elseif($p->getTiemporeal()==null):
            $respuesta['error']=true;
            $respuesta['sms']="No se puede culminar una tarjeta que no tiene progreso.";
            return $respuesta;
        endif;


        
        $respuesta['sms']='La tarjeta se ha culminado.';

        return $respuesta;
    }
    
   
    //se llama desde el index de la actividad
    public function ubicacion($recibe,$ubicacion,$idusuario,$objetivoModelo,$bitacoraModelo,$admin,$proyectoModelo){
        $em=$this->em;
        $respuesta=  $this->respuesta;

        //divido los datos enviados desde el ajax
        $datos = explode("||", $recibe);
        $recibe = explode("-", $datos[0]);
        $bundleActual = $datos[1];
        
        $id=$recibe[1];
        $desde=$recibe[2];
        $hasta=$recibe[0];
        
        if($bundleActual=='ticket'):
            $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id);
            $responsable=$p->getResponsable()->getUsuario();
        
            //si la mueven desde culmiando elimino la fecha fin y solucion
            if($desde=='culminado' and $p->getTicket()->getEstatus()==4):
                $respuesta['error']=true;
                $respuesta['sms']='Este ticket ya se encuentra cerrado.';
                return $respuesta;
            elseif($desde=='culminado' and $p->getTicket()->getEstatus()!=4):
                $t = $em->getRepository('TicketBundle:Ticket')->find($id);
                $t->setFechasolucion(null);
                $t->setHorasolucion(null);
                $t->setSolucion(null);
                $t->setEstatus(2);
                $em->flush();
            elseif($desde=='dependencia'):
                $p->setJustificacion(false);
                $em->flush();
            endif;
            //fin
            
        elseif($bundleActual=='proyecto'):
            $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($id); 
            $responsable=$p->getResponsable()->getMiembroespacio()->getUsuario();
            
            if($desde!='revision' and $hasta=='culminado' and $responsable->getId()==$idusuario):
                $respuesta['error']=true;
                $respuesta['sms']='No puede culminar esta tarjeta, primero debe ser ubicada en revisión.';
                return $respuesta;
            elseif($desde=='culminado'):
                $respuesta['error']=true;
                $respuesta['sms']='Esta actividad ya se encuentra cerrada.';
                return $respuesta;
            elseif($desde=='revision' and $responsable->getId()==$idusuario and $admin==false):
                $respuesta['error']=true;
                $respuesta['sms']='No puede mover esta tarjeta porque se encuentra en revisión.';
                return $respuesta;
            elseif(($desde=='revision' and $hasta=='proceso')):
                $respuesta['error']=true;
                $respuesta['sms']='Las actividades en revisión no puden ser colocadas en proceso.';
                return $respuesta;
            endif;
        endif;
        
        //validaciones simples
        if($responsable->getId()!=$idusuario and $admin==false):
            $respuesta['error']=true;
            $respuesta['sms']='Solo el responsable y el administrador puede mover esta tarjeta.';
            return $respuesta;
        elseif($desde=='dependencia' and ($hasta=='revision' or $hasta=='culminado') and $p->getTiemporeal()==null):
            $respuesta['error']=true;
            $respuesta['sms']='Esta tarjeta no posee ningun progreso por lo tanto no puede ser puesta en revision ni culminada.';
            return $respuesta;
        //fin validaciones simples
            
        //redirijo hasta la columna donde mueve la tarjeta
        else:
            $respuesta=$this->$hasta($desde,$idusuario,$id,$bundleActual,$bitacoraModelo,$proyectoModelo);
        endif;
         
        //si devuelve un error desde el método 
        if($respuesta['error']==true): return $respuesta;
        
        //guardo la ubicacion si no retorna un error
        else:
            
            $p->setUbicacion($ubicacion);
            $em->flush();
            
            if($bundleActual=='proyecto'):
                $objetivoModelo->estatusobjetivo($p->getObjetivo()->getId());
                $objetivoModelo->porcentajeobjetivo($p->getObjetivo()->getId());
            endif;

            return $respuesta;
        endif;

    }

    //calcula tiempo que tardan las tarjetas
    public function calcularTiempo($p) {
        
        //calculo el tiempo de duracion con la fecha comienzo y el tiempo actual
        $comienzo=new \DateTime($p->getComienzo()->format("d-m-Y G:i:s"));
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        $intervalo=$comienzo->diff($fa);

        //verifico si ya hay un tiempo real guardado y lo sumo
        $tiemporeal=$p->getTiemporeal();
        
        $tiempototal=array();
        if($tiemporeal==null){
            $tiempototal=$intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s;
        }else{

            $tiempototal=$this->sumarTiempo($tiemporeal, $intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s);
        }

        return $tiempototal;
    }
    
    //sumo el nuevo tiempo con el tiemporeal ya guardado
    public function sumarTiempo($tanterior,$tnuevo) {
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        //convierto los datos de tiempo real
        $tanterior=  explode("-", $tanterior);
        $tnuevo=  explode("-", $tnuevo);
        
        //dia a segundo
        $diasegundo=($tanterior[0]+$tnuevo[0])*86400;
        
        //hora a segundo
        $horasegundo=($tanterior[1]+$tnuevo[1])*3600;
        
        //minuto a segundo
        $minutosegundo=($tanterior[2]+$tnuevo[2])*60;
        
        //sumo todos los segundos
        $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tanterior[3]+$tnuevo[3];
        
        //sumo todo a la fecha actual
        $duracion = strtotime ( '+'.$segundototal.' second', strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;
        $duracion = date ( 'Y-m-d G:i:s' , $duracion );
        $duracion=new \DateTime($duracion);      
        
        //obtengo la diferencia o total con la segunda fecha actual
        $intervalo=$duracion->diff($fa);
        
        return $intervalo->d.'-'.$intervalo->h.'-'.$intervalo->i.'-'.$intervalo->s;
    }
    
    
    //tiempo para la cuenta regresiva
    public function calculaCuentaRegresiva($p,$bundleActual) {
       
        $comienzo=$p->getComienzo();
        $tiemporeal=$p->getTiemporeal();
        $fa=new \DateTime(\date("d-m-Y G:i:s"));

    //renombro los tipos de tiempo para puder hacer la sumatoria
        if($p->getTipotiempo()==1)$tt='day';
        else if($p->getTipotiempo()==2)$tt='hour';
        else if($p->getTipotiempo()==3)$tt='minute';
        
    //sumo el tiempo estimado a la fecha de comienzo para saber el tota
        $tiempoestimado = strtotime ( '+'.$p->getTiempoestimado().' '.$tt , strtotime ( $comienzo->format("d-m-Y G:i:s") ) ) ;

        //si hay un tiempo real
        if($tiemporeal!=null){
        
       
            //sumo el tiempo utilizado a la fecha actual y obtengo el utilizado
                $tiemporeal=  explode("-", $tiemporeal);
                $diasegundo=$tiemporeal[0]*86400;
                $horasegundo=$tiemporeal[1]*3600;
                $minutosegundo=$tiemporeal[2]*60;
                $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tiemporeal[3];

                $tiempoconsumido = strtotime ( '+'.$segundototal.' second' , strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;


            //si la actividad ya se ha retrasado coloco 0 en la cuenta regresiva
            if($tiempoconsumido>$tiempoestimado)
                $cuentaregresiva="00-00-00-00";
            else{

                //convierto ambas fecha para poder hacer un diff
                $tiempoestimado = date ( 'Y-m-d G:i:s' , $tiempoestimado );
                $tiempoestimado=new \DateTime($tiempoestimado);

                $tiempoconsumido = date ( 'Y-m-d G:i:s' , $tiempoconsumido );
                $tiempoconsumido=new \DateTime($tiempoconsumido);
                
                $intervalo=$tiempoestimado->diff($tiempoconsumido);

                $cuentaregresiva=str_pad($intervalo->d,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->h,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->i,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->s,2,"0",STR_PAD_LEFT);
            }
        } else{
            
                //aca calculo el tiempo transcurrido cuando la actividad no ha sido pausada
                //calculo el tiempo estimado sumando el tiempo de la actividad a la fecha de comienzo
                //luego hago un dif entre el tiempo estimado y la fecha actual

                $tiempoactual=strtotime ( $fa->format("d-m-Y G:i:s") );
                
                if($tiempoactual>$tiempoestimado)
                    $cuentaregresiva="00-00-00-00";
                else{
                
                    $tiempoestimado = date ( 'Y-m-d G:i:s' , $tiempoestimado );
                    $tiempoestimado=new \DateTime($tiempoestimado);

                    $intervalo=$tiempoestimado->diff($fa);

                    $cuentaregresiva=str_pad($intervalo->d,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->h,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->i,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->s,2,"0",STR_PAD_LEFT);
                }
  
        }
        return $cuentaregresiva;
        
    }
    
  public function retardo($p){
        $em=$this->em;
        $act = $p;
        $fa=new \DateTime(\date("d-m-Y G:i:s"));

        //calculo el tiempo estimado de la actividad 
        if($act->getTipotiempo()==1)$tt='day';
        else if($act->getTipotiempo()==2)$tt='hour';
        else if($act->getTipotiempo()==3)$tt='minute';
        $duracionestimada = strtotime ( '+'.$act->getTiempoestimado().' '.$tt , strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;
        $duracionestimada = date ( 'Y-m-d G:i:s' , $duracionestimada );
        $duracionestimada=new \DateTime($duracionestimada);
        
        //convierto los datos de tiempo real
        $tiemporeal=$act->getTiemporeal();
        $tiemporeal=  explode("-", $tiemporeal);
        //dia a segundo
        $diasegundo=$tiemporeal[0]*86400;
        //hora a segundo
        $horasegundo=$tiemporeal[1]*3600;
        //minuto a segundo
        $minutosegundo=$tiemporeal[2]*60;
        $segundototal=$diasegundo+$horasegundo+$minutosegundo+$tiemporeal[3];

        $duracionreal = strtotime ( '+'.$segundototal.' second', strtotime ( $fa->format("d-m-Y G:i:s") ) ) ;
        $duracionreal = date ( 'Y-m-d G:i:s' , $duracionreal );
        $duracionreal=new \DateTime($duracionreal);
        
        if(strtotime($duracionreal->format("d-m-Y G:i:s")) >= strtotime($duracionestimada->format("d-m-Y G:i:s"))):
            $act->setRetardo(true);
            $em->flush();
            return true; 
        else: 
            return false;
        endif;
                
    }
    
    //se llama desde el index de la actividad
    public function ubicacionPriometa($recibe,$ubicacion,$idusuario){
        $em=$this->em;
        $respuesta=  $this->respuesta;

        //divido los datos enviados desde el ajax
        $datos = explode("-", $recibe);
        $desde=$datos[0];
        $hasta=$datos[1];
        $idproceso=$datos[2];
        $tipotarjeta=$datos[3];
        $idmeta=$datos[4];
        

        
        if($tipotarjeta=='ticket'):
            $p = $em->getRepository('TicketBundle:ProcesarTicket')->find($id); 
        
            if($hasta=='priometa'):
                $p->setUbicacionPriometa(2);
                
            elseif($hasta=='pendiente'):
                $p->setUbicacionPriometa(1);
                $p->setOrdenPriometa(null);
            endif;
            
            $em->flush();
            
            $respuesta['numero']=$p->getOrdenPriometa();
            $respuesta['sms']="Tarjeta ubicada con exito.";
            
        elseif($tipotarjeta=='proyecto'):
            $p = $em->getRepository('ProyectoBundle:ProcesarActividad')->find($idproceso); 
            $m = $em->getRepository('PrincipalBundle:Metas')->find($idmeta); 
        
            $metaActividad=new MetaActividad;

            if($hasta=='priometa'):
                $p->setUbicacionPriometa(2);
                $metaActividad->setActividad($p);
                $metaActividad->setMeta($m);
                $em->persist($metaActividad);
                
            elseif($hasta=='pendiente'):
                $p->setUbicacionPriometa(1);
                $p->setOrdenPriometa(null);
                
                $x = $em->getRepository('PrincipalBundle:MetaActividad')->findByActividad($idproceso); 
                 $em->remove($x[0]);
            endif;
            
            $em->flush();
            
            $respuesta['numero']=$p->getOrdenPriometa();
            $respuesta['sms']="Tarjeta ubicada con exito.";

        endif;
        
        return $respuesta;
    }
    
}