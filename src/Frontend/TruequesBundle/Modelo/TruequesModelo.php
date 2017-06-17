<?php

namespace Frontend\TruequesBundle\Modelo;
use Symfony\Component\HttpFoundation\JsonResponse;

class TruequesModelo
{
    
    function __construct($em) {
        $this->em = $em;
    }


    //se llama desde el index de la actividad
    public function VerificaFinPublicacion(){
        $em =  $this->em;
        $fa=new \DateTime(\date("d-m-Y G:i:s"));
        
        $dql = "select MP from TruequesBundle:MisProductos MP join MP.status E where MP.fecha_fin_publicacion < :fa and E.nombre!='Inactivo' and E.nombre!='Expirado'";
        $query = $em->createQuery($dql);
        $query->setParameter('fa', $fa);
        $misPublicacionesVencidas = $query->getResult();   
        
        $estatus=$em->getRepository('TruequesBundle:Status')->findByNombre('Expirado');
        foreach ($misPublicacionesVencidas as $v) {
            $v->setStatus($estatus[0]);
        }
        $em->flush();
    }
}