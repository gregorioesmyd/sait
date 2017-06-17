$(function(){

	/*==========  Conexion AJAX - Colocar cintas en Temporal de Prestamo  ==========*/
	$('.btn-prestamo').click(function(event) {
		event.preventDefault();
		$this = $(this);
		var path = window.location.pathname;
		index = path.search('segmento') + 8;
		pathRoot = path.substr(0, index);
		console.log(pathRoot.replace("segmento", "prestamo/tmp_prestamo"));
		$.ajax({
			url: pathRoot.replace("segmento", "prestamo/tmp_prestamo"),
			type: 'POST',
			dataType: 'json',
			data: {id: $this.data(),codigo: $this.data()},
		})
		.done(function(data) {
			$nroPrestamo = $('#nroPrestamo');
			var nro = $nroPrestamo.data('nroprestamo') + 1;
			$nroPrestamo.data('nroprestamo', nro).text(nro);
			if(nro == 1)
			{
				$("#boton").show();
				$(".botoncancelar").show();
				$("#msjvacio").hide();
				$("#msjcancelar").hide();
			}
			var codigo = '.' + $this.data('codigo');
			var cono = '#' + $this.data('codigo');
			$(codigo).remove();
			$(cono).html('<img src="/proyecto/videoteca1-0/web/bundles/videoteca/img/prestada.png"/>');
			$("#tablelista").find('tbody').append('<tr><td>'+data.id+'</td><td>'+data.codigo+'</td></tr>');	
			$("#tablecancelar").find('tbody').append('<tr><td>'+data.id+'</td><td>'+data.codigo+'</td><td><input type="checkbox" name="id[]" value="'+data.id+'" /> <span class="lbl"> Cancelar</span></td></tr>');	
			swal("Listo", "Cinta colocada temporalmente en prestamo!", "success");
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});

	});

	$('#cancelarPrestamo').on('click', function(event) {
		event.preventDefault();
		cancelar();

	});

	function cancelar() {
		if (confirm('¿Esta seguro que desea Cancelar la cinta a prestar?')) {
			var id = $('input[name="id[]"]').serialize();
			var path = window.location.pathname;
			index = path.search('segmento') + 8;
			pathRoot = path.substr(0, index);
			if (id !='') {
				$.ajax({
					type: "POST",
					url: pathRoot.replace("segmento", "prestamo/cancelar"),
					data: id,
					dataType: "json",
					success: function(response) {
						if (response.valid == "si") {
							location.reload();
						}
					}
				});
			} else {
				alert('Debe seleccionar la cinta a cancelar su prestamo.');
			}
		}
		return false;
	}

	/*==========  Gestion de boton de filtros activos  ==========*/

	$('#btnfilterActive').click(function() {
		$('#etiquetaActive').slideToggle( "slow" );
	});


	$('.pagination li.disabled span').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
	});

	/*==========  Gestion de boton de Reporte en excel  ==========*/
	$('#excel').on('click', function(event) {
		event.preventDefault();
		excel();

	});

	function excel() {
        $("#reporte").val(1)
        $('#form').submit()
        $("#reporte").val(0)
 	}

 	/*==========  Resalta texto de la busqueda  ==========*/
	var form_segmento_sinopsis = $("#form_segmento_sinopsis").val();
	console.log(form_segmento_sinopsis);

	if(form_segmento_sinopsis != ''){
		
		var data = form_segmento_sinopsis.split(",");

		for(var i=0; i < data.length; i++){
			$('.modal-body').highlight(data[i].trim());
		}

	}

    
});
