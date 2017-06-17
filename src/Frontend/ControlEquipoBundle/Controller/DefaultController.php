<?php

namespace Frontend\ControlEquipoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        //echo $date = '2014-10-01 10:10:59';
        //echo $fecha = $this->formatDate($date);
        return $this->render('ControlEquipoBundle:Default:index.html.twig', array());
    }
    
    public function formatDate($date)
    {
        \date_default_timezone_set("America/Caracas");
        //return \strtotime(\substr($date, 6,4)."-".\substr($date, 3,2)."-".\substr($date, 0,2)." ".\substr($date, 10,6))* 1000;
        return \strtotime($date) * 1000;
    }
    
    public function calendarioAction()
    {
        return $this->render('ControlEquipoBundle:Default:calendario.html.twig', array());
    }

}