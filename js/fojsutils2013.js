$(window).load(function(){
	var windowWidth = $(window).width();
        var col_right = windowWidth - $("#col_left").outerWidth(true)-20;
        var col_right_menu = col_right+160;
        
        widthRight();
	
	//Initialisation affichage hauteur
	hauteur_page();
	
	//Initialisation du menu
	menu_action();
		
	
	
		
	$(".affichage_menu").toggle(
			function() {
                                var windowWidth = $(window).width();
                                var col_right = windowWidth - $("#col_left").outerWidth(true)-20;
                                var col_right_menu = col_right+160;
                            
                            
                            
				$(".affichage_menu img").attr("src","/backoffice/cms/img/2013/picto/fleche_noire2_open.png");
				$("#menu").removeClass('open_menu');
				$("#menu").addClass('close_menu');
				$("ul.ss_menu").css("display", "none");
				$( "#col_left" ).animate({
					width: 90,
					marginLeft: -7
				}, 500 );
				$("#col_right").animate({
					width: (col_right_menu)
				}, 500 );
				$(".site_travail span").hide();
				
			},
			function() {
                            
                                var windowWidth = $(window).width();
                                var col_right = windowWidth - 252-20;
                                var col_right_menu = col_right+160;
                            
				$(".affichage_menu img").attr("src","/backoffice/cms/img/2013/picto/fleche_noire2.png");
				$("#menu").removeClass('close_menu');
				$("#menu").addClass('open_menu');
				$( "#col_left" ).animate({
					marginLeft: 0,
					width: 252
				}, 500 );
				$("#col_right").animate({
					width: (col_right)
				}, 500 );
				$(".site_travail span").delay('300').show('15000');
				
			}
		);
                    
        $("a.affichage_menu").click(function(){
                $.ajax({
                    type: 'post',
                    data: { state: $("#menu").attr('class') },
                    url: '/backoffice/cms/img/2013/theme1/ajax/update_session.php'
                });
            });
            
            
            
            
        $("ol.new_diapo .visuel").fancybox(); 
        $("ol.one_img .visuel").fancybox(); 
            
            
            
            
            
            
            
            
            
            
           
});


$(window).resize(function(){
    //alert('resize');
    widthRight();

    hauteur_page();
});
function widthRight(){
    var windowWidth = $(window).width();
    var col_right = windowWidth - $("#col_left").outerWidth(true)-20;
    var col_right_menu = col_right+160;

    $('#col_right').removeAttr('style');
    $('#col_left').css('min-height', 0);
    $("#col_right").css('width',col_right);
}



//Fonction gérant les différentes versions du menu
function menu_action(){
	$("#menu li a").click(function () { 
		$(this).next("ul.ss_menu").slideToggle("slow" , function() {
                        $("#col_left").css('overflow', 'visible');
                        $(this).parent().css('overflow', 'visible');
                        $(this).css('overflow', 'visible');
                        $(".ss_menu li.nav2.active").children(".ss_menu").css('overflow', 'visible');
  		});
                $("#col_left").css('overflow', 'visible');
                $(this).parent().siblings().children(".ss_menu").slideUp("slow");
                $(this).parent().siblings().children(".ss_menu").find("ul").slideUp("slow");
                $(this).parent().toggleClass("active");
                $(this).parent().siblings().removeClass("active");

                if($(".ss_menu li.nav2.active")){	
                        $(this).parent().addClass('active open');
                        $(".ss_menu li.nav2.active").children(".ss_menu").css('overflow', 'visible');
                };
	});
	
	
}

//Fonction hauteur contenu page
function hauteur_page(message){
	//alert(message);
	//calcul hauteur #center
	var windowHeight = $(window).height();
        var headerHeight = $("header").height();
        var hauteurRight = $("#col_right").height();
        var hauteurLeft = $("#col_left").height();
        var minHeight = 0;
        
        if(hauteurRight > hauteurLeft){
            if(hauteurRight < windowHeight) {
                minHeight = windowHeight - headerHeight;
            } else {
                minHeight = hauteurRight;
            }
        } else {
            if(hauteurLeft < windowHeight) {
                minHeight = windowHeight - headerHeight;
            } else {
                minHeight = hauteurLeft;
            }
        }

        $('#col_left').css('min-height', minHeight+'px');


}


//Fonction ouvrant le menu suivant la page en cours
function refresh_menu(page){
	//alert(page);
	$t = $("#menu a[href$='"+page+"']");
	$t.addClass("active");
	if($t.parent().attr('class') == "niv3"){
		//cas niveau 3
		$t.parent().addClass("active");
		$t.parent().parent().css("display", "block");
		$t.parent().parent().parent().addClass("active");
		$t.parent().parent().parent().parent().css("display", "block");
		$t.parent().parent().parent().parent().parent().addClass("active");
	} else {
		//cas niveau 2
		$t.parent().addClass("active");
		$t.parent().parent().css("display", "block");
		$t.parent().parent().parent().addClass("active");
	}
	
	 
}
//Fonction


 








