$(document).on('ready', function() {
    var wrapperHeader = $("#wrapper-header"),
        titleMenu = $("#title-menu")
        header = $("#main-header")
        sidebar = $(".sidebar");

    var contentHeader = wrapperHeader.html();
    var contentMenu = titleMenu.children().html();

    // console.log(contentHeader);
    // console.log(contentMenu);

    $("#title-menu").on('affixed.bs.affix', function(){
        header.animate({
            height: "60"
        }, 500);
        sidebar.animate({
            top: '62'
        }, 500);
        titleMenu.css("border", "1px solid white");
        wrapperHeader.html(contentMenu);
        titleMenu.html('').css("height", "1px");
    });

    $("#title-menu").on('affixed-top.bs.affix', function(){
        header.animate({
            height: "80"
        }, 500);
        sidebar.animate({
            top: '82'
        }, 500);
        titleMenu.html(contentMenu);
        wrapperHeader.html(contentHeader);
        titleMenu.css("border", "0");
        titleMenu.css("border-bottom", "1px solid #ccc");
        titleMenu.css("height", "inherit");
    });

    /*
 ######    ##     ##  ########              ##     ##  ########   ##    ##   ##     ##  
##    ##   ##     ##  ##     ##             ###   ###  ##         ###   ##   ##     ##  
##         ##     ##  ##     ##             #### ####  ##         ####  ##   ##     ##  
 ######    ##     ##  ########   #######    ## ### ##  ######     ## ## ##   ##     ##  
      ##   ##     ##  ##     ##             ##     ##  ##         ##  ####   ##     ##  
##    ##   ##     ##  ##     ##             ##     ##  ##         ##   ###   ##     ##  
 ######     #######   ########              ##     ##  ########   ##    ##    #######   
*/

    var subMenu = $("#sub-menu")
        btnFilter = $("#wrapper-btnFilter")
        wrpRegister = $("#wrapper-register")
        btnShop = $("#wrapper-btnShop");

    subMenu.on('affixed.bs.affix', function(){
        subMenu.animate({
            marginTop: "-12"
        }, 500);
        subMenu.removeClass('col-xs-12 col-sm-12 col-md-12 col-lg-12').addClass("col-xs-12 col-sm-12 col-md-8 col-md-offset-1 col-lg-7 col-lg-offset-1");
        btnFilter.removeClass('col-xs-1 col-sm-2 col-md-2 col-lg-2').addClass('col-xs-2 col-sm-2 col-md-2 col-lg-2');
        btnFilter.children('button').removeClass('btn-sm').addClass('btn-xs');
        wrpRegister.removeClass('col-xs-9 col-sm-7 col-md-8 col-lg-8').addClass('col-xs-7 col-sm-7 col-md-7 col-lg-8');
        btnShop.removeClass('col-xs-1 col-sm-3 col-md-2 col-lg-2').addClass('col-xs-3 col-sm-3 col-md-3 col-lg-2');
        btnShop.children('div').children('button').removeClass('btn-sm').addClass('btn-xs');
        btnShop.css('line-height', '2');
    });

    subMenu.on('affixed-top.bs.affix', function(){
        subMenu.animate({
            marginTop: "0"
        }, 500);
        subMenu.removeClass('col-xs-12 col-sm-12 col-md-8 col-md-offset-1 col-lg-7 col-lg-offset-1').addClass("col-xs-12 col-sm-12 col-md-12 col-lg-12");
        btnFilter.removeClass('col-xs-2 col-sm-2 col-md-2 col-lg-2').addClass('col-xs-1 col-sm-2 col-md-2 col-lg-2');
        btnFilter.children('button').removeClass('btn-xs').addClass('btn-sm');
        wrpRegister.removeClass('col-xs-7 col-sm-7 col-md-7 col-lg-7').addClass('col-xs-9 col-sm-7 col-md-8 col-lg-8');
        btnShop.removeClass('col-xs-3 col-sm-3 col-md-3 col-lg-2').addClass('col-xs-1 col-sm-3 col-md-2 col-lg-2');
        btnShop.children('div').children('button').removeClass('btn-xs').addClass('btn-sm');
        btnShop.css('line-height', '2');
    });

    
        
} );