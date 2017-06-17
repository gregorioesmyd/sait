$(function(){

	$('.timepicker').timepicker({
		timeFormat: 'HH:mm:ss'
	});

	$('.datepicker').datepicker({
		dateFormat: 'dd-mm-yy'
	});

	$('.chosen-select').chosen();

	$(document).on("click", "input[type='radio']",function(){
	      if ($('#frontend_protocolobundle_invitados_transportePersonal_1').is(':checked')){
	    	  $("#oculto").show();
	      } else {
	    	  $("#oculto").hide();
	      }
	   });

		 if ($('#frontend_protocolobundle_invitados_transportePersonal_1').is(':checked')){
	    	  $("#oculto").css('display','block');
	      }

	//si tengo admin no puedo tener consulta ni tecnico en ticket
        $("#frontend_protocolobundle_reunion_refrigerio_2").click(function(){
            $("#frontend_protocolobundle_reunion_refrigerio_0").prop("checked", false);
            $("#frontend_protocolobundle_reunion_refrigerio_1").prop("checked", false);
        });
        
        //si tengo admin no puedo tener consulta ni tecnico en ticket
        $("#frontend_protocolobundle_reunion_refrigerio_0").click(function(){
            $("#frontend_protocolobundle_reunion_refrigerio_2").prop("checked", false);
        });
        
        //si tengo admin no puedo tener consulta ni tecnico en ticket
        $("#frontend_protocolobundle_reunion_refrigerio_1").click(function(){
            $("#frontend_protocolobundle_reunion_refrigerio_2").prop("checked", false);
        });

});