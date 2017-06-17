<?php

namespace Frontend\ProtocoloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	return $this->render('ProtocoloBundle:Default:index.html.twig');
    }
}
