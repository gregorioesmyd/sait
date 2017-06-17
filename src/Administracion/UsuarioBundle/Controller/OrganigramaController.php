<?php

namespace Administracion\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrganigramaController extends Controller
{
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $nivel = $em->getRepository('UsuarioBundle:Nivelorganizacional')->findAll(array(),array('codigo'=>'asc'));
        
        $arrayNiveles=array();$count=0;
        foreach ($nivel as $n) {
            $arrayNiveles[strlen($n->getCodigo())][$count]['codigo']=$n->getCodigo();
            $arrayNiveles[strlen($n->getCodigo())][$count]['descripcion']=$n->getDescripcion();
            $count++;
        }
        
        
        return $this->render('UsuarioBundle:Organigrama:index.html.twig',array('nivel'=>$nivel,'arrayNiveles'=>$arrayNiveles));        
    }
    
    public function index2Action()
    {
        $em = $this->getDoctrine()->getManager();
        $nivel = $em->getRepository('UsuarioBundle:Nivelorganizacional')->findAll(array(),array('codigo'=>'asc'));
        
        /*
        $dql = "select x from UsuarioBundle:Perfil x join x.user u join x.nivelorganizacional no where no.codigo like :codnivel and u.isActive=true order by no.codigo ASC, x.jerarquia ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('codnivel','%01%');
        $perfil = $query->getResult();*/




        $arrayNiveles=array();$count=0;
        foreach ($nivel as $n) {
            $arrayNiveles[strlen($n->getCodigo())][$count]['codigo']=$n->getCodigo();
            $arrayNiveles[strlen($n->getCodigo())][$count]['descripcion']=$n->getDescripcion();
            $count++;
        }
        
        
        return $this->render('UsuarioBundle:Organigrama:index2.html.twig',array('nivel'=>$nivel,'arrayNiveles'=>$arrayNiveles));        
    }
    public function ajaxAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        //$perfil= $em->getRepository('UsuarioBundle:Perfil')->findBy(array('nivelorganizacional'=>$id));
        
        $dql = "select x from UsuarioBundle:Perfil x join x.user u join x.nivelorganizacional no where no.id= :idnivel and u.isActive=true order by x.jerarquia ASC";
        $query = $em->createQuery($dql);
        $query->setParameter('idnivel',$id);
        $perfil = $query->getResult();
                        
        $html= $this->render('UsuarioBundle:Organigrama:info.html.twig',array('perfil'=>$perfil));        
        
        echo $html->getContent();
        die;
    }

}