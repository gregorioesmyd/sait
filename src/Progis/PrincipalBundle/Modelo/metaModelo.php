<?php

namespace Progis\PrincipalBundle\Modelo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class metaModelo extends Controller
{
    function __construct($em) {
        $this->em = $em;
    }
    
    public function estatus($metas){
        $em=  $this->em;
        $estatus=1;

        if(!empty($metas)):
            
            $nuevo=false;$proceso=false;$revision=false;$culminado=false;$dependencia=false;
            
            //recorro metas
            foreach ($metas as $m) {
                $metaActividad = $em->getRepository('PrincipalBundle:MetaActividad')->findByMeta($m->getId());

                //recorro las actividades de las metas
                if(!empty($metaActividad)):
                    foreach ($metaActividad as $ma) {
                        if($ma->getActividad()->getUbicacion()==1)
                            $nuevo=true;
                        if($ma->getActividad()->getUbicacion()==2)
                            $proceso=true;
                        if($ma->getActividad()->getUbicacion()==3)
                            $revision=true;
                        if($ma->getActividad()->getUbicacion()==4)
                            $culminado=true;
                        if($ma->getActividad()->getUbicacion()==5)
                            $$dependencia=true;
                    }
                    
                    if($proceso==true or $revision==true or $dependencia==true or ($nuevo==true and $culminado==true)):
                        $estatus = $em->getRepository('PrincipalBundle:Estatus')->find(2);
                    elseif($nuevo==false and $proceso==false and $revision==false and $culminado==true and $dependencia==false):
                        $estatus = $em->getRepository('PrincipalBundle:Estatus')->find(3);
                    else:
                        $estatus = $em->getRepository('PrincipalBundle:Estatus')->find(1);
                    endif;

                    $m->setEstatus($estatus);
                    $em->flush();
                    
                endif;
                //fin recorro las actividades de las metas
            }
        endif;
        
        $this->porcentaje($metas);
        
        //fin recorro metas
        return $estatus;
    }
    
    public function porcentaje($metas){
        $em=  $this->em;
        
        
        if(!empty($metas)):
            //recorro metas
            foreach ($metas as $m) {
                $porcentaje=0;
                $total=0;$enFecha=0;$fueraDeFecha=0;
            
                $metaActividad = $em->getRepository('PrincipalBundle:MetaActividad')->findByMeta($m->getId());

                //recorro las actividades de las metas
                if(!empty($metaActividad)):
                    foreach ($metaActividad as $ma) {
                        $total=$total+1;
                        //si se culmino entre la fecha propuesta de meta
                        if($ma->getActividad()->getUbicacion()==4 and $m->getFechadesde()->format("Y-m-d")<=$ma->getActividad()->getFin()->format("Y-m-d") and $m->getFechahasta()->format("Y-m-d")>=$ma->getActividad()->getFin()->format("Y-m-d"))
                            $enFecha=$enFecha+1;
                    }
                    $porcentaje=($enFecha*100)/$total;
                endif;
                //fin recorro las actividades de las metas
                $m->setPorcentaje(number_format($porcentaje,0));
                $em->flush();
            }
        endif;
    }
    
}