        /**** POP-UP ******/
jQuery(document).ready(function($) {
    
        /* Apertura Chiusura da Bottone */
        //$('.info_show_wrap .pop-up-button').on('click', function (e) {
          $('body').on('click', '.info_show_wrap .pop-up-button', function () {
                var statusActive = $(this).hasClass('js-actives');
			var popCon = $(this).parent('.bodyshape_info').next('.pop-up');

                if (statusActive == false) {
             /* rimozione di eventuali precedenti aperti nella stessa posizione */
                    $(popCon).removeClass('js-actives');
                    $('.pop-up-button').removeClass('js-actives');
                    /* tolgi tutte le classi che iniziano per "pop-up-special-" */
                    $(popCon).removeClass(function (index, css) {
                        return (css.match(/(^|\s)pop-up-special-\S+/g) || []).join(' ');
                    });

                    /* calcolo posizione pop-up */


                    /* Dimensioni Finestra */
                    var totX = $(window).width();
                    var totY = $(window).height();

                    /* Dimensioni POP UP*/
                    var popWidth = $(popCon).outerWidth();
                    var popWidthHalf = popWidth / 2;
                    var popHeight = $(popCon).outerHeight();
					var popHeightHalf = popHeight / 2;
     
                    /* Dimensioni Pulsante */
                    var buttWidth = $(this).outerWidth();
                    var buttWidthHalf = buttWidth / 2;
                    var buttHeight = $(this).outerHeight();

                    /* Offset Pulsante */
                    var buttOffLeft = ($(this).offset().left) - ($(window).scrollLeft());
                    var buttOffTop = ($(this).offset().top) - ($(window).scrollTop());

                    var buttOffRight = (totX - (buttOffLeft + buttWidth));
                    var buttOffBottom = Math.abs(totY - (buttOffTop + buttHeight));
					var popWrapX = $('.info_show_wrap').outerWidth();
					if (totX > 992) {
						console.log(popHeightHalf);
						$(popCon).css({
                        'top': '50%',
                        'left': '50%',
						'margin-top': '-'+popHeightHalf+'px',
						'margin-left': '-'+popWidthHalf+'px'
						});
						
						$('#shop-overlay').addClass('set--active');
					} else {
						$(popCon).css({
							'top': 0,
							'left': 0,
							'margin-top': 0,
							'margin-left': 0
						});
					}
            

                    /* ALLINEAMENTI */

                    /* al centro */

                    if ( 
                        (popWidthHalf <= (buttOffLeft + buttWidthHalf)) && (popWidthHalf <= (buttOffRight + buttWidthHalf)) 
                        ) 
                    
                    {
                        var popX = (buttOffLeft + (buttWidth / 2) - (popWidth / 2));

                    }
                        /* a sinistra */
                    else if ((popWidthHalf >= (buttOffLeft + buttWidthHalf))) {
                        var popX = (buttOffLeft);
                        $(popCon).addClass('pop-up-special-left');
                    }
                    /* a destra */
                    else if (popWidthHalf >= (buttOffRight + buttWidthHalf)) {
                    
                        var popX = (totX - (popWidth + buttOffRight + 30)); /* 30 per il pulsante "X" */
                        $(popCon).addClass('pop-up-special-right');
                }


                  
                    /* in basso */
                    if (popHeight <= buttOffBottom) {

                        var popY = (buttOffTop + buttHeight + 5);
                 
                    }
                        /* in alto */
                    else {
              
                        var popY = (totY - (buttOffBottom + buttHeight + popHeight + 10));

                        $(popCon).addClass('pop-up-special-bottom');
                    }

                   
                    /* posizione finale del pop-up */
                    /*$(popCon).css({
                        'top': (popY),
                        'left': (popX),
                    }).css*/

                    /* apertura pop-up e attivazione pulsante */
                    //$(this).addClass('js-actives');
                    /* pausa prima di rifare animazione al contrario */
                    setTimeout(function () {
                        $(popCon).addClass('js-actives');
                    }, 150);

                  
                }
                else {
                    $(popCon).removeClass('js-actives');
					if ($(window).width() > 992) {
						$('#shop-overlay').removeClass('set--active');
					}
                    $(this).removeClass('js-actives');
                    /* tolgi tutte le classi che iniziano per "pop-up-special-" */
                    $(popCon).removeClass(function (index, css) {
                        return (css.match(/(^|\s)pop-up-special-\S+/g) || []).join(' ');
                    });
                }



        });


        /* Chiusura POP-UP da Tasto "X" */
        //$('.info_show_wrap .pop-up-close').on('click', function (e) {
        $('body').on('click', '.info_show_wrap .pop-up-close', function (e) {
			var popCon = $(this).parents('.pop-up');
			$(this).parents('.info_show_wrap').find('.pop-up-button').removeClass('js-actives');
            //$('.pop-up-button').removeClass('js-actives');
            $(popCon).removeClass('js-actives');
			if ($(window).width() > 992) {
						$('#shop-overlay').removeClass('set--active');
					}
            /* tolgi tutte le classi che iniziano per "pop-up-special-" */
            $(popCon).removeClass(function (index, css) {
                return (css.match(/(^|\s)pop-up-special-\S+/g) || []).join(' ');
            });
            e.stopPropagation();
        });


        /* Chiusura POP- UP per CLICK FUORI DALL'ELEMENTO */
        $(document).mouseup(function (e) {

            if (
                ($(e.target).is('.pop-up,.pop-up-button') === false) &&
                ($(e.target).parents('.pop-up,.pop-up-button').length === 0)
                ) {

                $('.pop-up-button,.pop-up').removeClass('js-actives');
				$('#shop-overlay').removeClass('set--active');

            }

        });

        /* CHIUSURA pop-up per scroll sulla window principale */
        $(window).scroll(function () {

                            //$('.pop-up-button,.pop-up').removeClass('js-actives');


                        });


                        /* CHIUSURA  pop-up per ogni evento di scroll */
/*  
if($('.pop-up-button.js-actives').parents().filter(function(){
    return $(this).scroll();
}).length)
  {
    alert('ciao');
       $('.pop-up-button,.pop-up').removeClass('js-actives');
  } */
  
   $('html, body').scroll(function () {
          $('.pop-up-button,.pop-up').removeClass('js-actives');
      });  

        /* FINE */
	});