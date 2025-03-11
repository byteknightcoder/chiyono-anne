jQuery(document).ready(function ($) {
    if ($('.fit_data_options').length) {
        $('.ch_next').addClass('ch_next_disable');
        $('.plung_bra_area').hide();
        $('.soft_bra_area').hide();
        $('.full_cup_bra_area').hide();
        if ($('.image-checkbox').length) {
            $(".image-checkbox").each(function () {
                if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
                    $(this).addClass('image-checkbox-checked');
                } else {
                    $(this).removeClass('image-checkbox-checked');
                }
            });
            // sync the state to the input
            $(".image-checkbox").on("click", function (e) {
                $(this).toggleClass('image-checkbox-checked');
                var $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop("checked", !$checkbox.prop("checked"));
                if ($('input[name="bra_product_id[]"]:checked').length > 2) {
                    $checkbox.prop('checked', false);
                    $(this).removeClass('image-checkbox-checked');
                    alert($('#alert_ja_max6').val());
                } else {
                    var val = [];
                    $('.bra_product_id').each(function (i) {
                        if ($(this).is(':checked')) {
                            val[i] = $(this).val();
                        }
                    });
                    if (val.length > 0) {
                        $(this).closest('.bra_you_prefer').removeClass('required_filled');
                        $(this).closest('.bra_you_prefer').addClass('required_filled');
                        $(this).closest('.bra_you_prefer').find('.ch_next').removeClass('ch_next_disable');
                    } else {
                        $(this).closest('.bra_you_prefer').find('.ch_next').addClass('ch_next_disable');
                        $(this).closest('.bra_you_prefer').removeClass('required_filled');
                    }
                    $('body').LoadingOverlay('show');
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        url: $('#try_fit_type').attr('ajaxurl'),
                        data: {action: 'ch_get_category_name', product_id: val},
                    }).success(function (response) {
                        if (response.value !== '') {
                            $('#try_fit_type').val(response.value);
                        }
                        $('.your_fit_type').remove();
                        if (response.text !== '') {
                            $('.bra_you_prefer .grids__card').after('<p class="your_fit_type your_fit_type__result selected_output"><span class="underline">' + response.text + '</span></p>');
                        }
                        if (response.cat_detect.includes("plunge-bra")) {
                            $('.plung_bra_area').show();
                            $('.plung_bra_area').addClass('require');
                        } else {
                            $('.plung_bra_area').hide();
                            $('#alert_plunge').hide();
                            $('.plung_bra_area').removeClass('require');
                            $('.plung_bra_area input').prop('checked', false);
                        }
                        if (response.cat_detect.includes("soft-bra")) {
                            $('.soft_bra_area').show();
                            $('.soft_bra_area').addClass('require');
                        } else {
                            $('.soft_bra_area').hide();
                            $('#alert_soft').hide();
                            $('.soft_bra_area').removeClass('require');
                            $('.soft_bra_area input').prop('checked', false);
                        }
                        if (response.cat_detect.includes("full-cup-bra")) {
                            $('.full_cup_bra_area').show();
                            $('.full_cup_bra_area').addClass('require');
                        } else {
                            $('.full_cup_bra_area').hide();
                            $('#alert_full_cup').hide();
                            $('.full_cup_bra_area').removeClass('require');
                            $('.full_cup_bra_area input').prop('checked', false);
                        }
                        $('body').LoadingOverlay('hide');
                    });
                }
            });
        }

//limit choose size plunge
        $('#seleted_size').hide();
        $('#alert_plunge').hide();
        $('.bra_size').on('change', function (e) {
            var $current = $(this);
            if ($(this).closest('.fit_bra_size').hasClass('outstock')) {
                $(this).closest('.fit_bra_size').addClass('outstock_alert');
                $(this).prop('checked', false);
                $('#alert_plunge').html('<span class="boxed_alert">申し訳ございません。"プランジブラ"のサイズ ' + $(this).val() + 'のフィットサンプルが全て出回っておりますため、在庫がございません。明後日にご確認くださいませ。<br>"プランジブラ"をフィットサンプルブラタイプから外しますか？<br><a class="remove_plunge underline remove_bratype_link" href="javascript:;"><strong class="link">プランジブラを外す</strong></a></span>');
                $('#alert_plunge').show();
                return;
            } else {
                $(this).closest('.swatch__list').find('.fit_bra_size').removeClass('outstock_alert');
                $('#alert_plunge').hide();
            }
            var selected_size_value = [];
            $('body').LoadingOverlay('show');
            if ($('input[name="bra_size[]"]:checked').length > 2) {
                $(this).prop('checked', false);
                alert($('#alert_ja_max2').val());
            }
            if ($('input[name="bra_size[]"]:checked').length > 0) {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').addClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').removeClass('ch_next_disable');
            } else {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').addClass('ch_next_disable');
            }
            if ($('input[name="bra_size[]"]:checked')) {
                $('input[name="bra_size[]"]:checked').parent().addClass('checked');
            }
            if ($('input[name="bra_size[]"]:not(:checked)')) {
                $('input[name="bra_size[]"]:not(:checked)').parent().removeClass('checked');
            }
            //valide select size next and prev
            if ($('input[name="bra_size[]"]:checked').length > 0) {
                $('.bra_size').each(function (i) {
                    $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                    if ($(this).is(':checked')) {
                        selected_size_value.push($(this).val());
                        if ($('input[name="bra_size[]"]:checked').length < 2) {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').next().css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').prev().css({"color": "#0a0a0a", "pointer-events": "auto"});
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                        }
                    } else {
                        if ($(this).closest('.fit_bra_size').prev().find('input').is(':checked')) {
                            if ($('input[name="bra_size[]"]:checked').length < 2) {
                                $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            }
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                        }
                    }
                });
                $('#seleted_size').show();
                $('#seleted_size span.seleted_size_value').html(selected_size_value.toString());
            } else {
                $('#seleted_size').hide();
                $('.plung_bra_area .fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
            }
            //end
            $('body').LoadingOverlay('hide');
        });
        //limit choose size soft
        $('#seleted_size_soft').hide();
        $('#alert_soft').hide();
        $('.bra_size_soft').on('change', function (e) {
            var $current = $(this);
            if ($(this).closest('.fit_bra_size').hasClass('outstock')) {
                $(this).closest('.fit_bra_size').addClass('outstock_alert');
                $(this).prop('checked', false);
                $('#alert_soft').html('<span class="boxed_alert">申し訳ございません。"ソフトブラ"のサイズ' + $(this).val() + 'のフィットサンプルが全て出回っておりますため、在庫がございません。明後日にご確認くださいませ。 <br>"ソフトブラ"をフィットサンプルブラタイプから外しますか？<br><a class="remove_soft underline remove_bratype_link" href="javascript:;"><strong class="link">ソフトブラを外す</strong></a></span>');
                $('#alert_soft').show();
                return;
            } else {
                $(this).closest('.swatch__list').find('.fit_bra_size').removeClass('outstock_alert');
                $('#alert_soft').hide();
            }
            var selected_size_value = [];
            $('body').LoadingOverlay('show');
            if ($('input[name="bra_size_soft[]"]:checked').length > 2) {
                $(this).prop('checked', false);
                alert($('#alert_ja_max2').val());
            }
            if ($('input[name="bra_size_soft[]"]:checked').length > 0) {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').addClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').removeClass('ch_next_disable');
            } else {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').addClass('ch_next_disable');
            }
            if ($('input[name="bra_size_soft[]"]:checked')) {
                $('input[name="bra_size_soft[]"]:checked').parent().addClass('checked');
            }
            if ($('input[name="bra_size_soft[]"]:not(:checked)')) {
                $('input[name="bra_size_soft[]"]:not(:checked)').parent().removeClass('checked');
            }
            //valide select size next and prev
            if ($('input[name="bra_size_soft[]"]:checked').length > 0) {
                $('.bra_size_soft').each(function (i) {
                    $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                    if ($(this).is(':checked')) {
                        selected_size_value.push($(this).val());
                        if ($('input[name="bra_size_soft[]"]:checked').length < 2) {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').next().css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').prev().css({"color": "#0a0a0a", "pointer-events": "auto"});
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                        }
                    } else {
                        if ($(this).closest('.fit_bra_size').prev().find('input').is(':checked')) {
                            if ($('input[name="bra_size_soft[]"]:checked').length < 2) {
                                $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            }
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                        }
                    }
                });
                $('#seleted_size_soft').show();
                $('#seleted_size_soft span.seleted_size_value').html(selected_size_value.toString());
            } else {
                $('#seleted_size_soft').hide();
                $('.soft_bra_area .fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
            }
            //end
            $('body').LoadingOverlay('hide');
        });
        //limit choose size full cup
        $('#seleted_size_full_cup').hide();
        $('#alert_full_cup').hide();
        $('.bra_size_full_cup').on('change', function (e) {
            var $current = $(this);
            if ($(this).closest('.fit_bra_size').hasClass('outstock')) {
                $(this).closest('.fit_bra_size').addClass('outstock_alert');
                $(this).prop('checked', false);
                $('#alert_full_cup').html('<span class="boxed_alert">申し訳ございません。"ソフトブラ"のサイズ' + $(this).val() + 'のフィットサンプルが全て出回っておりますため、在庫がございません。明後日にご確認くださいませ。 <br>"ソフトブラ"をフィットサンプルブラタイプから外しますか？<br><a class="remove_soft underline remove_bratype_link" href="javascript:;"><strong class="link">ソフトブラを外す</strong></a></span>');
                $('#alert_full_cup').show();
                return;
            } else {
                $(this).closest('.swatch__list').find('.fit_bra_size').removeClass('outstock_alert');
                $('#alert_full_cup').hide();
            }
            var selected_size_value = [];
            $('body').LoadingOverlay('show');
            if ($('input[name="bra_size_full_cup[]"]:checked').length > 2) {
                $(this).prop('checked', false);
                alert($('#alert_ja_max2').val());
            }
            if ($('input[name="bra_size_full_cup[]"]:checked').length > 0) {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').addClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').removeClass('ch_next_disable');
            } else {
                $(this).closest('.choose_bra_size').removeClass('required_filled');
                $(this).closest('.choose_bra_size').find('.ch_next').addClass('ch_next_disable');
            }
            if ($('input[name="bra_size_full_cup[]"]:checked')) {
                $('input[name="bra_size_full_cup[]"]:checked').parent().addClass('checked');
            }
            if ($('input[name="bra_size_full_cup[]"]:not(:checked)')) {
                $('input[name="bra_size_full_cup[]"]:not(:checked)').parent().removeClass('checked');
            }
            //valide select size next and prev
            if ($('input[name="bra_size_full_cup[]"]:checked').length > 0) {
                $('.bra_size_full_cup').each(function (i) {
                    $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                    if ($(this).is(':checked')) {
                        selected_size_value.push($(this).val());
                        if ($('input[name="bra_size_full_cup[]"]:checked').length < 2) {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').next().css({"color": "#0a0a0a", "pointer-events": "auto"});
                            $(this).closest('.fit_bra_size').prev().css({"color": "#0a0a0a", "pointer-events": "auto"});
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                        }
                    } else {
                        if ($(this).closest('.fit_bra_size').prev().find('input').is(':checked')) {
                            if ($('input[name="bra_size_full_cup[]"]:checked').length < 2) {
                                $(this).closest('.fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
                            }
                        } else {
                            $(this).closest('.fit_bra_size').css({"color": "#ddd", "pointer-events": "none"});
                        }
                    }
                });
                $('#seleted_size_full_cup').show();
                $('#seleted_size_full_cup span.seleted_size_value').html(selected_size_value.toString());
            } else {
                $('#seleted_size_full_cup').hide();
                $('.full_cup_bra_area .fit_bra_size').css({"color": "#0a0a0a", "pointer-events": "auto"});
            }
            //end
            $('body').LoadingOverlay('hide');
        });
        //for online_consultation
        if ($('.choose_online_onsultation').length) {
            $('.custom_option__total').hide();
            $(".choose_online_onsultation .checkbox__simple").on("click", function (e) {
                var $checkbox = $(this).find('input[type="checkbox"]');
                $checkbox.prop("checked", !$checkbox.prop("checked"));
                $('body').LoadingOverlay('show');
                if ($('input[name="online_consultation"]').is(':checked')) {
                    $(this).closest('.choose_online_onsultation').removeClass('required_filled');
                    $(this).closest('.choose_online_onsultation').addClass('required_filled');
                    $('.custom_option__total').show();
                } else {
                    $('.custom_option__total').hide();
                    $(this).closest('.choose_online_onsultation').removeClass('required_filled');
                }
                $('body').LoadingOverlay('hide');
            });
        }
//validate disable add to cart
        function ch_valid() {
            var pass_validate = true;
            var val = [];
            $('.bra_product_id').each(function (i) {
                if ($(this).is(':checked')) {
                    val[i] = $(this).val();
                }
            });
            if(val.length == 0){
                pass_validate=false;
            }
            //plunge size
            var valszie = [];
            $('.bra_size').each(function (i) {
                if ($(this).is(':checked')) {
                    valszie[i] = $(this).val();
                }
            });
            if (valszie.length == 0 && $('.plung_bra_area').hasClass('require')) {
                pass_validate = false;
            }
            //end
            //soft size
            var valszie_soft = [];
            $('.bra_size_soft').each(function (i) {
                if ($(this).is(':checked')) {
                    valszie_soft[i] = $(this).val();
                }
            });
            if (valszie_soft.length == 0 && $('.soft_bra_area').hasClass('require')) {
                pass_validate = false;
            }
            //end
            if (!$('.choose_online_onsultation .fit_title').hasClass('ch_active_body')) {
                pass_validate = false;
            }
            if ($('.read_term_of_use_try_fit').length) {
                if (!$('#term_of_use_try_fit').is(':checked')) {
                    pass_validate = false;
                }
            }
            if (pass_validate === true) {
                $('.single_add_to_cart_button').removeClass('disabled');
            } else {
                $('.single_add_to_cart_button').removeClass('disabled');
                $('.single_add_to_cart_button').addClass('disabled');
            }
        }
        if ($('.fit_data_options').length) {
            $('.single_add_to_cart_button').addClass('disabled');
            $('.fit_data_options').find('input').each(function (i, field) {
                if ($(this).hasClass('required')) {
                    $(this).on('change', function (e) {
                        ch_valid();
                    });
                    $(this).closest('.image-checkbox').on('click', function (e) {
                        ch_valid();
                    });
                }
            });
        }
//show/content for each options
        if ($('.fit_body').length) {
            $('.fit_body').slice(1).hide();
        }
        $('body').on('click', '.ch_next', function () {
            $('.fit_body').hide();
            if ($(this).closest('.bra_you_prefer').length) {
                $('.choose_bra_size .fit_body').show(1000);
                $("html, body").animate({scrollTop: $('.choose_bra_size .fit_title').offset().top}, 1000);
            } else {
                $('.choose_online_onsultation .fit_body').show(1000);
                $('.fit_title').removeClass('ch_active_body');
                $('.choose_online_onsultation .fit_title').addClass('ch_active_body');
                $("html, body").animate({scrollTop: $('.choose_online_onsultation .fit_title').offset().top}, 1000);
                ch_valid();
            }
        });
        $('body').on('click', '.fit_title', function () {
            $('.fit_body').hide();
            $('.fit_title').removeClass('ch_active_body');
            $(this).next('.fit_body').show(1000);
            $(this).addClass('ch_active_body');
            $("html, body").animate({scrollTop: $(this).offset().top}, 1000);
            ch_valid();
        });
        //end
        //hide qty textbox
        $('div.quantity').hide();
        //remove "Plunge Bra" sample seleted
        $('body').on('click', '.remove_plunge', function () {
            // sync the state to the input
            $('.plunge-bra .image-checkbox-checked').each(function (i) {
                var $this = $(this);
                i=i+1;
                console.log('i='+i*500);
                var t = setTimeout(function () {
                    $this.click();
                }, i*500);
            });
            $('.plung_bra_area .checked input').each(function (i) {
                $(this).click();
            });
        });
        //remove "Soft Bra" sample seleted
        $('body').on('click', '.remove_soft', function () {
            // sync the state to the input
            $('.soft-bra .image-checkbox-checked').each(function (i) {
                var $this = $(this);
                i=i+1;
                console.log('i='+i*500);
                var t = setTimeout(function () {
                    $this.click();
                }, i*500);
            });
            $('.soft_bra_area .checked input').each(function (i) {
                $(this).click();
            });
        });
    }
    //modal of term of use try fit your size
    if ($('.read_term_of_use_try_fit').length) {
        $('body').on('click', '.read_term_of_use_try_fit', function (e) {
            if (!$('#term_of_use_try_fit').is(':checked')) {
                var inst = $('[data-remodal-id=read_term_of_use_try_fit_modal]').remodal();
                inst.open();
            } else {
                $('.single_add_to_cart_button').removeClass('disabled');
                $('.single_add_to_cart_button').addClass('disabled');
            }
        });
    }
    $('body').on('opening', '.remodal', function () {
        $('#term_of_use_try_fit').prop('checked', false);
    });
    $('body').on('opened', '.remodal', function () {
        $('#term_of_use_try_fit').prop('checked', false);
    });
    if ($('.ch_agree_term_try_fit').length) {
        $('body').on('click', '#ch_agree_term_try_fit', function () {
            $('#term_of_use_try_fit').prop('checked', true);
            ch_valid();
        });
    }
//end
//i returned
    $(document).on('click', '.i_returned_order_btn', function (event) {
        var txt_confirm = $(this).attr('data-confirm');
        if (confirm(txt_confirm))
        {
            jQuery('body').LoadingOverlay('show');
            event.preventDefault();
            var order_id = $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: gl_ajax_url,
                data: {order_id: order_id, action: 'try_fit_customer_notify_to_admin'},
                dataType: "json"
            }).done(function (response) {
                if (response.success)
                {
                    alert(response.msg);
                }
                jQuery('body').LoadingOverlay('hide');
            });
        }
    });
    //only for slick for guide-tbyb page
    if ($('.slick-active').length) {
        $('body').on('click', '.elementor-tab-title', function () {
            $('.grids_style02').slick('refresh');
        });
        if (window.location.hash) {
            var tagr = window.location.hash;
            setTimeout(function () {
                $(tagr).trigger('click');
                $("html, body").animate({scrollTop: $('.elementor-element-7e3fcf3').offset().top - 20}, 800);
            }, 1000);
        }
    }
});