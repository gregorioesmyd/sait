<?php
namespace Frontend\TruequesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Administracion\UsuarioBundle\Entity\User;
use Administracion\UsuarioBundle\Entity\Perfil;

class BaseController extends Controller
{
    protected function currentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    protected function findPerfilUser(User $user)
    {
    	$em = $this->getDoctrine()->getManager();

        $perfil = $em->getRepository('UsuarioBundle:Perfil')->find($user->getId());

        return $perfil;
    }

    protected function sendMail($subject, $from, $to, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body, 'text/html');

        $this->get('mailer')->send($message);
    }

    protected function getStatus($nombre)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('TruequesBundle:Status')->findOneByNombre($nombre);
    }

}