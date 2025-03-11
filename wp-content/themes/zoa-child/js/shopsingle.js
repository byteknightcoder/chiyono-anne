jQuery(document).ready(function ($) {
    
		function checkRequiredFieldsNew( $cart ) {
			'use strict';

			var do_submit = true;

			$cart.find( '.ywapo_group_container' ).each( function() {

				var group_container = $(this);

				if ( typeof group_container != 'undefined' && ! group_container.hasClass('ywapo_conditional_hidden') && ! group_container.hasClass('ywapo_conditional_variation_hidden') ) {

					var type =  group_container.data('type');
					var required = group_container.data('requested') == '1';
					var required_all_options = group_container.data('requested-all-options') == '1';
					var selected = required_all_options;
					var max_item_selected = group_container.data('max-item-selected');

					switch( type ) {
						case 'text' :
						case 'textarea' :
						case 'number' :
						case 'file' :
						case 'date' :
						case 'range' :
						case 'color' :

							// work just for number
							if ( group_container.data('max-input-values-required') == '1' || group_container.data('min-input-values-required') == '1' ) {
								required = true;
								selected = false;
							} else {
								var $all_elements = group_container.find( 'input.ywapo_input, textarea.ywapo_input, input.ywapo_input_color');
								var $selected_elements = 0;

								$all_elements.each( function() {
									var value = $(this).val();
									if ( value != '' ) {
										$selected_elements++;
									}
								});

								if ( max_item_selected > 0 && max_item_selected == $selected_elements ) {
									selected = true;
								} else {
									$all_elements.each( function() {
										if ( required_all_options ) {
											if ( $(this).val() == '' && $(this).attr('required') == 'required' ) {
												required = true;
												selected = false;
												return;
											} else if ( $(this).val() == '' ) {
												selected = false;
												return;
											}
										} else {
											if ( $(this).val() != '' ) {
												selected = true;
												return;
											}
										}
									});
								}
							}

							break;

						case 'checkbox' :

							if ( required ) {

								var num_elements =  group_container.find( '.ywapo_input').length;
								var num_elements_selected =  group_container.find( '.ywapo_input:checked').length;

								if ( required_all_options ) {
									selected = num_elements > 0 && ( num_elements == num_elements_selected || ( max_item_selected > 0 && num_elements == max_item_selected )) ;
								} else {
									selected = num_elements > 0 && ( num_elements_selected > 0 ) ;
								}

							} else {

								group_container.find( '.ywapo_input').each(function(){

									if( ! $(this).is(':checked') && $(this).attr('required') == 'required' ) {
										required = true;
										selected = false;
										return;
									}

								});

							}

							break;


						case 'select' :

							selected = group_container.find( 'select.ywapo_input').val() != '';

							break;

						case 'labels' :
							selected = group_container.find( '.ywapo_input_container_labels.ywapo_selected').length > 0;
							if (!selected) {
								group_container.parent().next().find('.single_add_to_cart_button').addClass('disabled');
							} else {
								group_container.parent().next().find('.single_add_to_cart_button').removeClass('disabled');
							}

							break;

						case 'multiple_labels' :
							selected = group_container.find( '.ywapo_input_container_labels.ywapo_selected').length > 0;
							if (!selected) {
								group_container.parent().next().find('.single_add_to_cart_button').addClass('disabled');
							} else {
								group_container.parent().next().find('.single_add_to_cart_button').removeClass('disabled');
							}

							break;

						case 'radio' :

							selected = false;

							group_container.find( 'input.ywapo_input').each(function(){

								if( $(this).is(':checked') )  {
									selected = true;
									return;
								} else {
									group_container.parent().next().find('.single_add_to_cart_button').addClass('disabled');
								}

							});

							break;

						default :

					}

					if ( required && ! selected ) {
						do_submit = false;
						$('.single_add_to_cart_button').addClass('disabled');
						return;
					} else {
                                            $('.single_add_to_cart_button').removeClass('disabled');
					}

				}

			} );

			return do_submit;

		}

    $(window).on('load resize', function (e) {
        e.preventDefault();
        //has gallery
        var ImageW = $("#gallery-image-ow").width();//aspect 3:4
        //console.log('MainImageW：' + ImageW + 'px');
        //console.log('MainImageH：' + (ImageW * 4 / 3) + 'px');
        $('#gallery-image-ow').css('height', (ImageW * 4 / 3) + 'px');
        //has no gallery
        var SImageW = $(".single-gallery-slider.single-gallery-vertical .single-product-gallery.pro-single-image .pro-carousel-image #gallery-image .pro-img-item").width();//aspect 3:4
        $('.single-gallery-slider.single-gallery-vertical .single-product-gallery.pro-single-image .pro-carousel-image #gallery-image .pro-img-item').css('height', (SImageW * 4 / 3) + 'px');

        //add nano scroller for gallery thumb
        $('.pro-carousel-thumb').addClass('nano');
        $('#gallery-thumb').addClass('nano-content');
        $('.pro-carousel-thumb').height($('#gallery-image').height());
        $('.pro-carousel-thumb').nanoScroller();
    });
    //change class for woocommerce ultimate gift card plugin
    $('form.cart .mwb_wgm_added_wrapper p.mwb_wgm_section').each(function () {
        $(this).addClass('field-wrapper').removeClass('mwb_wgm_section').wrap('<div class="form-row"></div>');
    });
    $('.mwb_wgm_delivery_via_email > input').each(function () {
        $(this).wrap('<div class="form-row" />').wrap('<div class="field-wrapper" />');
    });
    //add required element for attribute
    var attribute = $('.variations.pdp__attribute--group');
    if (attribute.length > 0) {
        $('.pdp__attribute--group > .pdp__attribute.variations__attribute').each(function () {
            if ($(this).find('.about_size_wraper').length) {
                $(this).find('.info_show_wrap').before('<abbr class="required" title="Required">*</abbr>');
            } else {
                $(this).find('.pdp__attribute__label').append('<abbr class="required" title="Required">*</abbr>');
            }
        });
    }
    //add class if attribute element width is bigger width
    $(window).on('load resize', function () {
        var vairations = $('.variations.pdp__attribute--group');
        if (vairations.length > 0) {
            var variationCon = vairations.width();
            var attList = [];
            $('.pdp__attribute--group > .pdp__attribute.variations__attribute').each(function () {
                attList.push($(this).width());
            });
            var maxAttW = Math.max.apply(null, attList);
            var sumAttW = attList.reduceRight(function (a, b) {
                return a + b;
            });
            if ((sumAttW + 24) > variationCon) {
                //$('.pdp__attribute--group > .pdp__attribute.variations__attribute + .pdp__attribute.variations__attribute').css('margin-top', '24px');
            } else {
                //$('.pdp__attribute--group > .pdp__attribute.variations__attribute + .pdp__attribute.variations__attribute').css('margin-top', '0');
            }
            //console.log('attribute max width：' + maxAttW + ' px');
            //console.log('attContainer：' + variationCon + ' px');
            //console.log('attList sum：' + sumAttW + ' px');
            //console.log('attList sum + 24px：' + (sumAttW + 24) + ' px');
        }
    });

    function isAddToCartValid()
    {
        var validateForm = $("form.cart");
        validateForm.validationEngine({
            promptPosition: 'inline',
            addFailureCssClassToField: "inputError",
            bindMethod: "live"
        });
        var isValid = validateForm.validationEngine('validate');
        return isValid;
    }

    if ($('.mwb_wgm_added_wrapper').length)
    {
//        if($('body.postid-32466-temp').length || $('body.postid-32471-temp').length){
//        }else{
//            $('#mwb_wgm_from_name').addClass('validate[required] required');
//        }
        // $('#mwb_wgm_message').addClass('validate[required] required');
        $('#mwb_wgm_to_email').addClass('validate[required,custom[email]] required');
        $('#mwb_wgm_to_ship').addClass('validate[required] required');

        var cloneAddCartBtn = $('button[name="add-to-cart"]').clone();
        cloneAddCartBtn.attr('name', 'add-to-cart-clone');
        cloneAddCartBtn.attr('type', 'button');

        $('button[name="add-to-cart"]').hide();
        $('button[name="add-to-cart"]').after(cloneAddCartBtn);
        function showHideAddCartBtn(isValid)
        {
            if (isValid)
            {
                var radioval = $('input[name="mwb_wgm_send_giftcard"]:checked').val();
                if (radioval === "Shipping") {
                    $('#previewBox').hide();
                } else {
                    //$('#previewBox').show();
                }

                $('button[name="add-to-cart"]').show();
                $('button[name="add-to-cart-clone"]').hide();
            } else {
                $('#previewBox').hide();
                $('button[name="add-to-cart"]').hide();
                $('button[name="add-to-cart-clone"]').show();
            }
        }
        $('input[name="mwb_wgm_send_giftcard"]:radio').change(function () {
            //var isValid = $('form.cart .inputError').length ? false : true;
            //showHideAddCartBtn(isValid);
        });
        $('body').on('blur', 'form.cart input, form.cart textarea', function () {
            setTimeout(function () {
                var isValid = false;
                if (!$('form.cart .inputError').length)
                {
                    isValid = isAddToCartValid();
                }
                showHideAddCartBtn(isValid);
            }, 100);
        });

        $('body').on('click', 'button[name="add-to-cart-clone"]', function () {
            var isValid = isAddToCartValid();
            showHideAddCartBtn(isValid);
        });
    }

    $('.mwb_wgm_delivery_method_wrap > .mwb_wgm_delivery_method > div').each(function () {
        $(this).find('input[type="text"]').wrap(function (i) {
            return '<div class="form-row" />';
        });
        $(this).find('.form-row').wrapInner(function (i) {
            return '<div class="field-wrapper" />';
        });
        //$(this).wrapAll('<div class="field-wrapper"></div>').wrapAll('<div class="form-row"></div>');
    });
    //remove cta class from related items
    if ($('.c-product-item .yith-wcwl-add-to-wishlist > div > a').hasClass('cta')) {
        $('.c-product-item .yith-wcwl-add-to-wishlist > div > a').removeClass('cta');
    }
    var slickH = $('.pro-carousel-image >#gallery-image > .slick-list').height();
    $('.pro-carousel-image >#gallery-image > .slick-list > .slick-track > .slick-slide').css('height', slickH + 'px');


    $('body').on('click', '.pdp__attribute--group.variations', function () {
        setTimeout(function () {
            disableAddCartButton();
        }, 150);
    });
    $('body').on('change', '#ywapo_ctrl_id_15_0', function () {
        setTimeout(function () {
            disableAddCartButton();
        }, 150);
    });
    if($('.yith_wapo_groups_container').length){
        $('.single_variation_wrap .single_variation').first().addClass('ch_need_hide_detect');
        if($('.all_out_stock').length){
            $('.single_variation_wrap .single_variation').first().addClass('has_all_out_stock');
        }
    }
    $('body').on('change', '.yith_wapo_groups_container input[type="radio"]', function () {
        var type_field = $(this).attr('type');
        if(type_field==='radio'){
            $(this).closest('.ywapo_options_container').find('.ywapo_input_container').removeClass('checked');
            if ($(this).is(':checked')) {
                $(this).parent().addClass('checked');
            }
        }
    });
    $('body').on('change', '.yith_wapo_groups_container select', function () {
        var value_sl=$(this).val();
        var img_url_select=$(this).find('option[value="'+value_sl+'"]').attr('data-image-url');

        if(img_url_select!=='' && jQuery.type(img_url_select) !== "undefined"){
            var res = img_url_select.split("/");
            var img_url_select_name=res[res.length-1];
            $('.iconic-woothumbs-images-wrap .slick-list .slick-slide').each(function () {
                var large_image=$(this).find('img').attr('data-large_image');
                var res_large = large_image.split("/");
                var res_large_name=res_large[res_large.length-1];
                if(img_url_select_name===res_large_name){
                    var sl_index=$(this).attr('data-slick-index');
                    $('.iconic-woothumbs-images').slick('slickGoTo', sl_index);
                }
            });
        }
    });
    var iconic_was_fees = $('.single-product script.iconic-was-fees').text();
    if (iconic_was_fees != '') {
        //for case have fee on options
    } else {
        if ($('.single-product .single_variation').length && typeof jckpc_inventory !== "undefined" && jckpc_inventory !== null) {
            $('.single_variation_wrap').addClass('ch_vari_wrap');
        }
    }
    function convert_price_to_float(current_prive){
        if(typeof current_prive!=='undefined' && typeof current_prive !== undefined && current_prive!=''){
            current_prive=current_prive.replace(',','');
            current_prive=current_prive.replace('¥','');
            current_prive=parseFloat(current_prive);
        }
        return current_prive;
    }
    if ($('.prod-info .woocommerce-Price-amount bdi').length) {
        org_price = $('.prod-info .woocommerce-Price-amount bdi').text();
        org_price = org_price.replace(',', '');
        org_price = org_price.replace('¥', '');
        org_price = parseFloat(org_price);
    }
    function disableAddCartButton() {
        $('.add-to-wishlist-button .disable_wishlist_float').remove();
        $('.add-to-wishlist-button').removeClass('disabled');


        var variationGroup = $('.pdp__attribute--group.variations .variations__attribute').length;
        if($('ul[data-attribute="attribute_pa_embroidery"] li a').length){
            if($('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').length && $('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').attr('data-attribute-value')!='none'){
                variationGroup=variationGroup+1;
            }else{
                $('#ywapo_value_15').hide();
            }
        }
        if ($('.pdp__attribute--group.variations li.variable-item').length) {
            var numVariationSelected = $('.pdp__attribute--group.variations').find('li.variable-item.selected').length;
        } else {
            var numVariationSelected = $('.pdp__attribute--group.variations').find('li.iconic-was-swatches__item a.iconic-was-swatch--selected').length;
            var check_rqr=0;
            $('.pdp__attribute--group.variations .variations__attribute select').each(function () {
                var attr = $(this).attr('required');
                if (typeof attr !== typeof undefined && attr !== false) {
                    if($(this).val()!=''){
                        check_rqr++;
                    }
                }else{
                    check_rqr++;
                }
            });
            //console.log('check_rqr='+check_rqr);
            numVariationSelected=numVariationSelected+check_rqr;
            if($('ul[data-attribute="attribute_pa_embroidery"] li a').length){
                if($('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').length && $('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').attr('data-attribute-value')!='none'){
                    if($('#ywapo_ctrl_id_15_0').val()==''){
                        numVariationSelected=0;
                    }
                }
            }
        }
//console.log('variationGroup='+variationGroup+'|||numVariationSelected='+numVariationSelected);
        if (numVariationSelected < variationGroup)
        {
            $('.single_add_to_cart_button').addClass('woocommerce-variation-add-to-cart-disabled disabled wc-variation-is-unavailable');
            $('.woocommerce-variation-add-to-cart').addClass('woocommerce-variation-add-to-cart-disabled disabled wc-variation-is-unavailable');

            // Disable favorite button
            var overflowWishlist = '<div class="disable_wishlist_float">&nbsp;</div>';
            $('.add-to-wishlist-button').append(overflowWishlist);
            $('.add-to-wishlist-button').addClass('disabled');
            var iconic_was_fees = $('.single-product script.iconic-was-fees').text();
            if (iconic_was_fees != '') {
                //for case have fee on options
            } else {
                if ($('.single-product .single_variation').length && typeof jckpc_inventory !== "undefined" && jckpc_inventory !== null) {
                    $('p.ch_variation_price').remove();
                    $('.prod-info p.price').show();
                }
                if($('#yith_wapo_groups_container')){
                    $('div.ch_vari_price').remove();
                }
            }
        } else {
             if($('ul[data-attribute="attribute_pa_embroidery"] li a').length){
                if($('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').length && $('ul[data-attribute="attribute_pa_embroidery"] li.iconic-was-swatches__item a.iconic-was-swatch--selected').attr('data-attribute-value')!='none'){
                    if($('#ywapo_ctrl_id_15_0').val()!='' && $('.pdp__attribute--group.variations').find('li.iconic-was-swatches__item a.iconic-was-swatch--selected').length == 4){
                        $('.single_add_to_cart_button').removeClass('woocommerce-variation-add-to-cart-disabled disabled wc-variation-is-unavailable');
                    }
                }
            }
            //check to  switch above price but plz apply this ONLY IF ANY ATTRIBUTE DON'T HAVE OPTIONAL FEE.
            var iconic_was_fees = $('.single-product script.iconic-was-fees').text();
            if (iconic_was_fees != '') {
                //for case have fee on options
                var html = $('.single_variation_wrap div.woocommerce-variation-price').html();
                if(html!=='' && jQuery.type(html) !== "undefined"){
                    const obj_fee = JSON.parse(iconic_was_fees);
                    if(("length-type" in obj_fee)){
                        var current_prive=html;
                        current_prive=convert_price_to_float(current_prive);
                        var type_length=$('#length-type').val();
                        var fee=obj_fee['length-type'][type_length];
                        if(typeof fee!=='undefined' && typeof fee !== undefined ){
                            fee=fee*0.1;
                            var new_price=parseFloat(current_prive)+parseFloat(fee);
                            if(new_price!==current_prive){
                                html="¥"+formatNumber(new_price,'.',',');
                                //for case discount
                                if($('.single_variation_wrap div.woocommerce-variation-price del').length){
                                    var price_del=$('.single_variation_wrap div.woocommerce-variation-price del').html();
                                    var price_ins=$('.single_variation_wrap div.woocommerce-variation-price ins').html();
                                    price_del=convert_price_to_float(price_del);
                                    price_ins=convert_price_to_float(price_ins);
                                    if(typeof price_del!=='undefined' && typeof price_del !== undefined &&price_del>0&&typeof price_ins!=='undefined' && typeof price_ins !== undefined &&price_ins>0){
                                        var price_del=parseFloat(price_del)+parseFloat(fee);
                                        var price_ins=parseFloat(price_ins)+parseFloat(fee);
                                        var html="<del>¥"+formatNumber(price_del,'.',',')+"</del> <ins>¥"+formatNumber(price_ins,'.',',')+"</ins>";
                                        //console.log(html);
                                    }
                                }
                            }
                        }
                    }
                    $("div.prod-info p.price").html(html);
                    //$('.single_variation_wrap div.woocommerce-variation-price').html(html);
                }
            } else {
                if ($('.single-product .single_variation').length && typeof jckpc_inventory !== "undefined" && jckpc_inventory !== null) {
                    var html = $('.single_variation_wrap div.woocommerce-variation-price').html();
					if(html!=='' && jQuery.type(html) !== "undefined" && !$('.postid-31641').length){
						$('.prod-info p.price').hide();
						$('.single_variation_wrap div.woocommerce-variation-price').hide();
						$('p.ch_variation_price').remove();
						$("div.prod-info p.price").after('<p class="price ch_variation_price">' + html + '</p>');
					}
                }
                if($('#yith_wapo_groups_container')){
                    var html_vari=$('div.woocommerce-variation').html();
					if(html_vari!=='' && jQuery.type(html_vari) !== "undefined"){
						$('div.ch_vari_price').remove();
						$("#yith_wapo_groups_container").after('<div class="ch_vari_price">'+html_vari+'</div>');
                                                if($('.woocommerce-variation-price .price .woocommerce-Price-amount').length){
                                                    $('div.ch_vari_price').hide();
                                                    $("div.prod-info p.price").html(html_vari);
                                                }
					}
                }
            }
            //end
        }
        if($('#ywapo_value_8').length){
            setTimeout(function () {
                if($('#ywapo_select_7').val()==''||($('#ywapo_ctrl_id_8_0').val()=='' && $('#ywapo_select_7').val()=='0')){
                    $('.single_add_to_cart_button').addClass('disabled');
                }
            }, 200);
        }
        if($('#ywapo_value_18').length && $('#ywapo_value_20').length){
            setTimeout(function () {
//                if($('#ywapo_select_7').val()==''||($('#ywapo_ctrl_id_8_0').val()=='' && $('#ywapo_select_7').val()=='0')){
//                    $('.single_add_to_cart_button').addClass('disabled');
//                }
                var pa_checked=false;
                if($('#ywapo_ctrl_id_18_0').is(':checked')){
                    $('input[name="ywapo_radio_20[]"]').each(function () {
                        if($(this).is(':checked')){
                            pa_checked=true;
                            return false;
                        }
                    });
                    if(pa_checked==false){
                        $('.single_add_to_cart_button').addClass('disabled');
                    }
                }
            }, 200);
        }
        if($('#ywapo_select_31').length){
            var attr_require = $('#ywapo_select_31').attr('required');
            if (attr_require !== '' && typeof (attr_require) != "undefined") {
                if($('#ywapo_select_31').val()==''){
                    $('.single_add_to_cart_button').addClass('disabled');
                }
            }
        }
        $('form.variations_form').find('input,textarea,select').each(function (i, field) {
            var attr_require = $(this).attr('required');
            if ((attr_require !== '' && typeof (attr_require) != "undefined")||$(this).closest('.variations__attribute').find('abbr.required').length) {
                if($(this).val()==''){
                    $('.single_add_to_cart_button').addClass('disabled');
                }
            }
        });
        
    }
    disableAddCartButton();

    $('body').on('click', '.ywapo_input_container_labels', function () {
        $('.variations__attribute__value:eq(0)').find('input, select').each(function () {
            $(this).trigger('change');
            return false;
        })
    })
    
    $('body').on('change', '#length-type', function () {
        setTimeout(function () {
            var iconic_was_fees = $('.single-product script.iconic-was-fees').text();
            if (iconic_was_fees != '') {
                //for case have fee on options
                var html = $('.single_variation_wrap div.woocommerce-variation-price').html();
                if(html!=='' && jQuery.type(html) !== "undefined"){
                    const obj_fee = JSON.parse(iconic_was_fees);
                    if(("length-type" in obj_fee)){
                        var current_prive=html;
                        current_prive=convert_price_to_float(current_prive);
                        var type_length=$('#length-type').val();
                        var fee=obj_fee['length-type'][type_length];
                        if(typeof fee!=='undefined' && typeof fee !== undefined ){
                            fee=fee*0.1;
                            var new_price=parseFloat(current_prive)+parseFloat(fee);
                            if(new_price!==current_prive){
                                html="¥"+formatNumber(new_price,'.',',');
                                //for case discount
                                if($('.single_variation_wrap div.woocommerce-variation-price del').length){
                                    var price_del=$('.single_variation_wrap div.woocommerce-variation-price del').html();
                                    var price_ins=$('.single_variation_wrap div.woocommerce-variation-price ins').html();
                                    price_del=convert_price_to_float(price_del);
                                    price_ins=convert_price_to_float(price_ins);
                                    if(typeof price_del!=='undefined' && typeof price_del !== undefined &&price_del>0&&typeof price_ins!=='undefined' && typeof price_ins !== undefined &&price_ins>0){
                                        var price_del=parseFloat(price_del)+parseFloat(fee);
                                        var price_ins=parseFloat(price_ins)+parseFloat(fee);
                                        var html="<del>¥"+formatNumber(price_del,'.',',')+"</del> <ins>¥"+formatNumber(price_ins,'.',',')+"</ins>";
                                        //console.log(html);
                                    }
                                }
                            }
                        }
                    }
                    $("div.prod-info p.price").html(html);
                }
            }
        }, 200);
    });
    
    //for eyemask
    $('#ywapo_value_8 input, #ywapo_value_6 input, #ywapo_select_7,input[name="ywapo_radio_20[]"], #ywapo_select_31').change(function () {
            var cart = $('form.cart');
            checkRequiredFieldsNew(cart);
            if($(this).val()==''||($('#ywapo_ctrl_id_8_0').val()=='' && $('#ywapo_select_7').val()=='0')){
                $('.single_add_to_cart_button').addClass('disabled');
            }
            
            if($('#ywapo_select_31').length){
                var attr_require = $('#ywapo_select_31').attr('required');
                if (attr_require !== '' && typeof (attr_require) != "undefined") {
                    if($('#ywapo_select_31').val()==''){
                        $('.single_add_to_cart_button').addClass('disabled');
                    }
                }
            }
    });
    //validate
    $('form.variations_form').change(function () {
        disableAddCartButton();
    });
});