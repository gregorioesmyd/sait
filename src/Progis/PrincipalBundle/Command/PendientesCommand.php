<?php
namespace Progis\PrincipalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class PendientesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('progis:pendientes')
            ->setDescription('Descripción de lo que hace el comando')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Explicamos el significado del argumento');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        
        $miembros=  $em->getRepository('PrincipalBundle:Miembroespacio')->findAll();
        
        
        //esto lo hago para tener el perfil solo una vez por usuario 
        //por es miembro de varios espacios
        $perfiles=array();
        foreach ($miembros as $m) {
            $perfiles[$m->getUsuario()->getId()]=$m;
        }
        
        foreach ($perfiles as $p) {
            //verifico si esta en un dia de trabajo
            $jornada = $p->getJornadalaboral()->getDias();
            $dias=  explode('-', $jornada);
            
            //si el dia que estoy corriendo el cron el dia de trabajo de la persona
            if(in_array(date("N"), $dias)):

                //inicializo
                $proyectoModelo=$this->getContainer()->get("proyectoModelo");    

                //consulto
                $pendientes=$proyectoModelo->pendientes($p->getUsuario());

                if(!empty($pendientes['metas'])):
                    $html=$this->getContainer()->get('templating')->render('PrincipalBundle:Correo:pendientes.html.twig',array('pendientes'=>$pendientes));

                    //envio correo
                    $funcion=$this->getContainer()->get("funcionModelo");    
                    $correo=$funcion->correo('Actividades Pendientes','Progis_Informacion',array($p->getUsuario()->getUser()->getUsername().'@telesurtv.net'),$html);
                    $this->getContainer()->get('mailer')->send($correo); 
                endif;
            endif;
        }
        
        
        

            
    }
}