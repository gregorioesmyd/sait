<?php

namespace Solicitudes\SolicitudesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SolicitudesBundle:Default:index.html.twig', array('name' => $name));
    }
}
