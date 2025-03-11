function setChCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getChCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

jQuery(document).ready(function ($) {
	// Mega Menu Mobile Navigation	
	let width;
	$(window).on('resize', function() {
	  if ($(this).width() !== width) {
		
		width = $(this).width();
		if(width>991){
			$( ".submenu-mobile-shop" ).attr('style', 'display: none!important');
			$( ".submenu-mobile-items" ).attr('style', 'display: none!important');
			$( ".submenu-mobile-collection" ).attr('style', 'display: none!important');
			$( ".submenu-mobile-main" ).attr('style', 'display: flex!important');
		}
	  }
	});
	$( ".show-submenu-mobile-shop" ).on("click", function() {
		$( ".submenu-mobile-main" ).attr('style', 'display: none!important');
		$( ".submenu-mobile-items" ).attr('style', 'display: none!important');
		$( ".submenu-mobile-collection" ).attr('style', 'display: none!important');
		$( ".submenu-mobile-shop" ).attr('style', 'display: flex!important');
	} );
	$( ".show-submenu-mobile-main" ).on("click", function() {
		$( ".submenu-mobile-main" ).attr('style', 'display: flex!important');
		$( ".submenu-mobile-shop" ).attr('style', 'display: none!important');
	} );
	$( ".show-submenu-mobile-items" ).on("click", function() {
		$( ".submenu-mobile-items" ).attr('style', 'display: flex!important');
		$( ".submenu-mobile-shop" ).attr('style', 'display: none!important');
	} );
	$( ".show-submenu-mobile-collection" ).on("click", function() {
		$( ".submenu-mobile-collection" ).attr('style', 'display: flex!important');
		$( ".submenu-mobile-shop" ).attr('style', 'display: none!important');
	} );
	$('#new-badge-product-single').appendTo('.iconic-woothumbs-images-wrap');
	$('#new-badge-product-single').css('display', 'flex');
    //hide NEW badge if yith badge is shown on single product page
    if($('new-badge-product-single').length){
        $('.entry-summary .ch_bad_single').hide();
    }
    //hide color swatch of out of stock if ACF 'hide' = yes
    if($('.hide_out_of_stock').length){
        $('li.iconic-was-swatches__item.iconic-was-swatches__item--out-of-stock').each(function () {
            var vl=$(this).find('a').attr('data-attribute-value');
            $('div[data-value='+vl+']').addClass('iconic-was-swatches__item--out-of-stock');
        });
    }
    //Fix title product in menu
    if($('.quadmenu-item-6740.pickup_menu_content').length){
        $('.quadmenu-item-6740.pickup_menu_content a').each(function () {
            if($(this).html()===''){
                $(this).remove();
            }
        });
        $('.quadmenu-item-6740.pickup_menu_content > a').each(function () {
            if($(this).attr('title')!==''){
                var product_title=$(this).closest('.quadmenu-item-6740.pickup_menu_content').find('a.c-product-item--link_title').text();
                $(this).attr('title',product_title);
            }
        });
    }
    //New Date Picker for birthday
    if ($('#account_birth').length) {
        new Rolldate({
            el: '#account_birth',
            format: 'YYYY-MM-DD',
            beginYear: 1940,
            endYear: 2000,
            lang: {
                title: '生年月日を選択',
                cancel: 'キャンセル',
                confirm: '完了',
                year: '年',
                month: '月',
                day: '日',
                hour: '時間',
                min: '分',
                sec: '秒'
            },
            init: function() {
                $('html').addClass('cancel-scroll');
            },
            confirm: function(date) {
                $('html').removeClass('cancel-scroll');
            },
            cancel: function() {
                $('html').removeClass('cancel-scroll');
            }
        })
    }
    if ($('.ul_notion').length) {
        $('.ul_notion > li').each(function () {
            $(this).wrapInner('<span class="txt"></span>');
        });
    }
    $(window).scroll(function () {
        if ($('.fade__inout').length) {
            var coverH = $('.full_cover').innerHeight();
            var top = $('.fade__inout').offset().top; // ターゲットの位置取得
            var position = top - $(window).height();  // ターゲットが上からスクロールしたときに見える位置
            var position_bottom = top + $('.fade__inout').height();  // ターゲットが下からスクロールしたときに見える位置

            if ($(window).scrollTop() > position && $(window).scrollTop() < position_bottom) {
                $(".fade__inout").addClass("in");
                $(".fade__inout").removeClass("out");
            } else {
                $(".fade__inout").addClass("out");
                $(".fade__inout").removeClass("in");
            }
        }
    })

    if ($('.masonry__grid').length) {
        $('.grid').masonry({
            itemSelector: '.grid-item',
            columnWidth: '.grid-item'
        });
    }

    $(window).load(function () {
        $('body').removeClass('fade-out');
    });

    if ($('.wpforms-field select').length) {
        $('.wpforms-field select').each(function () {
            $(this).wrap('<div class="styled-select">');
        });
    }
    if ($('.ywapo_input_container_radio .ywapo_label_price').length) {
        $('.ywapo_input_container_radio .ywapo_label_price').each(function () {
            $(this).parent().parent().addClass('has_ywaop_price');
        });
    }
    if ($('.ywapo_input_container .ywapo_single_option_image').length) {
        $('.ywapo_input_container .ywapo_single_option_image').each(function () {
            $(this).parent().parent().parent().addClass('has_ywaop_img');
        });
    }

    if ($('.wpforms-field-checkbox.wpforms-list-inline').length) {
        $(this).find('li:not(.wpforms-image-choices-item) input + label').prepend('<span class="chebox"></span>')
    }

    if ($('.wpforms-field.group_row').length) {
        var listWrap = $('.wpforms-field-container');
        //$('.wpforms-field.group_row:lt(2)').wrapAll('<div class="field_section"></div>');
        do {
            listWrap.children('.wpforms-field.group_row:lt(2)').wrapAll('<div class="field_section"></div>');
        } while (listWrap.children('.wpforms-field.group_row').length);
    }
    setTimeout(function () {
        if ($('.wpforms-field-container > .field_section').length) {
            $('.wpforms-field-container > .field_section').each(function () {
                if ($(this).find('div').hasClass('wpforms-conditional-hide')) {
                    $(this).addClass('hide_section');
                }

            });
        }
    }, 200);
    if ($('.wpforms-conditional-trigger').length) {
        $('.wpforms-conditional-trigger input').click(function () {
            setTimeout(function () {
                if ($('.wpforms-field-container > .field_section').length) {
                    $('.wpforms-field-container > .field_section').each(function () {
                        if ($(this).find('div').hasClass('wpforms-conditional-hide')) {
                            $(this).addClass('hide_section');
                        }

                    });
                }
            }, 200);
        });
    }

    if ($('.wpforms-submit-container button.wpforms-submit').length) {
        $('.wpforms-submit-container button.wpforms-submit').addClass('ch_disable');
    }

    //validate add to cart
    function check_validate_submit_review() {
        $('form.wpforms-form').each(function (i, field_form) {
            var showRequiredError = false;
            $(field_form).find('input,select,textarea').each(function (i, field) {
                var required = $(this).attr('required');
                if (required) {
                    var type_field = $(field).attr('type');
                    if (type_field === 'radio') {
    //for other case
                    } else if (type_field === 'checkbox') {
    //for other case
                    } else {
                        if ($(this).val() === '') {
                            showRequiredError = true;
                        }
                    }
                }

            });
    //
            $(field_form).find('.wpforms-field-checkbox .wpforms-field-required').each(function () {
                if (showRequiredError === true) {
                    return false;
                }
                var checked_cb = false;
                var detect = false;
                $(this).find('input').each(function (i, field) {
                    var required = $(field).attr('required');
                    var type_field = $(field).attr('type');
                    if (required && type_field !== 'hidden') {
                        detect = true;
                        if ($(field).is(':checked')) {
                            checked_cb = true;
                        }
                    }
                });
                if (!checked_cb && detect === true) {
                    showRequiredError = true;
                }
            });


            $(field_form).find('.wpforms-field-rating .wpforms-field-rating-items').each(function () {
                if (showRequiredError === true) {
                    return false;
                }
                if (!$(this).closest('.wpforms-field-rating').hasClass('wpforms-conditional-hide')) {
                    var checked_rd = false;
                    var detect_rd = false;
                    $(this).find('input').each(function (i, field) {
                        var required = $(field).attr('required');
                        var type_field = $(field).attr('type');
                        if (required && type_field !== 'hidden') {
                            detect_rd = true;
                            if ($(field).is(':checked') || $(field).closest('.wpforms-field-rating-item').hasClass('selected')) {
                                checked_rd = true;
                            }
                        }
                    });
                    if (!checked_rd && detect_rd === true) {
                        showRequiredError = true;
                    }
                }
            });


            if (showRequiredError !== true) {
                $(field_form).find('.wpforms-submit-container button.wpforms-submit').removeClass('ch_disable');
            } else {
                $(field_form).find('.wpforms-submit-container button.wpforms-submit').addClass('ch_disable');
            }
        });
    }
    if($('form.wpforms-form').length){
        check_validate_submit_review();
    }
    $("form.wpforms-form input, form.wpforms-form textarea, form.wpforms-form select").change(function (event) {
        check_validate_submit_review();
    });

    $(window).on('load resize', function () {
        if ($('#menu-item-6741').length) {
            var $win = $(window);
            if ($win.width() < 768) {
                $('#menu-item-6741 ul li > a').each(function () {
                    $(this).wrap('<div class="quad_itemimg__div">');
                });
                $('#menu-item-6741 ul li').each(function () {
                    $(this).find('.mini-product__item, .product_title, .quadmenu-product-float, .quadmenu-product-cart').wrapAll('<div class="quad_iteminfo__div">');
                });
            }
        }
    });
    $('.header-box .quadmenu-navbar-nav').addClass('theme-primary-menu');
    //$('.banner-ad-promo > .item').hide();
    //$('.banner-ad-promo > .item:first-child').show();
    $(".banner-ad-promo > .item:gt(0)").hide();
    setInterval(function () {
        $('.banner-ad-promo > .item:first')
                .hide()
                .next()
                .show()
                .end()
                .appendTo('.banner-ad-promo');
    }, 6000);
    //remove empty p tag
    $('p:not(.keep-p)').each(function () {
        var $this = $(this);
        if ($this.html().replace(/\s|&nbsp;/g, '').length === 0) {
            $this.remove();
        }
    });
    //rearrange WWOOF filter
    $('.woof_redraw_zone .woof_container > .woof_container_inner > .woof_submit_search_form_container').insertAfter('.woof_redraw_zone > .woof_container.woof_container_size');
    //call link only mobile
    var ua = navigator.userAgent;
    if (ua.indexOf('iPhone') > 0 || ua.indexOf('Android') > 0) {
        $('.tel-link').each(function () {
            var str = $(this).text();
            $(this).html($('<a>').attr('href', 'tel:' + str.replace(/-/g, '')).append(str + '</a>'));
        });
    }
    $('.checkbox_label > input[type=checkbox]').change(function () {
        if ($(this).is(":checked")) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }

    });

    function checkRadioCustom() {
        $('.radio-label').each(function () {
            $(this).find('input[type=radio]').each(function () {
                if ($(this).is(':checked')) {
                    $(this).trigger('click');
                    $(this).trigger('change');
                    var optionName = $(this).attr('name');
                    $('input[name="' + optionName + '"]').closest('.radio-label').removeClass('checked');
                    $(this).parent().addClass('checked');
                }
            });
        });
    }

    $('.radio-label').find('input[type=radio]').change(function () {
        if ($(this).is(':checked')) {
            var optionName = $(this).attr('name');
            $('input[name="' + optionName + '"]').closest('.radio-label').removeClass('checked');
            $(this).parent().addClass('checked');
        }
    });
    checkRadioCustom();

    if ($('.banner-ad-promo').length) {
        /***********/
        $('.banner-ad-link').on('click', function (e) {
            e.preventDefault();
            var inst = $('[data-remodal-id=news_top_modal]').remodal();
            var wraper = $(this).closest('.grid-item');
            var pID = $(this).attr('data-id');

            $('body').LoadingOverlay('show');
            $.ajax({
                type: "post",
                url: gl_ajax_url,
                data: {action: 'get_banner_post', id: pID},

            }).success(function (response) {
                $('.remodal_wraper').html(response);
                inst.open();

                $('body').LoadingOverlay('hide');

            });
        });

        /***********/
        $adBar = $('.banner-ad-promo')
        var adNav = $adBar.outerHeight();
        var adNavPos = $adBar.offset().top;
        $(window).on('load scroll', function () {
            var scrollPos = $(this).scrollTop();
            if (scrollPos > adNavPos) {
                //$('.banner-ad-promo').hide();
            } else {
                //$('.banner-ad-promo').show();
            }
        });
    }
    if ($('.sticky_header').length)
    {
        var $win = $(window),
                $nav = $('.header-box'),
                navHeight = $nav.outerHeight(),
                navPos = $nav.offset().top,
                fixedClass = 'is-scroll';



        $win.on('load scroll', function () {
            if (!$('.blog-article').length) {
                var value = $(this).scrollTop();
                if (value > navPos) {
                    $nav.addClass(fixedClass);
                    if ($win.width() > 991) {
                        $('.header-scroll-logo').show();
                    }
                } else {
                    $nav.removeClass(fixedClass);
                    if ($win.width() > 991) {
                        $('.header-scroll-logo').hide();
                    }
                }
            }
        });
    }
    if($('#new_modal').length){
        var newModal = $('[data-remodal-id=new_modal]').remodal();
        setTimeout(function() {
            newModal.open();
        }, 200); // 200ミリ秒（0.2秒）後にモーダルを開く
    }
    if($('#product_specific_modal').length){
        var productModal = $('[data-remodal-id=product_specific_modal]').remodal();
        setTimeout(function() {
            productModal.open();
        }, 200); // 200ミリ秒（0.2秒）後にモーダルを開く
    }
    //mega menu
    //disable category parent menu
    if ($('.quadmenu-item-object-mega').length) {
        var $win = $(window);
        $('.quadmenu-row > .quadmenu-item-has-children > div > ul').addClass('close');
        $win.on("load", function () {

            if ($win.width() > 991) {
                $('.quadmenu-row > .quadmenu-item-has-children > div > ul > li.no_link > a').click(function (e) {
                    e.preventDefault();
                    return false; // Do something else in here if required
                });
            } else {
                $('.quadmenu-row > .quadmenu-item-has-children > div > ul > li.no_link.has_link_sm > a').click(function (e) {
                    e.preventDefault();
                    return true; // Do something else in here if required
                });
            }
            if ($win.width() < 768) {
                $('h2.order--checkout__summary__heading.icon--plus').click(function (e) {
                    e.preventDefault();
                    if ($(this).next().hasClass('toggle--active')) {
                        $(this).removeClass('toggle--active');
                        $(this).parent().removeClass('toggle--active');
                        $(this).next().removeClass('toggle--active');
                    } else {
                        $(this).addClass('toggle--active');
                        $(this).parent().addClass('toggle--active');
                        $(this).next().addClass('toggle--active');
                    }
                    //$(this).next('.toggle--active').toggleClass('toggle--active');
                });
            }
            if ($win.width() > 991) {
            } else {
                $('.quadmenu-row > .quadmenu-item-has-children > div > ul > li.no_link.has_link_sm').click(function (e) {
                    e.preventDefault();
                    $(this).toggleClass('icon-back');
                    $(this).parent().toggleClass('open-child close').toggleClass('open-current');
                    $(this).parent().parent().parent().toggleClass('open-current');
                    if ($(this).parent().parent().parent().hasClass('open-current')) {
                        var ocH = $(this).parent().parent().height();
                        var pbH = $('.banner-ad-promo').innerHeight();
                        $(this).parent().css('height', (ocH + pbH) + 'px');
                    } else {
                        $(this).parent().css('height', 'auto');
                    }

                    $(this).parents('.quadmenu-dropdown-menu').siblings('.quadmenu-dropdown-toggle').toggleClass('close_prvlevel');
                    $(this).parent().parent().parent().siblings('li').toggleClass('close_otherchild');

                });
                //remove class and change css
                $('#menu-toggle-btn').on('click', function () {
                    $('.theme-primary-menu').find('.open-current').removeClass('open-current').addClass('close').css('height', 'auto');
                    $('.theme-primary-menu').find('.open-child').removeClass('open-child');
                });
            }


        });

    }

    //$('.sub-menu.mega-menu-row > li.no_link > a').addClass('flyout-col__heading heading heading--small');

    $('#menu-toggle-btn').on('click', function (e) {
        e.preventDefault();
        var headH = $(".menu-layout-custom .header-container").height();
        var wH = $(window).height();
        var pbH = $(".banner-ad-promo").innerHeight();
        pbH = $(".sticky_header.is-scroll").length ? 0 : pbH;
        pbH = 0;

        $(this).toggleClass('toggle--active');
        $('.menu-layout-custom').toggleClass('toggle--active');
        $('.nav-container').toggleClass('nav--active');
        if ($(this).hasClass('toggle--active')) {
            $('.nav-container').css('height', (wH - headH - pbH) + 'px');
        } else {
            $('.nav-container').css('height', '0');
        }
        $('html').toggleClass('cancel-scroll');
    });
    //add class for select
    $('.form-row select:not(.justselect)').each(function () {
        $(this).addClass('input-select styled-select').wrapAll('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
    });

    //add class for variable product select
    $('.pdp__attribute--group .pdp__attribute.variations__attribute').each(function () {
        var select = $(this).find('select');
        if (!$(select).hasClass('hide')) {
            $(select).addClass('input-select justselect').wrapAll('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
        }
    });
    $('select.orderby').addClass('input-select justselect').wrapAll('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
    //change form field style
    $('.order--checkout--row p.form-row').each(function () {
        //$(this).find('label, .woocommerce-input-wrapper').wrapAll('<div class="field-wrapper"></div>');
        $(this).find('label, .woocommerce-Input').wrapAll('<div class="field-wrapper"></div>');
    });
    //remove cta class from product archive
    $('.related products > ul.products > li.product').each(function () {
        if ($(this).find('.cta-wish')) {
            $('.cta-wish').removeClass('cta');
        }
    });

    // copy shipping address to billing address on checkout page
    function copyBillingAddress() {
        let isChecked = $("#copy_to_billing").is(":checked");

        if (isChecked) {
            $("[name='billing_first_name']").val($("[name='shipping_first_name']").val()).trigger('input').trigger('focusout');
            $("[name='billing_last_name']").val($("[name='shipping_last_name']").val()).trigger('input').trigger('focusout');
            $("[name='billing_address_1']").val($("[name='shipping_address_1']").val()).trigger('input').trigger('focusout');
            $("[name='billing_address_2']").val($("[name='shipping_address_2']").val()).trigger('input').trigger('focusout');
            $("[name='billing_city']").val($("[name='shipping_city']").val()).trigger('input').trigger('focusout');
            $("[name='billing_state']").val($("[name='shipping_state']").val()).trigger('input').trigger('focusout');
            $("[name='billing_zip']").val($("[name='shipping_zip']").val()).trigger('input').trigger('focusout');
            $("[name='billing_country']").val($("[name='shipping_country']").val()).trigger('input').trigger('focusout');
            $("[name='mobile/email']").val($("[name='shipping_phone']").val()).trigger('input').trigger('focusout');
        }
    }

    // Use shipping address as my billing address Checkbox Click
    $(document).on('click', '#copy_to_billing', function () {
        copyBillingAddress();
    });
    
    // Use shipping address as my billing address Checkbox Click
    $(document).on('change', "[name='shipping_first_name'], [name='shipping_last_name'], [name='shipping_address_1'], [name='shipping_address_2'], [name='shipping_city'], [name='shipping_state'], [name='shipping_zip'], [name='shipping_country'], [name='shipping_phone']", function () {
        copyBillingAddress();
    });


    $('body').on('click', 'input[name="mwb_wgm_send_giftcard"]', function () {
        if ($(this).val() == 'Shipping')
        {
            $('#mwb_wgm_message').closest('.field-wrapper').hide();
            $('#mwb_wgm_message').val('---');
        } else {
            //$('#mwb_wgm_message').closest('.field-wrapper').show();
            $('#mwb_wgm_message').val('');
        }
    });

    $('[data-toggle]').on('click', function (e) {
        e.preventDefault();
        $('[data-toggle-target=' + $(this).data('toggle') + ']').toggleClass('toggle--active');
        $(this).toggleClass('toggle--active');
        $(this).parent().toggleClass('toggle--active');
    });


    $('.fade-anitop, .home ul.products > li, .fade-ani').each(function () {
        $(this).addClass('showing');
    });
    $(window).scroll(function () {
        var $win = $(window);

        //fade-in
        $('.fade-anitop, .home ul.products > li').each(function () {
            var elemPos = $(this).offset().top;
            var scroll = $(window).scrollTop();
            var windowHeight = $(window).height();
            if ($win.width() > 991) {
                if (scroll > elemPos - windowHeight + 200) {
                    $(this).addClass('showing');
                } else {
                    $(this).removeClass('showing');
                }
            } else {
                $(this).addClass('showing');
            }
        });
        $('.fade-ani').each(function (i) {
            var bottom_of_object = $(this).offset().top + $(this).outerHeight();
            var bottom_of_window = $(window).scrollTop() + $(window).height();
            if ($win.width() > 991) {
                if (bottom_of_window > bottom_of_object) {
                    $(this).addClass('showing');
                } else {
                    $(this).removeClass('showing');
                }
            } else {
                $(this).addClass('showing');
            }
        });

        /* Check the location of each desired element */
        /*$('.home ul.products > li').each( function(i){
         
         var bottom_of_object = $(this).offset().top + $(this).outerHeight();
         var bottom_of_window = $(window).scrollTop() + $(window).height();
         
         if( bottom_of_window > bottom_of_object ){
         
         $(this).addClass('showing');
         
         }else{
         $(this).removeClass('showing');
         }
         
         }); */
    });
    //option add on
    $(".product-addon-options label:has(input.addon-checkbox)").addClass("check-label");
    /* Checkbox  */
    var checkBoxRow = $('.product-addon-options p.form-row');
    var addClassCheckBox = function ($input) {
        if ($input.prop('checked')) {
            $input.parent().addClass('checked');
        } else {
            $input.parent().removeClass('checked');
        }
    };
    checkBoxRow.on('change', 'input', function () {
        addClassCheckBox($(this));
    });
    function footerScript() {
        $("footer #reg_email").attr("placeholder", "EMAIL ADDRESS");
        $("footer #reg_password").attr("placeholder", "PASSWORD");
    }
    footerScript();

    function NameFormScript() {
        if ($('form').find('.name-field-wrapper, .kana-field-wrapper')) {
            var NameInput = $(this).find('.name-field-wrapper').find('input');
            var KanaInput = $(this).find('.kana-field-wrapper').find('input');
            $(NameInput).addClass('name-field');
            $(KanaInput).addClass('kana-field');
        }
        $.fn.autoKana('#billing_first_name', '#billing_first_name_kana', {katakana: true});
        $.fn.autoKana('#billing_last_name', '#billing_last_name_kana', {katakana: true});
    
        $.fn.autoKana('#shipping_first_name', '#shipping_first_name_kana', {katakana: true});
        $.fn.autoKana('#shipping_last_name', '#shipping_last_name_kana', {katakana: true});
    
        $.fn.autoKana('#account_first_name', '#account_first_name_kana', {katakana: true});
        $.fn.autoKana('#account_last_name', '#account_last_name_kana', {katakana: true});
    }
    if($('#wpforms-2027257-field_3-container input').length){
        var name_jp=$('#wpforms-2027257-field_0-container input');
        var kana_jp=$('#wpforms-2027257-field_3-container input');
        $.fn.autoKana(name_jp, kana_jp, {katakana: true});
    }
    if($('form').length) {
        NameFormScript(); 
    }
    function MwFormScript() {
        $('.mw_wp_form').each(function () {
            var $NameCon = $('input#name');
            var $KanaCon = $('input#name-kana');
            $.fn.autoKana($NameCon, $KanaCon, {katakana: true});
        });
    }
    if($('.mw_wp_form').length) {
        MwFormScript();
    }
    //auto zip input
    function AutoZip() {
        $('body').on('change', '#billing_postcode, #shipping_postcode', function () {
            var zip1 = $.trim($(this).val());
            var zipcode = zip1;
            var elementChange = $(this);
            // Remove error message about postcode
            $('.postcode_fail').remove();

            $.ajax({
                type: "post",
                url: gl_site_url + "dataAddress/api.php",
                data: JSON.stringify(zipcode),
                crossDomain: false,
                dataType: "jsonp",
                scriptCharset: 'utf-8'
            }).done(function (data) {
                var address = [
                    //{postcode : '#deliver_postcode', state : '#deliver_state', city: '#deliver_city', address1: '#deliver_addr1'},
                    {postcode: '#billing_postcode', state: '#billing_state', city: '#billing_city', address1: '#billing_address_1'},
                    {postcode: '#shipping_postcode', state: '#shipping_state', city: '#shipping_city', address1: '#shipping_address_1'},
                ]
               
                $.each(address, function (index, addressItem) {
                    if ($(addressItem['postcode']).length && ('#' + elementChange.attr('id') == addressItem['postcode']))
                    {
                        $(addressItem['state'] + ' option').each(function () {
                            if ($(this).text() == data[0])
                            {
                                $(addressItem['state']).val($(this).attr('value'));
                                $(addressItem['state']).change();
                            }
                        });
                        $(addressItem['city']).val(data[1] + data[2]);
                    }
                });
            });
        });
    }
    if($('#billing_postcode').length || $('#billing_postcode').length) {
        AutoZip();
    }
    //share tools
    $(".sharing-tools").on("click", function () {
        if ($(this).hasClass("-open")) {
            $(this).removeClass("-open");
        } else {
            $(this).addClass("-open");
        }
    });
    //accordion .accordion > li > .acc-toggle
    var acElements = document.querySelectorAll('.acc-toggle, .toggle_show_block__title');
    acElements.forEach((element) => {
        $(element).click(function(){
            // alert('Clicked!'+element);
            if ($(this).hasClass("-open")) {
                $(this).removeClass("-open");
                $(this).next().slideUp(500);
                $(this).find(".acc-icon").removeClass("-close").addClass("-open");
            } else {
                if($(this).parent().siblings('li').length) {
                    $(this).parent().siblings().children('div:first-child').find(".acc-icon").removeClass("-close").addClass("-open");//close others
                    $(this).parent().siblings().children('div:first-child').removeClass("-open");//close others
                    $(this).parent().siblings().children('div:last-child').slideUp(500);//close others
                }
                $(this).find(".acc-icon").removeClass("-open").addClass("-close");
                $(this).addClass("-open");
                $(this).next().slideDown(500);
            }
        });
    });
    
    $('body').on('click', '#book_confirmed', function (e) {
        e.preventDefault();
        var postData = $('form#confirmed_booking_form').serialize();
        postData += '&' + $.param({
            action: 'bookingform_schedule_confirmed'
        });
        $.post(gl_ajax_url, postData, function (data, status, xhr) {
            var response = jQuery.parseJSON(data);
            if (response.success)
            {
                location.href = gl_site_url + "reservation-thanks";
            }
        });
    });
    $('body').on('click', '#book_back', function () {
        location.href = gl_site_url + "reservation";
    });

    $('body').on('click', '#shipping_info_link', function () {
        var inst = $('[data-remodal-id=shipping_info_modal]').remodal();
        inst.open();
    });
    $('body').on('click', '#gcusage', function () {
        var inst = $('[data-remodal-id=gcusage]').remodal();
        inst.open();
    });
    $('body').on('click', '#read_cancel_policy_modal', function () {
        var inst = $('[data-remodal-id=read_cancel_policy_modal]').remodal();
        inst.open();
    });

    $('body').on('click', 'span.pop-up-button-remodal', function () {
        var inst = $('[data-remodal-id=remodal_config_size_info]').remodal();
        inst.open();
    });
    
    $('body').on('click', '.bra_size_info_modal', function () {
        var inst = $('[data-remodal-id=bra_size_info]').remodal();
        inst.open();
    });
    
    $('body').on('click', '.panty_size_info_modal', function () {
        var inst = $('[data-remodal-id=panty_size_info]').remodal();
        inst.open();
    });
    
    $('body').on('click', '.cami_size_info_modal', function () {
        var inst = $('[data-remodal-id=cami_size_info]').remodal();
        inst.open();
    });
    
    $('body').on('click', '.shorts_size_info_modal', function () {
        var inst = $('[data-remodal-id=shorts_size_info]').remodal();
        inst.open();
    });

    function scrollToFormTopCH(formId)
    {
        $("html, body").animate({scrollTop: $('#' + formId).offset().top - 52}, 1000);
    }
    $(document).on('click', '#ch_link_cp', function () {
        var formId = 'ch_about_delivery';
        scrollToFormTopCH(formId);
        $('.acc-parent .acc-icon').addClass('-open');
        $('#ch_about_delivery .acc-toggle').addClass('-open');
        $('#ch_about_delivery .acc-icon').removeClass('-open');
        $('#ch_about_delivery .acc-icon').addClass('-close');
        $('#ch_about_delivery .acc-inner').css('display', 'block');
    });
    function createCheckboxHook()
    {
        jQuery(".woof_checkbox_term").on("ifChecked", function (e) {
            keep_expand_filter_shop();
        });

        jQuery(".woof_checkbox_term").on("ifUnchecked", function (e) {
            keep_expand_filter_shop();
        });

        jQuery(".filterClear").on("click", function (e) {
            keep_expand_filter_shop();
        });

    }

    function keep_expand_filter_shop(is_onload)
    {
        if (!is_onload)
        {
            var toggle_interval = setInterval(function () {
                if (!$('.woof_redraw_zone').find('.woof_container_inner.toggle--active').length)
                {
                    var expanding = [];
                    $('.woof_redraw_zone').find('.woof_container').each(function (index, woof_container) {
                        if ($(this).find('.icheckbox_minimal-aero.checked').length)
                        {
                            expanding.push(index);
                        }
                    });

                    clearInterval(toggle_interval);
                    toggle_interval = null;
                    setTimeout(function () {
                        $.each(expanding, function (index, group_index) {
                            $('.woof_container:eq(' + group_index + ')').find('.toggle__link').click();
                        });
                        createCheckboxHook();
                    }, 100);
                }
            }, 10);
        } else {
            if (!$('.woof_redraw_zone').find('.woof_container_inner.toggle--active').length)
            {
                var expanding = [];
                $('.woof_redraw_zone').find('.woof_container').each(function (index, woof_container) {
                    if ($(this).find('.icheckbox_minimal-aero.checked').length)
                    {
                        expanding.push(index);
                    }
                });

                $.each(expanding, function (index, group_index) {
                    $('.woof_container:eq(' + group_index + ')').find('.toggle__link').click();
                });
            }
            createCheckboxHook();
        }
    }
    setTimeout(function () {
        keep_expand_filter_shop(true);
    }, 1000);


    if ($('.contact-form input[name="ctf-name"]').length)
    {
        $('.contact-form input[name="ctf-name"]').val(gl_user_name);
    }
    $('body').on('click', '.shop-content #gallery-image img', function (e) {
        var inst = $('[data-remodal-id=product_image_modal]').remodal();
        var carousel_image = $('.shop-content .pro-carousel-image');
        var carousel_thumb = $('.shop-content .pro-carousel-thumb');
        $('#product_image_modal .single-product-gallery').removeClass('width_50');
        $('#product_image_modal .single-product-gallery').addClass('width_100');
        $('#product_image_modal .single-product-gallery').html(carousel_image);
        $('#product_image_modal .single-product-gallery').append(carousel_thumb);
        $('#product_image_modal .single-product-gallery').css('opacity', 0);
        inst.open();

        $('#product_image_modal').height($('#product_image_modal .single-product-gallery').height());

        $('.slick-dots').remove();
        $('.slick-next').remove();
        $('.slick-prev').remove();

        if ($('#gallery-image.slick-slider').length)
        {
            $('#gallery-image').slick('setPosition');
        }

        $('#product_image_modal .single-product-gallery').css('opacity', 1);
    });

    $(document).on('closing', '#product_image_modal', function (e) {
        var carousel_image = $('#product_image_modal .pro-carousel-image');
        var carousel_thumb = $('#product_image_modal .pro-carousel-thumb');
        $('#product_image_modal .single-product-gallery').css('opacity', 0);
        $('#product_image_modal .single-product-gallery').removeClass('width_100');
        $('#product_image_modal .single-product-gallery').addClass('width_50');

        $('.slick-dots').remove();
        $('.slick-next').remove();
        $('.slick-prev').remove();

        if ($('#gallery-image.slick-slider').length)
        {
            $('#gallery-image').slick('setPosition');
        }

        $('.shop-content .single-product-gallery').html(carousel_image);
        $('.shop-content .single-product-gallery').append(carousel_thumb);

        if ($('#gallery-image.slick-slider').length)
        {
            $('#gallery-image').slick('setPosition');
        }
    });


    $('body').on('click', '.cancel-appointment-btn', function (e) {
        e.preventDefault();

        if (!confirm(gl_cancel_appointment_alert))
            return '';

        appointment_id = $(this).data('id');
        var wraper = $(this).closest('.appointment_item');
        var btn_el = (this);
        jQuery('body').LoadingOverlay('show');
        $.ajax({
            type: "post",
            url: gl_ajax_url,
            data: {appointment_id: appointment_id, action: 'cancel_appointment'},
            dataType: "json"
        }).done(function (response) {
            if (response.success)
            {
                wraper.removeClass('status-active');
                wraper.addClass('status-cancelled');
                wraper.find('.booking-status .value').text(response.status);
                btn_el.remove();
            }
            jQuery('body').LoadingOverlay('hide');
        });
    });

    $(document).on('click', '.cancel_order_btn', function (event) {
        if (confirm(gl_cancel_order_alert_text))
        {
            jQuery('body').LoadingOverlay('show');
            event.preventDefault();
            var order_id = $(this).data('id');
            var cancel_btn = $(this);
            $.ajax({
                type: "post",
                url: gl_ajax_url,
                data: {order_id: order_id, action: 'customer_cancel_order'},
                dataType: "json"
            }).done(function (response) {
                if (response.success)
                {
                    cancel_btn.fadeOut(function () {
                        cancel_btn.remove();
                    });
                    if (response.payment == 'stripe' || response.payment == 'payid') {
                        cancel_btn.closest('.box.order').find('.order-status .value').removeClass('icon-processing').addClass('icon-refunded').text(response.status);
                        cancel_btn.closest('.box.order').find('.order__total .value').html(response.render_total);
                    } else {
                        cancel_btn.closest('.box.order').find('.order-status .value').text(response.status);
                    }
                }
                if (response.payment == 'stripe') {
                    if (response.error) {
                        alert(response.error);
                    }
                }
                jQuery('body').LoadingOverlay('hide');
            });
        }
    });

    $(document).on('click', 'input[name="shipping_delivery_option"]', function (event) {
        jQuery('body').LoadingOverlay('show');
        var shipping_delivery_option = $(this).val();
        $.ajax({
            type: "post",
            url: gl_ajax_url,
            data: {shipping_delivery_option: shipping_delivery_option, action: 'select_shipping_delivery_option'},
            dataType: "json"
        }).done(function (response) {
            if (response.success)
            {
                // trigger udpate shipping button
                if ($('button[name="calc_shipping"]').length)
                    $('button[name="calc_shipping"]').trigger('click');
                else if ($('#shipping_postcode').length)
                    $('#shipping_postcode').trigger('change');
                $('body').trigger('update_checkout');
            }
            setTimeout(function () {
                jQuery('body').LoadingOverlay('hide');
            }, 1000);
        });
    });
    $(document).click(function (event) {
        setTimeout(function () {
            if ($(".quick-view-open").length && !$(event.target).closest("ul.products").length && !$(event.target).closest(".shop-quick-view-container").length) {
                $('#shop-quick-view .quick-view-close-btn').trigger('click');
            }
        }, 50);
    });
    //for ppom plugin
    if (typeof ppom_input_vars !== "undefined") {
        var obj_ppom = JSON.parse(JSON.stringify(ppom_input_vars));
        $.each(obj_ppom.ppom_inputs, function (index, value) {
            if (value.required === 'on') {
                $('.single_add_to_cart_button').addClass('ch-disable-add-to-cart');
                return false;
            }
        });
    }
    $('.ppom-palettes label').click(function () {
        $(".ppom-palettes label").removeClass('active-palette');
        $(this).addClass('active-palette');
        $('.single_add_to_cart_button').removeClass('ch-disable-add-to-cart');
    });
    if($('.container-contact').length) {
        $('body').on('click', '.container-contact .mw_wp_form input[name="submitConfirm"]11', function (e) {
            e.preventDefault();
            var submitButton = $(this);
            var contactForm = $(this).closest('form');
    
            if (typeof contact_validations != 'undefined')
            {
                jQuery.each(contact_validations, function (index, validation) {
                    if (contactForm.closest('#mw_wp_form_mw-wp-form-' + validation.form_id).length)
                    {
                        if (validation.validation && validation.validation.length)
                        {
                            var field_validations = validation.validation;
                            jQuery.each(field_validations, function (index, field_validation) {
                                var target_field = contactForm.find('[name="' + field_validation.target + '"]');
                                if (target_field.length)
                                {
                                    if (field_validation.target == 'email') {
                                        target_field.addClass('validate[required,custom[email]]');
                                    } else {
                                        if (field_validation.noempty == 1) {
                                            target_field.addClass('validate[required]');
                                        }
                                    }
                                }
                            });
    
                        }
                    }
                });
    
                $('.dropdown').removeClass('inputError');
                $('.formError.inline').remove();
    
                contactForm.validationEngine({promptPosition: 'inline', addFailureCssClassToField: "inputError", bindMethod: "live"});
                var validate = contactForm.validationEngine('validate');
    
                if (validate)
                {
                    submitButton.prop('disabled', true);
                    contactForm.submit();
                }
            }
            return false;
        });
    }
    
    //end
    $(document).on({
        mouseenter: function () {
            $(this).find(".iconic-was-swatches").find('img').each(function (i, field) {
                var src_img = $(field).attr('src');
                $(field).attr('srcset', src_img);
            });
            $(this).find(".iconic-was-swatches").css('opacity', "1");
        },
        mouseleave: function () {
            $(this).find(".iconic-was-swatches").css('opacity', "0");
        }
    }, 'ul.products li.product');

    //another popup on popup size
    if ($('#size_chart_content_popup #size-chart').length) {
        var jis = $('.show_jis_area').clone();
        $('.show_jis_area').remove();
        $("#size_chart_content_popup #size-chart").first().after(jis);
        $('body').on('click', '.show_jis_area a', function () {
            var inst = $('[data-remodal-id=remodal_jis_area]').remodal();
            inst.open();
        });
    }
    $(window).on('load resize', function () {
        var $win = $(window);
        if ($win.width() < 768 && !$('table.jan-size.clone').length) {
            $(".jan-size").clone(true).appendTo('#jis_crontable').addClass('clone clones');
            //for View all menu on mobile
            $('.view_all_mobile').remove();//remove if exists
            $('<li class="view_all_mobile quadmenu-item quadmenu-item-object-column quadmenu-item-type-column col-12 col-sm-4 col-md-2 col-lg-2"><a href="https://staging.chiyono-anne.com/shop-all/"> <span class="quadmenu-item-content"> <span class="quadmenu-text  hover t_1000">VIEW ALL</span> </span> </a></li>').insertAfter("#menu-item-3536"); //insert after SERIES menu
        } else {
            //remove View all menu on desktop
            $('.view_all_mobile').remove();
            $('#jis_crontable .jan-size.clone.clones').remove();
        }
    });
    //remodal for Product Size
    if($('#remodal_jis_area').length) {
        $('body').on('click', '#remodal_jis_area .link_center', function () {
            var inst = $('[data-remodal-id=remodal_jis_area]').remodal();
            inst.close();
            var inst_prev_modal = $('[data-remodal-id=remodal_config_size_info]').remodal();
            inst_prev_modal.open();
        });
    }

    $('.privacy_policy_reg input[type=checkbox]').change(function () {
        if ($(this).is(":checked")) {
            $(this).parent().addClass('checked');
        } else {
            $(this).parent().removeClass('checked');
        }
    });

    if ($('div.woocommerce.list-shops').length) {
        $('div.woocommerce.list-shops ul li .c-product-item_wrap_info').each(function () {
            $(this).find('.subtitle, .pro-swatch-list, .price, .iconic-was-swatches').wrapAll('<div class="toggle_con">');
            $(this).after('<span class="acc-icon ch-acc-icon"></span>');
            var permalink = $(this).parent().find('.product-image-wrapper a.woocommerce-loop-product__link').attr('href');
            $(this).find('.toggle_con').append('<a href="' + permalink + '" class="details_btn">View Details</a>');
        });
    }
    $('body').on('click', '.ch-acc-icon', function () {
        if ($(this).prev('.c-product-item_wrap_info').hasClass('is_shown') && $(this).parent().parent('.c-product-item').hasClass('is_shown')) {
            $(this).prev('.c-product-item_wrap_info').removeClass('is_shown');
            $(this).parent().parent('.c-product-item').removeClass('is_shown');
            $(this).removeClass('-open');
            $(this).closest('li').removeClass('is_shown');
        } else {
            $(this).prev('.c-product-item_wrap_info').addClass('is_shown');
            $(this).parent().parent('.c-product-item').addClass('is_shown');
            $(this).addClass('-open');
            $(this).closest('li').addClass('is_shown');
        }
    });
    setTimeout(function () {
        if ($('.woocommerce-form-register').length) {
            $('.woocommerce-form-register #account_birth_year').removeAttr('required');
            $('.woocommerce-form-register #account_birth_month').removeAttr('required');
            $('.woocommerce-form-register #account_birth_day').removeAttr('required');
        }
    }, 200);

    if ($('.single-product').length) {
        $('#ywapo_value_15').hide();
        $('body').on('click', "ul[data-attribute='attribute_pa_embroidery'] li a", function () {
            if ($(this).attr('data-attribute-value') != 'none') {
                $('#ywapo_value_15').show();
            } else {
                $('#ywapo_ctrl_id_15_0').val('');//textbox 刺繍文字
                $('#ywapo_value_15').hide();
            }
        });
    }
    $(window).on('load', function () {
        if ($('.iconic-woothumbs-images__image').length) {
            $('.iconic-woothumbs-images__image').each(function () {
                var caption = $(this).attr('caption');
                var data_caption = $(this).attr('data-caption');
                if (caption === '' || typeof (caption) == "undefined") {
                    caption = data_caption;
                }
                if (caption !== '' && typeof (caption) != "undefined") {
                    $(this).parent().find('p.ch_caption').remove();
                    $(this).after("<p class='ch_caption ch_hide_default'>" + caption + "</p>");
                }
            });
            $('p.ch_caption').removeClass('ch_hide_default');
        }
    });
    if ($('.prod-info .woocommerce-Price-amount bdi').length) {
        org_price = $('.prod-info .woocommerce-Price-amount bdi').text();
        org_price = org_price.replace(',', '');
        org_price = org_price.replace('¥', '');
        org_price = parseFloat(org_price);
    }
    if ($('.single-product script.iconic-was-fees').length) {
        var iconic_was_fees_data = $('.single-product script.iconic-was-fees').text();
        if (iconic_was_fees_data !== '') {
            var txt = $("div.prod-info p.price").text();
            if (txt.search("From") == '-1') {
                $("div.prod-info p.price").prepend('From: ');
            }
        }
    }
    $('body').on('click', 'div[data-attribute="pa_color"]', function () {
        setTimeout(function () {
            if ($('.iconic-woothumbs-images__image').length) {
                $('.iconic-woothumbs-images__image').each(function () {
                    var caption = $(this).attr('caption');
                    var data_caption = $(this).attr('data-caption');
                    if (caption === '' || typeof (caption) == "undefined") {
                        caption = data_caption;
                    }
                    if (caption !== '' && typeof (caption) != "undefined") {
                        $(this).parent().find('p.ch_caption').remove();
                        $(this).after("<p class='ch_caption ch_hide_default'>" + caption + "</p>");
                    }
                });
                $('p.ch_caption').removeClass('ch_hide_default');
            }
        }, 400);
    });
    $('body').on('click', '.iconic-was-swatches__item', function () {
        var current_this = $(this);
        console.log('checking' + org_price);
        setTimeout(function () {
            if ($('.iconic-woothumbs-images__image').length) {
                $('.iconic-woothumbs-images__image').each(function () {
                    var caption = $(this).attr('caption');
                    var data_caption = $(this).attr('data-caption');
                    if (caption === '' || typeof (caption) == "undefined") {
                        caption = data_caption;
                    }
                    if (caption !== '' && typeof (caption) != "undefined") {
                        $(this).parent().find('p.ch_caption').remove();
                        $(this).after("<p class='ch_caption ch_hide_default'>" + caption + "</p>");
                    }
                });
                $('p.ch_caption').removeClass('ch_hide_default');
            }
            //For Special Color for vairable option to change price display
            if ($('.single-product script.iconic-was-fees').length) {
                var iconic_was_fees_data = $('.single-product script.iconic-was-fees').text();
                var is_pa_color = current_this.closest('ul.iconic-was-swatches').attr('data-attribute');
                if (iconic_was_fees_data !== '' && (is_pa_color == 'attribute_pa_color' || $('ul[data-attribute="attribute_pa_color"] .iconic_colors_extrafee a').hasClass('iconic-was-swatch--selected'))) {//only for color attribute
                    const obj_fee = JSON.parse(iconic_was_fees_data);
                    //if(obj_fee.pa_color!==''){
                    if (("pa_color" in obj_fee)) {
                        var dav = $('ul[data-attribute="attribute_pa_color"] .iconic_colors_extrafee a.iconic-was-swatch--selected').attr('data-attribute-value');//current_this.find('a.iconic-was-swatch').attr('data-attribute-value');
                        if (dav !== '') {
                            //current price
                            var current_price = $('.prod-info .woocommerce-Price-amount bdi').text();
                            current_price = current_price.replace(',', '');
                            current_price = current_price.replace('¥', '');
                            current_price = parseFloat(current_price);
                            //end
                            if (current_this.find('a.iconic-was-swatch').hasClass('iconic-was-swatch--selected') || $('ul[data-attribute="attribute_pa_color"] .iconic_colors_extrafee a').hasClass('iconic-was-swatch--selected')) {//if selected
                                var fee = obj_fee.pa_color[dav];
                                if (typeof fee !== 'undefined' && typeof fee !== undefined) {
                                    fee = fee + fee * 0.1;

                                    var new_price = parseFloat(org_price) + parseFloat(fee);
                                    console.log('price1:' + new_price);
                                    var new_price_html = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">¥</span>' + formatNumber(new_price, '.', ',') + '</bdi></span>';
                                    $('.prod-info p.price').html(new_price_html);
                                    $('.single_variation_wrap .woocommerce-variation-price').html('¥' + formatNumber(new_price, '.', ','));
                                } else {
                                    console.log('price2:' + org_price);
                                    var new_price_html = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">¥</span>' + formatNumber(org_price, '.', ',') + '</bdi></span>';
                                    $('.prod-info p.price').html(new_price_html);
                                }
                            } else {//if unselected
                                var fee = obj_fee.pa_color[dav];
                                if (typeof fee !== 'undefined' && typeof fee !== undefined && current_price != org_price) {
//                                    fee = fee + fee * 0.1;
//                                    var new_price = parseFloat(current_price) - parseFloat(fee);
                                    console.log('price3:' + org_price);
                                    var new_price_html = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">¥</span>' + formatNumber(org_price, '.', ',') + '</bdi></span>';
                                    $('.prod-info p.price').html(new_price_html);
                                } else {
                                    console.log('price4:' + org_price);
                                    var new_price_html = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">¥</span>' + formatNumber(org_price, '.', ',') + '</bdi></span>';
                                    $('.prod-info p.price').html(new_price_html);
                                }
                            }
                        }
                    }
                }
            }
        }, 400);
    });

    //Form Validation
    $('body').on('change', 'form.variations_form', function () {
        setTimeout(function () {
            var price_top = $('.prod-info p.price span.woocommerce-Price-amount.amount').text();
            if (price_top == '' || price_top == 'undefined' || typeof price_top == undefined) {
                price_top = $('.prod-info p.price').text();
            }
            price_top = convert_htmlprice_to_float(price_top);
            // console.log('a:' + price_top);
            if ($('.ywapo_tr_product_base_price').length) {
                var option_price = $('.yith_wapo_group_option_total').text();
                option_price = convert_htmlprice_to_float(option_price);
                // console.log('op:' + option_price);
                var yith_wapo_group_final_total = parseFloat(price_top) + parseFloat(option_price);
                if (yith_wapo_group_final_total > 0) {
                    // console.log('total:' + yith_wapo_group_final_total);
                    $('.yith_wapo_group_product_price_total span.price.amount').html('¥' + formatNumber(price_top, '.', ','));
                    $('.yith_wapo_group_final_total span.price.amount').html('¥' + formatNumber(yith_wapo_group_final_total, '.', ','));
                }
            }
            rewrite_leng_tag();
        }, 500);
    });
    //For video player
    if ($('#circle-play-b').length) {
        const video = document.getElementById("video");
        const circlePlayButton = document.getElementById("circle-play-b");

        function togglePlay() {
            if (video.paused || video.ended) {
                video.play();
            } else {
                video.pause();
            }
        }
        $('body').on('click', '#circle-play-b', function () {
            togglePlay();
        });
        var attr_autoplay = $('video').attr('autoplay');

        // For some browsers, `attr` is undefined; for others, `attr` is false. Check for both.
        if (typeof attr_autoplay !== typeof undefined && attr_autoplay !== false) {
            video.play();
        }

        //circlePlayButton.addEventListener("click", togglePlay);
        video.addEventListener("playing", function () {
            circlePlayButton.style.opacity = 0;
        });
        video.addEventListener("pause", function () {
            circlePlayButton.style.opacity = 1;
        });
    }
    //Shop Catalog?
    if ($('span.has_new').length) {
        var cloned_html = $('span.has_new').clone();
        $('span.has_new').addClass('need_del');
        $('span.has_new').closest('li').find('.quadmenu-item-content').append(cloned_html);
        $('span.has_new.need_del').remove();
    }
    if ($('body.archive.category-blog').length) {
        $('span.crums-item.last-item span').html('CA STORY');
    }

    if ($('body.digitalcard_tag').length) {
        if ($('#mwb_wgm_to_name_optional').length) {
            $('#mwb_wgm_to_name_optional').hide();
        }
        if ($('#mwb_wgm_from_name').length) {
            $('#mwb_wgm_from_name').closest('.form-row').hide();
        }
    }
    //hide attribute on Configurator when qty in stock = 0
    if (typeof jckpc_inventory !== 'undefined') {
        $.each(jckpc_inventory, function (index, row) {
            $.each(row, function (index_ch, row_ch) {
                if (row_ch == 0) {
                    var index_ch_ex_ngang = index_ch.replace("-", " ");
                    var index_ch_up_first = index_ch_ex_ngang.replace(/(^\w{1})|(\s{1}\w{1})/g, match => match.toUpperCase());
                    $('ul[data-attribute="attribute_' + index + '"] a[data-attribute-value="' + index_ch_up_first + '"]').addClass('ch_disable_select');
                }
            });
        });
    }
    setTimeout(function () {
        if ($('.ywapo_options_container').length) {
            $('.ywapo_options_container .ywapo_input_container img.ywapo_single_option_image').each(function () {
                var src = $(this).attr('src');
                if (typeof src !== 'undefined') {
                    var res_large = src.split("?");
                    if (res_large[0] !== '') {
                        var new_image = res_large[0] + '?resize=126%2C126&ssl=1';
                        $(this).attr('src', new_image);
                    }
                }
            });
        }
    }, 1500);

    if ($('#reserve_notify').length) {
        var ck_reserve_notify = getChCookie("ck_reserve_notify");
        if (ck_reserve_notify === '') {
            setChCookie('ck_reserve_notify', 'yes', 1);
            var inst = $('[data-remodal-id=reserve_notify]').remodal();
            inst.open();
        }
    }
    //for mailpoet_woocommerce_checkout_optin
    if ($('#mailpoet_woocommerce_checkout_optin').length) {
        $('body').on('click', '.woocommerce-checkout #step-2 .js-next', function () {
            $('#mailpoet_woocommerce_checkout_optin').prop("checked", true);
            $('#mailpoet_woocommerce_checkout_optin').closest("label.checkbox").addClass('checked');
        });

        $('body').on('click', '#mailpoet_woocommerce_checkout_optin_field', function () {
            if ($(this).find('.input-checkbox').is(":checked")) {
                $(this).find("label.checkbox").addClass('checked');
            } else {
                $(this).find("label.checkbox").removeClass('checked');
            }
        });
    }
    //For Special Color for vairable option
    if ($('.single-product script.iconic-was-fees').length) {
        var iconic_was_fees_data = $('.single-product script.iconic-was-fees').text();
        if (iconic_was_fees_data !== '') {
            const obj_fee = JSON.parse(iconic_was_fees_data);
            //if(obj_fee.pa_color!==''){//only for pa_color
            if (("pa_color" in obj_fee)) {
                if ($('label.pdp__attribute__label').attr('for') == 'pa_color') {
                    $('label[for="pa_color"] strong').append('(別注カラー有り)');
                }
                for (const property in obj_fee.pa_color) {
                    $('li.iconic-was-swatches__item').each(function () {
                        if (property == $(this).find('a').attr('data-attribute-value')) {
                            $(this).addClass('iconic_colors_extrafee');
                        }
                    });
                }
            }
            //for rewrite length tag
            rewrite_leng_tag();
        }
    }
    function rewrite_leng_tag(){
        if ($('.single-product script.iconic-was-fees').length) {
            var iconic_was_fees_data = $('.single-product script.iconic-was-fees').text();
            if (iconic_was_fees_data !== '') {
                const obj_fee = JSON.parse(iconic_was_fees_data);
                //for rewrite select tag
                setTimeout(function () {
                    for (const select_tag in obj_fee) {
                        if($('#'+select_tag).length){
                            for (const key_lgh in obj_fee[select_tag]) {
                                var current_tx=$('#'+select_tag+' option[value="'+key_lgh+'"]').text();
                                current_tx = current_tx.replace(' (+¥'+formatNumber(obj_fee[select_tag][key_lgh], '.', ',')+')', '');
                                $('#'+select_tag+' option[value="'+key_lgh+'"]').text(current_tx+' (+¥'+formatNumber(obj_fee[select_tag][key_lgh], '.', ',')+')');
                                //update price include tax of option value
                                if(key_lgh==$('#'+select_tag).val()){
                                    var fee=obj_fee[select_tag][key_lgh];
                                    fee = fee + fee * 0.1;
                                    var new_price = parseFloat(org_price) + parseFloat(fee);
                                    //console.log('include tax:' + new_price);
                                    $('.prod-info p.price').html('¥' + formatNumber(new_price, '.', ','));
                                    $('.single_variation_wrap .woocommerce-variation-price').html('¥' + formatNumber(new_price, '.', ','));
                                }
                            }
                        }
                    }
                }, 250);
            }
        }
    }
    if ($('#event_modal').length) {
        var event_modal = getChCookie("event_modal");
        if ((event_modal === '' && !$('.post-password-form').length) || $('[data-remodal-id=event_modal]').attr('expired')=='yes') {
            setChCookie('event_modal', 'yes', 1);
            var inst = $('[data-remodal-id=event_modal]').remodal();
            inst.open();
        }
    }
    if ($('#event_modal_myaccount').length) {
        var event_modal_myaccount = getChCookie("event_modal_myaccount");
        if ((event_modal_myaccount === '' && !$('.post-password-form').length) || $('[data-remodal-id=event_modal_myaccount]').attr('expired')=='yes') {
            setChCookie('event_modal_myaccount', 'yes', 1);
            var inst = $('[data-remodal-id=event_modal_myaccount]').remodal();
            inst.open();
        }
    }
    if ($('.woocommerce-account').length) {
        if ($('#account_email').length) {
            $('.dig_wc_nw_phone').closest('.woocommerce-FormRow--wide').removeClass('form-row-wide');
            var phone = $('.dig_wc_nw_phone').closest('.woocommerce-FormRow--wide').clone();
            $('.dig_wc_nw_phone').closest('.woocommerce-FormRow--wide').remove();
            $('#account_email').closest('.form-row').after(phone);
        }
    }
    //hide attribute_pa_color_picker_label if not yet seletect
    if ($('#picker_pa_color .select-option.selected').length) {
        $('.attribute_pa_color_picker_label.swatch-label').show();
    } else {
        $('.attribute_pa_color_picker_label.swatch-label').hide();
    }
    $('body').on('click', '#picker_pa_color .select-option', function () {
        if ($('#picker_pa_color .select-option.selected').length) {
            $('.attribute_pa_color_picker_label.swatch-label').show();
        } else {
            $('.attribute_pa_color_picker_label.swatch-label').hide();
        }
    });
    //end
    //size modal in bundle product
    $('body').on('click', 'span.pop-up-button-remodal-bundle', function () {
        var product_id = $(this).attr('bundle_product_id');
        if (product_id > 0) {
            var inst = $('[size_char_bundle=' + product_id + ']').remodal();
            inst.open();
        }
    });//end
    $(document).on('click', 'a.btb_ch_apply_coupon', function (event) {
        var coupon = $(this).attr('coupon');
        $('body').LoadingOverlay('show');
        if (coupon != '') {
            $('form.checkout_coupon #coupon_code').val(coupon);
            $('form.checkout_coupon button').click();
        }
        $('body').LoadingOverlay('hide');
    });
    $('body').on('click', '.link_modal.pop-up-button-remodal', function () {
        var inst = $('[data-remodal-id=remodal_rank_info]').remodal();
        inst.open();
    });
    $('body').on('click', '.openrepair.pop-up-button-remodal', function () {
        var inst = $('[data-remodal-id=repairModal]').remodal();
        inst.open();
    });
    //add class for bundle variable product select
    setTimeout(function () {
        $('.bundled_item_cart_content.variations_form .attribute_options').each(function () {
            var select = $(this).find('select');
            if (!select.hasClass('hide')) {

                select.addClass('input-select justselect').wrapAll('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
            }
        });
    }, 250);
    if ($('.product-type-bundle').length) {
        setTimeout(function () {
            if ($('.prod-info .price span.from').length) {
                $('.prod-info .price span.from').text('From: ');
            }
            if ($('.c-product-item_wrap_info span.price span.from').length) {
                $('.c-product-item_wrap_info span.price span.from').text('From: ');
            }
        }, 100);
        if ($('.bundle_form').length) {
            $('.bundled_item_optional .bundled_product_checkbox').change(function () {
                var me = $(this);
                var me_name = me.attr('name');
                var prev_price = '';
                if (me.is(":checked")) {
                    setTimeout(function () {
                        $('.bundled_item_optional .bundled_product_checkbox').each(function (i, field) {
                            var look_name = $(field).attr('name');
                            if ($(field).is(':checked') && me_name != look_name) {
                                prev_price = $(field).closest('.bundled_product_optional_checkbox').find('.woocommerce-Price-amount').text();
                                $(field).click();
                            }
                        });

                        if (prev_price != '') {
                            prev_price = prev_price.replace(',', '');
                            prev_price = prev_price.replace('¥', '');
                            prev_price = parseFloat(prev_price);
                            var current_total = $('.bundle_price .woocommerce-Price-amount').text();
                            console.log(prev_price + 'caca' + current_total);
                            current_total = current_total.replace(',', '');
                            current_total = current_total.replace('¥', '');
                            current_total = parseFloat(current_total);
                            current_total = current_total - prev_price;
                            $('.bundle_price .woocommerce-Price-amount').html('<span class="woocommerce-Price-currencySymbol">¥</span>' + formatNumber(current_total, '.', ','));
                        }
                    }, 100);
                }
            });
        }
    }

    $(document.body).on('applied_coupon_in_checkout removed_coupon_in_checkout', function (event) {
        $.ajax({
            type: "post",
            url: gl_ajax_url,
            data: {action: 'mr_time_number_coupon'},

        }).success(function (response) {
            if ($('.woocommerce-checkout #secondary .coupon_left').length) {
                if (response != '') {
                    if ($('.woocommerce-checkout #secondary .gold_coupon').length) {
                        $('.woocommerce-checkout #secondary .gold_coupon').show();
                    }
                    if ($('.woocommerce-checkout #secondary .silver_coupon').length) {
                        $('.woocommerce-checkout #secondary .silver_coupon').show();
                    }
                    $('.woocommerce-checkout #secondary .coupon_left').text(response);
                } else {
                    if ($('.woocommerce-checkout #secondary .gold_coupon').length) {
                        $('.woocommerce-checkout #secondary .gold_coupon').hide();
                    }
                    if ($('.woocommerce-checkout #secondary .silver_coupon').length) {
                        $('.woocommerce-checkout #secondary .silver_coupon').hide();
                    }
                }
            }
        });
    });

    $('body').on('click', '.ch_gtw_show_modal_option', function () {
        var inst = $('[data-remodal-id=gift_wrapper]').remodal();
        inst.open();
    });

    $('body').on('click', '.ch_add_gift_wrap_to_cart', function () {
        var url = $('#gift_wrapper .gift_wrap_options').val();
        window.location.replace(url);
    });

    if ($('.woof_container_product_cat').length) {
        setTimeout(function () {
            $('.woof_childs_list_li').each(function () {
                if (!$(this).find('ul.woof_childs_list li').length) {
                    $(this).addClass('ch_hide');
                }
            });
        }, 50);
    }
    
    if($('.myaccount-dashboard').length){
        setTimeout(function () {
            var $win = $(window);
            if ($win.width() < 768) {
                $('.myaccount-dashboard h2.icon--plus').click();
            }
        }, 100);
    }
    //click choose type shipping to sure load correct value
    if($('input[name="shipping_delivery_option"]').length){
        $('input[name="shipping_delivery_option"]').each(function () {
            if ($(this).is(":checked")) {
                $(this).click();
            }
        });
    }
    //lightbox image on single post
    $('body').on('click', 'a[data-rel="lightbox"]', function (e) {
        e.preventDefault();
        var img=$(this).attr('href');
        $('[data-remodal-id=modal_img_single_post] .remodal_body').html('<img class="attachment-full size-full" src="'+img+'"/>');
        var inst = $('[data-remodal-id=modal_img_single_post]').remodal();
        inst.open();
        return false;
    });
    //msg bellow digit input
    if($('#digit_ac_otp_container').length){
        $('#digit_ac_otp_container').append('<span><em>ワンタイムパスワードは携帯番号のSMSへ送信されます</em></span>');
    }
    
    //bundle product link
    setTimeout(function () {
        if($('.ch_product_bdl_link').length){
            $('.ch_product_bdl_link').each(function () {
                $(this).attr('href',$(this).attr('ch_bdl_link'));
            });
        }
    }, 500);
    
    //show gift options
    if ($('#show_hide_gift_options').length) {
        $('body').on('click', '#show_hide_gift_options', function (e) {
            if (!$('#show_hide_gift_options').is(':checked')) {
                $('.gift_options_area .gift_products').hide();
                $('.gift_products .cb').prop('checked', false);
            } else {
                $('.gift_options_area .gift_products').show();
            }
        });
        //change price of variable
        $(".gift_products select.gp_atr").change(function (event) {
            var element = $(this).find('option:selected'); 
            var price = element.attr("price");
            $(this).closest('.gp_option_item').find('span.gp_opt__price').html(price);
        });
        //select gift even
        $('body').on('click', '.gift_products .cb', function (e) {
            if($(this).val()=='384196'){
                if ($('input.cb[value="384196"]').is(':checked')) {
                    
                }else{
                    $('.gift_products .cb').prop('checked', false);
                }
            }else{
                if ($('input.cb[value="384196"]').is(':checked')) {
                    //passed
                }else{
                    var product_name=$(this).closest('label').find('.gp_opt__title').text();
                    alert(product_name+'はGift Boxオプションのため、単品では追加できません。');
                    $(this).prop('checked', false);
                }
            }
        });
    }
    function EventShop() {
        $(".ch_link_event").each(function () {
            var product_id = $(this).attr('id');
            var link = $(this).attr('ch_link_event');
            $('li.post-' + product_id).find('a.woocommerce-loop-product__link').attr('href', link);
            $('li.post-' + product_id).find('a.add_to_cart_button').attr('href', link);
            $('li.post-' + product_id).find('a.hover_link').attr('href', link);
            $('li.post-' + product_id).find('a.c-product-name_link').attr('href', link);
        });
    }
    if ($('.event_products').length) {
        EventShop();
    }
    //NOTE: not yet use because COVID, so can't open offline store. Will use in future
    function CouponUsageRule() {
        $('body').on('click', 'form.checkout_coupon button.button', function (e) {
            e.preventDefault();
            var coupon= $('#coupon_code').val();
            var res = coupon.substring(0, 2);
            if(res=='ca'||res=='CA'){
                //continue
                $('.checkout_coupon').submit();
            }else{
                console.log('invalid coupon.');
                $('.woocommerce > div.woocommerce-notice-wrapper').remove();
                $('.woocommerce-form-coupon-toggle').after('<div class="woocommerce-notice-wrapper"><ul class="woocommerce-error woo-notice-box" role="alert"><li>"'+coupon+'"のクーポンはありません。</li></ul></div>');
                return false;
            }
        });
    }
});
function formatNumber(nStr, decSeperate, groupSeperate) {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
}
function convert_htmlprice_to_float(current_prive) {
    if (typeof current_prive !== 'undefined' && typeof current_prive !== undefined && current_prive !== '') {
        current_prive = current_prive.replace(',', '');
        current_prive = current_prive.replace('¥', '');
        current_prive = parseFloat(current_prive);
    }
    return current_prive;
}