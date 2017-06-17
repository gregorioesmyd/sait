<?php

namespace Frontend\FormularioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FormularioBundle:Default:index.html.twig', array('name' => $name));
    }
}
