<?php

namespace Progis\PrincipalBundle\Modelo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class productividadModelo extends Controller
{
    function __construct($em) {
        $this->em = $em;
    }
    
    public function productividad($usuarios,$fechaDesde,$fechaHasta) {
        $em = $this->em;
        $consulta['ticketsCerrados']=null;
        $consulta['metasCulminadas']=null;
        $consulta['ticketsCerrados']=null;
        $responsable=array();

        $mejorPeorConTarjeta=array();
        $mejorPeorSinTarjeta=array();
        foreach ($usuarios as $u) {
            if($u->getJefe()==false):

            //inicializo
            $tiempoEstimado=0;
            $tiempoReal=0;
            
            
            $responsable[$u->getId()]['tarjetasValidas']=0;
            $responsable[$u->getId()]['tiempoRealTV']=  0;
            $responsable[$u->getId()]['tiempoEstimadoTV']=  0;
            
            $responsable[$u->getId()]['tarjetasInvalidas']=0;
            $responsable[$u->getId()]['tiempoRealTI']=  0;
            $responsable[$u->getId()]['tiempoEstimadoTI']=  0;
            
            $responsable[$u->getId()]['tarjetasRetardadas']=0;
            $responsable[$u->getId()]['tiempoRealTR']=  0;
            $responsable[$u->getId()]['tiempoEstimadoTR']=  0;
            
            $responsable[$u->getId()]['tiempoReal']=  0;
            $responsable[$u->getId()]['tiempoEstimado']=  0;
            
            $responsable[$u->getId()]['tiempoAhorrado']=0;
            $responsable['mejorPeor']=null;
            
            $responsable[$u->getId()]['totalTicketPesimo']=0;
            $responsable[$u->getId()]['totalTicketRegular']=0;
            $responsable[$u->getId()]['totalTicketBueno']=0;
            $responsable[$u->getId()]['totalTicketMuyBueno']=0;
            $responsable[$u->getId()]['totalTicketMuyExcelente']=0;
            
                       
            //tickets atendidos culminados
            $dql = "select p from TicketBundle:ProcesarTicket p join p.ticket t join p.responsable me where me.usuario=:usuario and t.estatus=4 and p.fin>=:fechaDesde and p.fin<=:fechaHasta";
            $query = $em->createQuery($dql);
            $query->setParameter('usuario',$u);
            $query->setParameter('fechaDesde',$fechaDesde);
            $query->setParameter('fechaHasta',$fechaHasta);
            $consulta['ticketsCerrados']= $query->getResult();

            
            foreach ($consulta['ticketsCerrados'] as $tc) {
                
                
                if($tc->getTicket()->getValoracion()!=null):
                    if($tc->getTicket()->getValoracion()==1)
                        $responsable[$u->getId()]['totalTicketPesimo']++;
                    elseif($tc->getTicket()->getValoracion()==2)
                        $responsable[$u->getId()]['totalTicketRegular']++;
                    elseif($tc->getTicket()->getValoracion()==3)
                        $responsable[$u->getId()]['totalTicketBueno']++;
                    elseif($tc->getTicket()->getValoracion()==4)
                        $responsable[$u->getId()]['totalTicketMuyBueno']++;
                    elseif($tc->getTicket()->getValoracion()==5)
                        $responsable[$u->getId()]['totalTicketMuyExcelente']++;
                endif;

                //calculo tiempo real de actividad
                $trt=0;
                $tiempo=  explode("-",$tc->getTiemporeal());
                $trt +=$tiempo[0]/24;    //dias                
                $trt +=$tiempo[1];       //tiempoReal
                $trt +=$tiempo[2]/60;    //minutos                
                $trt +=$tiempo[3]/3600;   //segundos

                //calculo total tiempo estimado de actividad
                $tet=0;
                if($tc->getTipotiempo()=='2')
                    $tet +=$tc->getTiempoestimado();
                elseif($tc->getTipotiempo()=='3')
                    $tet +=$tc->getTiempoestimado()/60;

                //tarjetas retardadas
                if($trt>$tet):
                    $responsable[$u->getId()]['tarjetasRetardadas'] +=1;
                    $responsable[$u->getId()]['tiempoRealTR'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTR'] +=  $tet;
                    
                    
                //tarjeta inválidas si tiene menos de un minuto
                elseif($trt<($tet/2)):
                    $responsable[$u->getId()]['tarjetasInvalidas'] +=1;
                    $responsable[$u->getId()]['tiempoRealTI'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTI'] +=  $tet;
                
                //tarjetas válidas
                elseif($trt<=$tet):
                    $responsable[$u->getId()]['tarjetasValidas'] += 1;
                    $responsable[$u->getId()]['tiempoRealTV'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTV'] +=  $tet;
                    
                    
                endif;
                
                //calculo total tiempo real y estimado
                $tiempoReal +=$trt;   
                $tiempoEstimado +=$tet;
            }
            //fin tickets atendidos culminados
            

            ####################################################################


            //actividades culminadas
            $dql = "select x from ProyectoBundle:ProcesarActividad x join x.responsable mp join mp.miembroespacio me where me.usuario=:usuario and x.ubicacion=4 and  x.fin>=:fechaDesde and x.fin<=:fechaHasta"; 
            $query = $em->createQuery($dql);
            $query->setParameter('usuario',$u);
            $query->setParameter('fechaDesde',$fechaDesde);
            $query->setParameter('fechaHasta',$fechaHasta);
            $consulta['actividadesCerradas'] = $query->getResult();

            foreach ($consulta['actividadesCerradas'] as $tc) {

                //calculo tiempo real de actividad
                $trt=0;
                $tiempo=  explode("-",$tc->getTiemporeal());
                $trt +=$tiempo[0]/24;    //dias                
                $trt +=$tiempo[1];       //tiempoReal
                $trt +=$tiempo[2]/60;    //minutos                
                $trt +=$tiempo[3]/3600;   //segundos

                //calculo total tiempo estimado de actividad
                $tet=0;
                if($tc->getTipotiempo()=='2')
                    $tet +=$tc->getTiempoestimado();
                elseif($tc->getTipotiempo()=='3')
                    $tet +=$tc->getTiempoestimado()/60;

                //tarjetas retardadas
                if($trt>$tet):
                    $responsable[$u->getId()]['tarjetasRetardadas'] +=1;
                    $responsable[$u->getId()]['tiempoRealTR'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTR'] +=  $tet;
                    
                    
                //tarjeta inválidas si tiene menos de un minuto
                elseif($trt<($tet/2)):
                    $responsable[$u->getId()]['tarjetasInvalidas'] +=1;
                    $responsable[$u->getId()]['tiempoRealTI'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTI'] +=  $tet;
                
                //tarjetas válidas
                elseif($trt<=$tet):
                    $responsable[$u->getId()]['tarjetasValidas'] += 1;
                    $responsable[$u->getId()]['tiempoRealTV'] +=  $trt;
                    $responsable[$u->getId()]['tiempoEstimadoTV'] +=  $tet;
                    
                    
                endif;
                
                //calculo total tiempo real y estimado total
                $tiempoReal +=$trt;   
                $tiempoEstimado +=$tet;
            }
            
            //tiempos y variables generales
            $responsable[$u->getId()]['tiempoReal'] += $tiempoReal;
            $responsable[$u->getId()]['tiempoEstimado'] += $tiempoEstimado;
            $responsable[$u->getId()]['tiempoAhorrado']=$responsable[$u->getId()]['tiempoEstimadoTV']-$responsable[$u->getId()]['tiempoRealTV'];
            
            $validas=$responsable[$u->getId()]['tarjetasValidas'];
            $invalidas=$responsable[$u->getId()]['tarjetasInvalidas']+$responsable[$u->getId()]['tarjetasRetardadas'];
            
            
            if($validas!=0)           
                $mejorPeorConTarjeta[]=array('idMe'=>$u->getId(),'tarjetasValidas'=>$responsable[$u->getId()]['tarjetasValidas'],'tarjetasInvalidas'=>($responsable[$u->getId()]['tarjetasRetardadas']+$responsable[$u->getId()]['tarjetasInvalidas']));
            elseif($validas==0)
                $mejorPeorSinTarjeta[]=array('idMe'=>$u->getId(),'tarjetasValidasTotales'=>$responsable[$u->getId()]['tarjetasValidas'] - ($responsable[$u->getId()]['tarjetasRetardadas']+$responsable[$u->getId()]['tarjetasInvalidas']),'tarjetasValidas'=>$responsable[$u->getId()]['tarjetasValidas'],'tarjetasInvalidas'=>($responsable[$u->getId()]['tarjetasRetardadas']+$responsable[$u->getId()]['tarjetasInvalidas']));
        
        endif;    
        }
        // ordeno array
        $a=$mejorPeorConTarjeta;
        foreach ($a as $clave => $fila) {            
            $tarjetasValidas[$clave] = $fila['tarjetasValidas'];
        }

        //ordeno cantidad positiva de tarjetas validas menos tarjetas retrasadas e invalidas
        if(!empty($mejorPeorConTarjeta))
            array_multisort($tarjetasValidas, SORT_DESC,SORT_NUMERIC, $mejorPeorConTarjeta);
        $responsable['mejorPeorConTarjeta']=$mejorPeorConTarjeta;
        $responsable['mejorPeorSinTarjeta']=$mejorPeorSinTarjeta;

        return $responsable;
    }
    
}
