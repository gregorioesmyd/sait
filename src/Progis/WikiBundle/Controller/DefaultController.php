<?php

namespace Progis\WikiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('WikiBundle:Default:index.html.twig', array('name' => $name));
    }
}
