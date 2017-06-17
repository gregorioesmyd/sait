$(function(){
    $('#myModalcancelar').on('hidden.bs.modal', function () {});
    $('#myModallistar').on('hidden.bs.modal', function () {});
    $("input[name=checktodos]").change(function(){
            $('input[type=checkbox]').each( function() {            
                if($("input[name=checktodos]:checked").length == 1){
                    this.checked = true;
                } else {
                    this.checked = false;
                }
            });
        });
});