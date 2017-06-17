<?php
namespace Progis\PrincipalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class AvisoTarjetaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('progis:avisoTarjeta')
            ->setDescription('Avisa cuando a una tarjeta se le avaba el tiempo')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Explicamos el significado del argumento');
    }

    public function calculaCuentaRegresiva($p) {
       
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

                $cuentaregresiva=$intervalo->d*86400+$intervalo->h*3600+$intervalo->i*60+$intervalo->s;
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

                    //$cuentaregresiva=str_pad($intervalo->d,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->h,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->i,2,"0",STR_PAD_LEFT).'-'.str_pad($intervalo->s,2,"0",STR_PAD_LEFT);
                    $cuentaregresiva=$intervalo->d*86400+$intervalo->h*3600+$intervalo->i*60+$intervalo->s;
                }
  
        }
        return $cuentaregresiva;
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        
        $ticket=  $em->getRepository('TicketBundle:ProcesarTicket')->findByUbicacion(2);
        $actividad=  $em->getRepository('ProyectoBundle:ProcesarActividad')->findByUbicacion(2);
        
        foreach ($ticket as $t) {

            if($t->getCorreoretardo()==false):
                if($t->getTipotiempo()==2)
                    $TiempoEstimado=$t->getTiempoestimado()*3600;
                else if($t->getTipotiempo()==3)
                    $TiempoEstimado=$t->getTiempoestimado()*60;

                $tiemporRestanteSegundos=$this->calculaCuentaRegresiva($t);

                if(($tiemporRestanteSegundos<=$TiempoEstimado*0.20)):
                    $funcion=$this->getContainer()->get("funcionModelo");    
                    $correo=$funcion->correo('Tarjeta por culminar','aplicaciones',array($t->getResponsable()->getUsuario()->getUser()->getUsername().'@telesurtv.net'),"<div align='center' style='font-size:18px;'><div style='font-size:30px;color:red;font-weight:bold;'>ALERTA</div><br><br><span style='color:red;font-weight:bold;'>Atención!</span> Tienes un ticket que está a punto de culminar, ingresa <a href='http://aplicaciones.telesurtv.net/sait/web/progis/ticket/procesarTicket'>aquí.</a></div>");
                    $this->getContainer()->get('mailer')->send($correo);

                    $t->setCorreoretardo(true);
                    $em->flush();
                endif; 
            endif;

        }

        foreach ($actividad as $t) {

            if($t->getCorreoretardo()==false):
                if($t->getTipotiempo()==2)
                    $TiempoEstimado=$t->getTiempoestimado()*3600;
                else if($t->getTipotiempo()==3)
                    $TiempoEstimado=$t->getTiempoestimado()*60;

                $tiemporRestanteSegundos=$this->calculaCuentaRegresiva($t);

                if(($tiemporRestanteSegundos<=$TiempoEstimado*0.20)):
                    $funcion=$this->getContainer()->get("funcionModelo");    
                    $correo=$funcion->correo('Tarjeta por culminar','aplicaciones',array($t->getResponsable()->getMiembroEspacio()->getUsuario()->getUser()->getUsername().'@telesurtv.net'),"<div align='center' style='font-size:18px;'><div style='font-size:30px;color:red;font-weight:bold;'>ALERTA</div><br><br><span style='color:red;font-weight:bold;'>Atención!</span> Tienes una actividad que está a punto de culminar, ingresa <a href='http://aplicaciones.telesurtv.net/sait/web/progis/proyecto/procesarActividad/".$t->getObjetivo()->getId()."'>aquí.</a></div>");
                    $this->getContainer()->get('mailer')->send($correo);

                    $t->setCorreoretardo(true);
                    $em->flush();
                endif; 
            endif;

        }

    }
}