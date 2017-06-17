<?php

namespace Administracion\DatatableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DatatableController extends Controller
{
    
    public function principalAction($bundle,$entidad,$joins,$where)
    {
        $em = $this->getDoctrine()->getManager();
        
        
        

        
        
    
        die;
    }
    
    public function paginar(){
        //paginando
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayLength'] )." OFFSET ".
                intval( $_GET['iDisplayStart'] );
        }
        
        echo $sLimit;   
    }
    
    public function ordenar(){
        //ordenando
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                        ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc').", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }      
    }
    
    public function filtrar(){
        $sWhere = "";
         if ( $_GET['sSearch'] != "" )
         {
             $sWhere = "WHERE (";
             for ( $i=0 ; $i<count($aColumns) ; $i++ )
             {
                 if ( $_GET['bSearchable_'.$i] == "true" )
                 {
                     $sWhere .= $aColumns[$i]." ILIKE '%".pg_escape_string( $_GET['sSearch'] )."%' OR ";
                 }
             }
             $sWhere = substr_replace( $sWhere, "", -3 );
             $sWhere .= ")";
         }
    }
    
}
