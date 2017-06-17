//cuando la tarjeta es puesta en nuevo o dependencia
function pausa(respuesta,idproceso,hasta){
    if(respuesta.retardo==true)clase="danger"
    else clase="success"

    //si una tarjeta nueva fue puesta en dependencia
    if(respuesta.tiemporeal==null && hasta=='dependencia'){
        $( "#contenido_dinamico"+idproceso ).append("<div id='tiemporeal"+idproceso+"' class='label label-info'></div>")
        $( "#tiemporeal"+idproceso ).html("NUEVA");
    }

    //si una tarjeta nueva que fue puesta en dependencia regresa a nuevo
    else if(respuesta.tiemporeal==null && hasta=='nuevo'){
        $( "#contenido_dinamico"+idproceso ).append("<div id='tiemporeal"+idproceso+"' class='label label-info'></div>")
        $( "#tiemporeal"+idproceso ).html("NUEVA");
    }

    //si la tarjeta ya fue puesta alguna vez en progreso
    else{
        $( "#contenido_dinamico"+idproceso ).append("<div id='tiemporeal"+idproceso+"' class='label label-"+clase+"'></div>")
        $( "#tiemporeal"+idproceso ).html(respuesta.tiemporeal);
    }

    //MUESTRO EL MODAL SI ESTA EN DEPENDENCIA
    if(hasta=='dependencia')
        muestramodal("dependencia",idproceso)
}

//cuando la tarjeta es puesta en proceso
function contador(cuentaregresiva,idproceso){
    $( "#contenido_dinamico"+idproceso ).append("<div style='width:130px;' class='bloqueContador' id='bloqueContador"+idproceso+"'><div style='display:none' id='counter"+idproceso+"'></div><div class='desc' id='descripcion_tiempo"+idproceso+"'><div>Días</div><div>Horas</div><div>Minutos</div><div>Segundos</div></div></div>")
    $('#counter'+idproceso).countdown({
        image: "/sait/web/libs/jquery-countdown/img/digits.png",
        startTime: cuentaregresiva
    })
    $( "#counter"+idproceso ).fadeIn("slow");
}

//muestra modales cuando culmina o depende
function muestramodal(ubi,idtarjeta){
    var url='/sait/web/progis/principal/procesar/validar'
    var bundleActual=$( "#bundleActual").val();
    
    $.ajax({
        url: url,
        data: bundleActual+"||"+ubi+"||"+idtarjeta,
        type: 'POST',
        dataType: 'json',
        success: function(respuesta){
            if(ubi=='culminado' && respuesta.infotarjeta!=null){
                $( "#modalCulminadoCuerpo").html(respuesta.infotarjeta)
                $('#myModalCulminado').modal("show")   

                if(bundleActual=='ticket')
                    $("#formCulminado").attr("action", "/sait/web/progis/ticket/cerrarTicket/"+respuesta.idtarjeta);
            }
            else if(ubi=='dependencia' && respuesta.infotarjeta!=null){
                $( "#modalDependenciaCuerpo").html(respuesta.infotarjeta)
                $('#myModalDependencia').modal("show")
                if(bundleActual=='ticket')
                    $("#formDependencia").attr("action", "/sait/web/progis/ticket/justificarTicket/"+respuesta.idtarjeta);
                else if(bundleActual=='proyecto')
                    $("#formDependencia").attr("action", "/sait/web/progis/proyecto/justificarActividad/"+respuesta.idtarjeta);
            }
        }
    });
}

//cuando la tarjeta es culminada
function culmina(respuesta,idproceso){
    if(respuesta.retardo==true)clase="danger"
    else clase="success"
    $( "#contenido_dinamico"+idproceso ).append("<div id='tiempofinal"+idproceso+"' class='label label-"+clase+"'></div>")

    $( "#tiempofinal"+idproceso ).html(respuesta.tiemporeal);

    muestramodal("culminado",idproceso)

}