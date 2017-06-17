$(function() {

	function getPath($form) {
		path = window.location.pathname;
		index = path.search('segmento') + 8;
		pathRoot = path.substr(0, index);
		parameters = $form.serialize();
		return pathRoot + '/1/enable';
	};

	function isEmptyForm(parameters) {
		var flag = true;
		parameters.pop();
		$.each(parameters, function(index, field) {
			if (typeof field.value != 'undefined') {
				if (field.value.length != 0) {
					flag = false;	
				};
			}			
		});

		return flag;
	};

	$(".searchBtn").on('click', function(event) {
		event.preventDefault();
		var $form = $('form');
		var path = window.location.pathname;
		parameters = $form.serializeArray();
		parameters.shift();
		if (isEmptyForm(parameters)) {
			alert('Debe seleccionar uno de los filtros');
		} else {
			index = path.search('segmento') + 8;
			pathRoot = path.substr(0, index);
			$form.attr('action', pathRoot).submit();
		}
	});

	$(".pagination li").on('click', function(e){
		e.preventDefault();
		var $this = $(this);
		var path = window.location.pathname;
		var $form = $('form');
		parameters = $form.serializeArray();
		parameters.shift();
		if (isEmptyForm(parameters)) {
			location.replace($this.find('a').attr('href'));
		} else {
			index = path.search('segmento') + 8;
			pathRoot = path.substr(0, index);
			newPath = pathRoot + '/' + $this.find('a').text();
			$form.attr('action', newPath).submit();
		}
	});


	$('#form_reset').on('click', function(event) {
		event.preventDefault();

		$(this).closest('form').find("input[type=text], textarea, select, input[type=datetime]").val("");

	});


});