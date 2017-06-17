function guarda_orden(obj){
    var orden = $(obj).sortable('toArray').toString();
    var bundleActual=$( "#bundleActual").val();

    if(orden!=''){
        var url='/sait/web/progis/principal/procesar/orden'
        var orden = $(obj).sortable('toArray').toString();
        $.ajax({
            url: url,
            data: orden+"||"+bundleActual,
            type: 'POST',
        });
    }else return;
}


function verificaSession(){
    
    //var ruta=window.location
    //alert(ruta.split("."))
    
    var url='/sait/web/progis/principal/verificaSession';
    $.ajax({
        url: url,
        dataType: 'json',
        success: function(respuesta){
            if(respuesta.session==false){
                alert("La session se ha vencido.")
                location.reload()
            }
        }
    });
}

function guarda_ubicacion(obj,ui){
    verificaSession()
    //definiciones
        var desde=ui.sender.attr("id")
        var hasta=obj.id

        var ubi=null;
        if(hasta=='nuevo')ubi=1
        else if(hasta=='proceso')ubi=2
        else if(hasta=='revision')ubi=3
        else if(hasta=='culminado')ubi=4
        else if(hasta=='dependencia')ubi=5

        var url='/sait/web/progis/principal/procesar/ubicacion/'+ubi
        var bundleActual=$( "#bundleActual").val();

        //recibo el id del panel
        var ids=ui.item.attr('id').split("-")
        var idproceso=ids[0]
        var idresponsable=ids[1]
    //fin definiciones

    $.ajax({
        url: url,
        data: hasta+"-"+idproceso+"-"+desde+"||"+bundleActual,
        type: 'POST',
        dataType: 'json',
        success: function(respuesta){
            error=post_guardado(respuesta,ui,hasta,idproceso,idresponsable)
            if(error==false){
                if(hasta=='nuevo' || hasta=='dependencia') pausa(respuesta,idproceso,hasta)
                else if(hasta=='proceso') contador(respuesta.cuentaregresiva,idproceso)
                else if(hasta=='revision' || hasta=='culminado') culmina(respuesta,idproceso)
            }
        }
    });
}

function post_guardado(respuesta,ui,hasta,idproceso,idresponsable){
    var clase=null;
    var alerta=null;
    //*****************MENSAJES*******************************//
    //si es error coloco el id del alert para error sino para success
    if(respuesta.error==null){
        alerta="success";
        if(hasta=='nuevo') clase='primary';
        else if(hasta=='proceso') clase='warning';
        else if(hasta=='revision') clase='info';
        else if(hasta=='culminado') clase='success';
        else if(hasta=='dependencia') clase='danger';
        $("#"+idproceso+"-"+idresponsable).attr('class', 'panel panel-'+clase);
    }
    else {alerta="danger"; $(ui.sender).sortable('cancel'); }



    //muestro el alert si es error o no
    $("#success").hide()
    $("#danger").hide()
    $( ".sms"+alerta ).empty();
    $("<div>"+respuesta.sms+"</div>").appendTo(".sms"+alerta);
    $("#"+alerta).slideDown( "slow" );
    setTimeout(function() {
        $("#"+alerta).slideUp( "slow" );
    },10000);
    //fin alert

    if(respuesta.error!=null) return true;

    //***************LIMPIAR CONTADOR*************************//
    //quito contador cuando ubico en otro lugar que no es proceso
    $( "#bloqueContador"+idproceso ).remove();
    $( "#tiemporeal"+idproceso ).remove();
    $( "#tiempofinal"+idproceso ).remove();
    //fin limpiar

    return false;
}


$(function() {
    $( "ul.droptrue" ).sortable({
      connectWith: "ul"
    });

    $( "ul.dropfalse" ).sortable({
      connectWith: "ul",
      dropOnEmpty: false
    });

    $( "#nuevo, #proceso, #revision, #culminado, #dependencia" ).disableSelection();


    $("#nuevo").sortable({
        //si el elemento se movio desde la misma
        update: function( event, ui ) {
            guarda_orden(this)
        },
        //si el elemento llego desde otra
        receive: function( event, ui ) {
            guarda_ubicacion(this,ui)
        }
    });
    $("#proceso").sortable({
        update: function( event, ui ) {
            guarda_orden(this)
        },
        receive: function( event, ui ) {
            guarda_ubicacion(this,ui)
        }
    });

    $("#revision").sortable({
        update: function( event, ui ) {
            guarda_orden(this)
        },
        receive: function( event, ui ) {
            guarda_ubicacion(this,ui)
        }
    });

    $("#culminado").sortable({
        update: function( event, ui ) {
            guarda_orden(this)
        },
        receive: function( event, ui ) {
            var ids=ui.item.attr('id').split("-")
            var idproceso=ids[0]
            var idresponsable=ids[1]

            var bundleActual=$( "#bundleActual").val();
            var idUsuarioActual=$( "#idUsuarioActual").val();

            if((ui.sender.attr("id")=='revision' && bundleActual=='proyecto') || (bundleActual=='ticket')){
                confirma=confirm("¿Esta seguro que desea culminar? No podrá devolver la tarjeta.")
                if(confirma==false){$(ui.sender).sortable('cancel');return;}
            }

            guarda_ubicacion(this,ui)
        }
    });

    $("#dependencia").sortable({
        update: function( event, ui ) {
            guarda_orden(this)
        },
        receive: function( event, ui ) {
            //valido para que no muestre alerta si viene de culminado
            if(ui.sender.attr("id")!='culminado' && ui.sender.attr("id")!='revision'){
                confirma=confirm("¿Esta seguro que desea colocar la tarjeta en dependencia? No podrá devolver la tarjeta.")
                if(confirma==false){$(ui.sender).sortable('cancel');return;}
            }

            guarda_ubicacion(this,ui)
        }
    });
});
    

