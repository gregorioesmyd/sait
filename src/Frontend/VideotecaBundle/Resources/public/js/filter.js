var $this = $("#slideFilter"),
    icon = $("#icon"),
    subMenu = $("#sub-menu");

$("#slideFilter").on('click', function(){
    eventTrigger = $this.data('event');
    execute($this, eventTrigger);
});

var execute = function($this, event) {
    if(event == "mostrar") {
        $(".sidebar").removeClass('row-sidebar oculto', 100, function(){
            $(".sidebar").addClass('col-xs-2 col-sm-2 col-md-3 col-lg-3 row-sidebar-active');
            $(".main")
            .removeClass('col-xs-12 col-sm-12 col-md-12 col-lg-12')
            .addClass('col-xs-10 col-sm-10 col-md-9 col-md-offset-3 col-lg-10 col-lg-offset-3');
            // subMenu.removeClass('col-md-offset-3 col-lg-offset-1').addClass("col-md-offset-1 col-lg-offset-1");
        });
        $this.data('event', 'oculto');
    } else {
        $(".sidebar").addClass('row-sidebar', 200, function(){
            $(".sidebar")
            .addClass('oculto')
            .removeClass('col-xs-2 col-sm-2 col-md-3 col-lg-2 row-sidebar-active');
            $(".main")
            .addClass('col-xs-12 col-sm-12 col-md-12 col-lg-12')
            .removeClass('col-xs-10 col-sm-10 col-md-9 col-md-offset-3 col-lg-10 col-lg-offset-3');
            // subMenu.removeClass('col-md-offset-1 col-lg-offset-1').addClass("col-md-offset-3");
        });
        $this.data('event', 'mostrar');
    }
};