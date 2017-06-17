<?php

namespace Administracion\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\Mapping as ORM;
use Administracion\UsuarioBundle\Resources\Misclases\Funcion;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        //VERIFICAR SI EL USUARIO ESTA LOGUEADO
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
          throw new AccessDeniedException();
        }

        $IdUsuario = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UsuarioBundle:Perfil')->find($IdUsuario);
        
        return $this->render('UsuarioBundle:Default:index.html.twig', array('usuario'=>$entity)
        );
    }
    public function loginAction()
    {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
        SecurityContext::AUTHENTICATION_ERROR,
        $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        
        
        return $this->render('UsuarioBundle:Default:login.html.twig', array(
        'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
        'error'
        => $error,'mensaje'=>''
        ));
    }

    public function sincronizacionAction()
    {
        return $this->render('UsuarioBundle:Default:sincronizacion.html.twig');
    }
}