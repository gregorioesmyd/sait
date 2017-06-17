function guarda_orden(obj,ui){
    //id de tarjeta, responsable y tipo de tarjeta
    var data=ui.item.attr('id').split("-")
    var idproceso=data[0]
    var idresponsable=data[1]
    var tipotarjeta=data[2]
        
    var orden = $(obj).sortable('toArray').toString();
    var recibe=null;
    if(orden!=''){
        var url='/sait/web/progis/principal/procesar/priometa/orden'
        var orden = $(obj).sortable('toArray').toString();
        $.ajax({
            url: url,
            data: orden+"||"+tipotarjeta,
            type: 'POST',
            dataType: 'json',
            success: function(respuesta){
                
                for (var i=0;i<=respuesta.numero.length;i++){
                    recibe=respuesta.numero[i].split("-");
                    $("#prioridad-"+recibe[0]+"-"+tipotarjeta).html("<b>Prioridad:</b> "+recibe[1]+"&nbsp;")
                }
            }
        });
    }

}

function guarda_ubicacion(obj,ui){

    //definiciones
        var desde=ui.sender.attr("id")
        var hasta=obj.id

        //id de tarjeta y responsable
        var data=ui.item.attr('id').split("-")
        var idproceso=data[0]
        var idresponsable=data[1]
        var tipotarjeta=data[2] //si es proyecto o ticket
        
        var idmeta=$("#idmeta").val()
        
        var ubi=null;
        if(hasta=='priometa'){
            ubi=2;
            $( "."+idproceso+"-"+idresponsable ).removeClass("col-md-6");
        }else{
            ubi=1;
            $( "."+idproceso+"-"+idresponsable ).addClass("col-md-6");
            $("#prioridad-"+idproceso+"-"+tipotarjeta).html(" ")
        }
            
        var url='/sait/web/progis/principal/procesar/ubicacion/priometa/'+ubi
    //fin definiciones

    $.ajax({
        url: url,
        data: desde+"-"+hasta+"-"+idproceso+"-"+tipotarjeta+"-"+idmeta,
        type: 'POST',
        dataType: 'json',
        success: function(respuesta){
            
            post_guardado(respuesta,ui,hasta,idproceso,idresponsable,tipotarjeta)
            
            /*
            if(error==false){
                if(hasta=='pendiente') pausa(respuesta,idproceso,hasta)
                else if(hasta=='priometa') contador(respuesta.cuentaregresiva,idproceso)
            }*/
        }
    });
}

function post_guardado(respuesta,ui,hasta,idproceso,idresponsable,tipotarjeta){
    var alerta=null;
    //*****************MENSAJES*******************************//
    //si es error coloco el id del alert para error sino para success
    if(respuesta.error==null){alerta="success";$("#tiempo").html(respuesta.tiempo)}
    else {
            alerta="danger"; 
            $(ui.sender).sortable('cancel');            
            $( "."+idproceso+"-"+idresponsable ).addClass("col-md-6");
            $("#prioridad-"+idproceso+"-"+tipotarjeta).html(" ") 
        }



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

    $( "#pendiente, #priometa" ).disableSelection();


    $("#pendiente").sortable({
        //si el elemento se movio desde la misma
        update: function( event, ui ) {
            //guarda_orden(this)
        },
        //si el elemento llego desde otra
        receive: function( event, ui ) {
            guarda_ubicacion(this,ui)
        }
    });
    $("#priometa").sortable({
        update: function( event, ui ) {
            guarda_orden(this,ui)
        },
        receive: function( event, ui ) {
            
            guarda_ubicacion(this,ui)
        }
    });

});
    

