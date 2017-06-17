<?php
    /*
     * Script:    DataTables server-side script for PHP and PostgreSQL
     * Copyright: 2010 - Allan Jardine
     * License:   GPL v2 or BSD (3-point)
     */
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
     
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array( 'id','codigo', 'tipo', 'formato', 'duracion', 'evento', 'marca','documentalista','slug');
    $bColumns = array( 'c.id','c.codigo', 'c.tipocinta_id', 'c.formato_id', 'c.duracion_id', 'c.evento_id', 'c.marca_id','c.user_id','tc.slug');
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "c.id";
    /* DB table to use*/
    $sTable = "videoteca.cinta c,videoteca.tipocinta tc,videoteca.formato f,videoteca.duracion d,videoteca.evento e,videoteca.marca m,usuarios.perfil up";
     
    /* Database connection information */
    $gaSql['user']       = "postgres";
    $gaSql['password']   = "12345678";
    #$gaSql['password']   = "postgres";
    $gaSql['db']         = "sait";
    $gaSql['server']     = "localhost";
     
     
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
     
    /*
     * DB connection
     */
    $gaSql['link'] = pg_connect(
             " host=".$gaSql['server'].
             " dbname=".$gaSql['db'].
             " user=".$gaSql['user'].
             " password=".$gaSql['password']
    ) or die('Could not connect: ' . pg_last_error());
     
     
    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayLength'] )." OFFSET ".
            intval( $_GET['iDisplayStart'] );
    }
     
     
    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $bColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc').", ";
            }
        }
         
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
     
     
    /*
     * Filtering
     * NOTE This assumes that the field that is being searched on is a string typed field (ie. one
     * on which ILIKE can be used). Boolean fields etc will need a modification here.
     */
    $sWhere = "";
    if ( $_GET['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($bColumns) ; $i++ )
        {
            if ( $_GET['bSearchable_'.$i] == "true" )
            {
                $sWhere .= "CAST(".$bColumns[$i]." AS TEXT) ILIKE '%".pg_escape_string( $_GET['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ")";
    }
     
    /* Individual column filtering */
    for ( $i=0 ; $i<count($bColumns) ; $i++ )
    {
        if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $bColumns[$i]." ILIKE '%".pg_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }

    if($sWhere=='') $sWhere='WHERE c.user_id=up.id and c.tipocinta_id=tc.id and c.formato_id=f.id and c.evento_id=e.id and c.duracion_id=d.id and c.marca_id=m.id';
    else $sWhere .=' and c.user_id=up.id and c.tipocinta_id=tc.id and c.formato_id=f.id and c.evento_id=e.id and c.duracion_id=d.id and c.marca_id=m.id';
  
    $sQuery = "
        SELECT c.id,c.codigo as codigo,tc.nombre as tipo,f.descripcion_formato as formato,d.nombre as duracion,e.nombre as evento,m.descripcion_marca as marca,up.primer_nombre || ' ' || up.primer_apellido as documentalista, tc.slug as slug
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
 

    $rResult = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
     

    $sQuery = "
        SELECT c.id,c.codigo as codigo,tc.nombre as tipo,f.descripcion_formato as formato,d.nombre as duracion,e.nombre as evento,m.descripcion_marca as marca,up.primer_nombre || ' ' || up.primer_apellido as documentalista,tc.slug as slug
        FROM   $sTable
        $sWhere
    ";

    $rResultTotal = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
    $iTotal = pg_num_rows($rResultTotal);
    pg_free_result( $rResultTotal );
         
    if ( $sWhere != "" )
    {
        $sQuery = "
            SELECT c.id,c.codigo as codigo,tc.nombre as tipo,f.descripcion_formato as formato,d.nombre as duracion,e.nombre as evento,m.descripcion_marca as marca,up.primer_nombre || ' ' || up.primer_apellido as documentalista,tc.slug as slug
            FROM   $sTable
            $sWhere
        ";
        $rResultFilterTotal = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
        $iFilteredTotal = pg_num_rows($rResultFilterTotal);
        pg_free_result( $rResultFilterTotal );
    }
    else
    {
        $iFilteredTotal = $iTotal;
    }
     
     
     
    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
     
    // echo $iTotal; die;
    while ( $aRow = pg_fetch_array($rResult, null, PGSQL_ASSOC) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {   
            if($i==0)
                $id=$aRow[ $aColumns[$i]];

            if ( $aColumns[$i] == "version" )
            {
                /* Special output formatting for 'version' column */
                $row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] != ' ' )
            {
                    $tipocinta=$aRow[ $aColumns[$i]];
                    $row[] = $aRow[ $aColumns[$i] ];

                    if($i==8)
                    $row[8] = "<a class='btn btn-default' role='button' href='".$id."/edit/".$tipocinta."'>Editar</a>
                   <a class='btn btn-default' role='button' href='".$id."/show/".$tipocinta."'>Segmento</a>";    
            }
        }
        $output['aaData'][] = $row;
    }
     


    echo json_encode( $output );
     
    // Free resultset
    pg_free_result( $rResult );
     
    // Closing connection
    pg_close( $gaSql['link'] );
?>
