<?php

namespace Administracion\DatatableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function funcionPrincipalAction(Request $request,$bundle,$entidad,$joins,$where,$order)
    {
        $this->bundle               =$bundle;
        $this->entidad              =$entidad;
        $this->joins                =$joins;
        $this->where                =$where;
        $this->order                =$order;
        
        $em = $this->getDoctrine()->getManager();
        $data=$request->request->all();
        
        $columnasBD=$this->validarColumnas($data['columns']);
        $camposFiltro=$this->columnasFiltro($columnasBD);
        
        
        $columnasSelect=$this->columnasSelect($columnasBD);
        $entity=$this->dqlPrincipal($data,$camposFiltro,$columnasBD,$columnasSelect);

        //encabezado json
        $arrayEntity['draw']=$data['draw'];
        $arrayEntity['recordsTotal']=$this->cantidadRegistros();
        $arrayEntity['recordsFiltered']=$this->cantidadFiltrada;
        //genero json
        $arrayEntity=$this->generarJson($entity,$arrayEntity,$columnasBD);
        
        ///retorno json
        return new JsonResponse($arrayEntity);
    }
    
    //verifico cuales columnas enviadas desde el ajax se utilizaran en las consultas
    public function validarColumnas($columnas) {
        $columnasBD=array();$cont=0;
        foreach ($columnas as $c) {
            $prefijo=substr($c['data'], 0,1);
            if($prefijo=='s'):
                $columnasBD[$cont]['nombre']=substr($c['data'], 1);
                $columnasBD[$cont]['tipo']=$c['name'];
            endif;
            $cont++;
        }
        
        return $columnasBD;
    }
    
    public function columnasFiltro($columnasBD) {
        
        //COLUMNAS EJ: x.primerNombre like :x0
        $filtro=null;
        foreach ($columnasBD as $cBD) {

            if($cBD['tipo']=='string')
                $filtro .='LOWER('.substr($cBD['nombre'],0,1).'.'.substr($cBD['nombre'],1).') like :'.substr($cBD['nombre'],1).' or '; 
            else
                $filtro .=substr($cBD['nombre'],0,1).'.'.substr($cBD['nombre'],1).' = :'.substr($cBD['nombre'],1).' or '; 
        }
        $filtro=  substr($filtro,0, -4);
        
        return $filtro;
    }
    
    public function generarJson($entity,$arrayEntity,$columnasBD) {

        $arrayEntity['data']=[];
        foreach ($entity as $e) {
            
            $arrayGet=array();
            foreach ($columnasBD as $columnas) {
                $get=substr($columnas['nombre'],1);
                
                if($columnas['tipo']=='date'):
                    $arrayGet['s'.$columnas['nombre']]=$e[$get]->format("d-m-Y");
                elseif($columnas['tipo']=='time'):
                    $arrayGet['s'.$columnas['nombre']]=$e[$get]->format("G:i:s");
                else:
                    $arrayGet['s'.$columnas['nombre']]=$e[$get];
                endif;
            }
            
            $arrayEntity['data'][]=$arrayGet;
        }

        return $arrayEntity;
    }
    
    public function dqlPrincipal($data,$camposFiltro,$columnasBD,$columnasSelect) {
        
        $buscar=null;
        
        if($data['search']['value']!= null )
            $buscar=$data['search']['value'];
        
        $start=$data['start'];
        $length=$data['length'];
        
        $em = $this->getDoctrine()->getManager();
        
        if($this->where!=' ')
            $dql = "select ".$columnasSelect." from ".$this->bundle.":".$this->entidad." x ".$this->joins." where (".$camposFiltro.") and ".$this->where." ".$this->order;
        else
            $dql = "select ".$columnasSelect." from ".$this->bundle.":".$this->entidad." x ".$this->joins." where (".$camposFiltro.") ".$this->order;

        $query = $em->createQuery($dql);
        
        $buscar=  explode(" ", $buscar);
        
        foreach ($columnasBD as $columnas) {
            
            foreach ($buscar as $b) {

                if($columnas['tipo']=='string')
                    $query->setParameter(substr($columnas['nombre'],1),'%'.strtolower($b).'%');
                elseif($columnas['tipo']=='integer')
                    $query->setParameter(substr($columnas['nombre'],1),(int) $b);

                elseif($columnas['tipo']=='date')
                    $query->setParameter(substr($columnas['nombre'],1),null);

                elseif($columnas['tipo']=='time')
                    $query->setParameter(substr($columnas['nombre'],1),null);

                elseif($columnas['tipo']=='boolean')
                    $query->setParameter(substr($columnas['nombre'],1),null);
            }
        }

        $entitySinFiltro = $query->getResult();
        $this->cantidadFiltrada=count($entitySinFiltro);
        
        $query->setFirstResult($start);
        $query->setMaxResults($length);
        $entity = $query->getResult();

        return $entity;
    }
    
    public function cantidadRegistros() {
        $em = $this->getDoctrine()->getManager();
        if($this->where!=' ')
            $dql = "select count(x) from ".$this->bundle.":".$this->entidad." x ".$this->joins." where ".$this->where;
        else
            $dql = "select count(x) from ".$this->bundle.":".$this->entidad." x ".$this->joins;
        
        $query = $em->createQuery($dql);
        $entityCantidad = $query->getResult();
        return $entityCantidad[0][1];
    }
    
    public function columnasSelect($columnasBD) {
        
        //COLUMNAS EJ: x.primerNombre like :x0
        $select=null;
        foreach ($columnasBD as $cBD) {
                 
                $select .=substr($cBD['nombre'],0,1).'.'.substr($cBD['nombre'],1).','; 
        }
        $select=  substr($select,0, -1);
        
        return $select;
    }
    
    public function indexAction($name)
    {
        return $this->render('DatatableBundle:Default:index.html.twig', array('name' => $name));
    }
}