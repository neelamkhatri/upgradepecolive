/* --------------------------------------------- 
* Filename:     custom.js
* Version:      1.0.0 (2018-10-12)
* Website:      http://www.zymphonies.com
* Description:  Global Script
* Author:       Zymphonies Team
                info@zymphonies.com
-----------------------------------------------*/

function theme_menu(){

	//Main menu
	jQuery('#main-menu').smartmenus();
	
	//Mobile menu toggle
	jQuery('.navbar-toggle').click(function(){
		jQuery('.region-primary-menu').addClass('expand');
	});
	
	jQuery('.navbar-toggle-close').click(function(){
		jQuery('.region-primary-menu').removeClass('expand');
	});

	//Mobile dropdown menu
	if ( jQuery(window).width() < 767) {
		jQuery(".region-primary-menu li a:not(.has-submenu)").click(function () {
			jQuery('.region-primary-menu').hide();
	    });
	}

}

function theme_home(){
	
	//flexslider
	jQuery('.flexslider').flexslider({
    	animation: "slide"	
    });

}

jQuery(document).ready(function($){
	theme_menu();
	theme_home();
});


jQuery(document).ready(function($) {
	$('.views-exposed-form-store-home-block-1').on('change', function(){
		var showThis = $(this).val();

		if( $('.sncStore').hasClass('onlyShowThis') ) {
			$('.sncStore').removeClass('onlyShowThis');
		}
		
		$('.sncStore').each(function(i) {
			$('.sncStore').hide();
			console.log( $(this).data('category') );

			if( $(this).data('category') == showThis ) {
				$(this).addClass('onlyShowThis');
			}
		});

		if( showThis == 'all') {
			$('.sncStore').removeClass('onlyShowThis');
			$('.sncStore').removeAttr('style');
		}

	});


	$('.eventFilters').on('change', function(){
		var showThis = $(this).val();

		if( $('.sncEvents').hasClass('onlyShowThis') ) {
			$('.sncEvents').removeClass('onlyShowThis');
		}
		
		$('.sncEvents').each(function(i) {
			$('.sncEvents').hide();
			console.log( $(this).data('category') );

			if( $(this).data('category') == showThis ) {
				$(this).addClass('onlyShowThis');
			}
		});

		if( showThis == 'all') {
			$('.sncEvents').removeClass('onlyShowThis');
			$('.sncEvents').removeAttr('style');
		}

	});

	$('.store-toggle').on('click', function() {
		$(".store-toggle p.font-color-setter span").text( ($(".store-toggle p.font-color-setter span").text() == 'MORE') ? 'LESS' : 'MORE');
		
		if( $('p.font-color-setter i').hasClass('fa-caret-up') ){
			$('p.font-color-setter i').removeClass('fa-caret-up');
			$('p.font-color-setter i').addClass('fa-caret-down');
		} else {
			$('p.font-color-setter i').removeClass('fa-caret-down');
			$('p.font-color-setter i').addClass('fa-caret-up');
		}

		$('.store-toggle').parents('.stores').toggleClass('open');
	});

	$('.plus').on('click', function() {
		$(this).parents('.sncStore').toggleClass('open');
	});


});