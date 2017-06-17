<?php

namespace Frontend\EstudioCabinaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($tipo)
    {
        
        $em = $this->getDoctrine()->getManager();
        if($tipo == "estudios" or $tipo=='1'){
            $id = 1;
        }elseif($tipo == "cabinas" or $tipo=='2'){
            $id= 2;
        }elseif($tipo == "salas" or $tipo=='3'){
            $id= 3;
        }else{
           return $this->redirect($this->generateUrl('estudio_cabina_principal'));
        }
        $entities = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->findByTipo($id);
        
        return $this->render('EstudioCabinaBundle:Default:index.html.twig', array(
            'entity' => $entities,
            'tipo'   => $id
        ));        
    }
    
    public function principalAction()
    {
        
      //  $em = $this->getDoctrine()->getManager();
        
    //    $entities = $em->getRepository('EstudioCabinaBundle:EstudiosCabinas')->findByTipo($id);
        
        return $this->render('EstudioCabinaBundle:Default:principal.html.twig', array());        
    }
}
