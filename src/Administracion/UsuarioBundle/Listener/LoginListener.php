<?php

namespace Administracion\UsuarioBundle\Listener;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.1.0+
// use Symfony\Bundle\DoctrineBundle\Registry as Doctrine; // for Symfony 2.0.x

/**
 * Custom login listener.
 */
class LoginListener
{
	/** @var \Symfony\Component\Security\Core\SecurityContext */
	private $securityContext;
	
	/** @var \Doctrine\ORM\EntityManager */
	private $em;
        
        private $kernel;
	
	/**
	 * Constructor
	 * 
	 * @param SecurityContext $securityContext
	 * @param Doctrine        $doctrine
	 */
	public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Kernel $kernel)
	{
		$this->securityContext = $securityContext;
		$this->em              = $doctrine->getEntityManager();
                $this->kernel = $kernel;
	}
	
	/**
	 * Do the magic.
	 * 
	 * @param InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
	{
            $user = $event->getAuthenticationToken()->getUser();
            if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
                //inicializo roles progis
                if($this->securityContext->isGranted('ROLE_PROGIS')):
                    $em = $this->em;
                    $perfil =  $em->getRepository('UsuarioBundle:Perfil')->find($user->getId());
                    $admin=false;if($this->securityContext->isGranted('ROLE_PROGIS_ADMIN'))$admin=true;

                    $seguridad=$this->kernel->getContainer()->get("seguridadModelo");
                    $seguridad->initSession($perfil,$this->kernel->getContainer()->get('session'),$admin);

                endif;

            }
		
	}
}