<?php
namespace Frontend\TruequesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
 
class TruequesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('trueques:EstadoTrueques')
            ->setDescription('Genera archivo json para cumpleañeros del dia y el año en la intranet')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Explicamos el significado del argumento');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager("default");
        
        $truequesModelo=$this->getContainer()->get("TruequesModelo");   
        $truequesModelo->VerificaFinPublicacion();
        
    }
}