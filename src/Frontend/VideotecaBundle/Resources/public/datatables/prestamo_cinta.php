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
    $aColumns = array( 'id','prestamista', 'solicitante', 'codigo', 'factura', 'fecha_prestamo','status_id');
    $bColumns = array( 'c.id','p.user_prestamista', 'p.user_solicitante', 'c.codigo', 'p.factura', 'dp.fecha_prestamo','c.status_id');
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "c.id";
    /* DB table to use*/
    $sTable = "  videoteca.cinta c,videoteca.detalleprestamo dp,videoteca.prestamo p,usuarios.perfil up";
     
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
 /*   if ( $_GET['sSearch'] != "" )
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
     */
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

    if($sWhere=='') $sWhere='WHERE dp.id_prestamo = p.id and dp.id_cinta = c.id and p.user_prestamista=up.id and p.user_solicitante=up.id';
    else $sWhere .=' dp.id_prestamo = p.id and dp.id_cinta = c.id and p.user_prestamista=up.id and  p.user_solicitante=up.id';
  
    $sQuery = "
        SELECT c.id,up.primer_nombre || ' ' || up.primer_apellido as prestamista ,up.primer_nombre || ' ' || up.primer_apellido as solicitante,c.codigo,p.factura,dp.fecha_prestamo,c.status_id

        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
 

    $rResult = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
     

    $sQuery = "
        SELECT c.id,up.primer_nombre || ' ' || up.primer_apellido as prestamista ,up.primer_nombre || ' ' || up.primer_apellido as solicitante,c.codigo,p.factura,dp.fecha_prestamo,c.status_id

        FROM   $sTable
        $sWhere
    ";

    $rResultTotal = pg_query( $gaSql['link'], $sQuery ) or die(pg_last_error());
    $iTotal = pg_num_rows($rResultTotal);
    pg_free_result( $rResultTotal );
         
    if ( $sWhere != "" )
    {
        $sQuery = "
            SELECT c.id,up.primer_nombre || ' ' || up.primer_apellido as prestamista ,up.primer_nombre || ' ' || up.primer_apellido as solicitante,c.codigo,p.factura,dp.fecha_prestamo,c.status_id

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

            if($i==6)
                $status=$aRow[ $aColumns[$i]];

                $row[] = $aRow[ $aColumns[$i] ];
          
        }
            if ($status==1){
               $row[6] = "<a class='btn btn-default' role='button' href='#' disabled >Disponible</a>";
            }else{
               $row[6] ="<a class='btn btn-default' role='button' href='".$id."/devolucion'>Devolucion</a>"; 
            }
            
        
        $output['aaData'][] = $row;
    }
     


    echo json_encode( $output );
     
    // Free resultset
    pg_free_result( $rResult );
     
    // Closing connection
    pg_close( $gaSql['link'] );

//http://localhost/sait/web/libs/datatables/avila.php?sEcho=1&iDisplayStart=0&iDisplayLength=10&bSortable=false&sSearch=&bSearchable_0=false&bSearchable_1=false&bSearchable_2=false&bSearchable_3=false&bSearchable_4=false&bSearchable_5=false