<?php

namespace Progis\PrincipalBundle\Modelo;
use Progis\PrincipalBundle\Entity\Bitacora;

class bitacoraModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }


    //se llama desde el index de la actividad
    public function guarda_bitacora($modulo,$descripcion,$responsable,$idmodulo,$nivelorganizacional){
        $em=$this->em;

        $modulo = $em->getRepository('PrincipalBundle:Modulo')->find($modulo);         
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $a=new Bitacora;
        $a->setModulo($modulo);
        $a->setResponsable($responsable);
        $a->setNivelorganizacional($nivelorganizacional);
        $a->setIdmodulo($idmodulo);
        $a->setFechahora($fa);
        $a->setDescripcion($descripcion);
        $em->persist($a);
        $em->flush(); 
    }
}