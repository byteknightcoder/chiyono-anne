jQuery(function ($) {
    if ($('#account_birth_year').length)
    {
        $('#last_name_kana').closest('tr').hide();
        $('#first_name_kana').closest('tr').hide();
        $('#birth_year').closest('tr').hide();
        $('#birth_month').closest('tr').hide();
        $('#birth_date').closest('tr').hide();

        $('#last_name_kana').val('...');
        $('#first_name_kana').val('...');

        $('#birth_year').html($('#account_birth_year').html());
        $('#birth_month').html($('#account_birth_month').html());
        $('#birth_date').html($('#account_birth_day').html());

        $('#birth_year').append('<option selected="selected" value="1"></option>');
        $('#birth_month').append('<option selected="selected" value="1"></option>');
        $('#birth_date').append('<option selected="selected" value="1"></option>');
    }

    if ($('#product_attribute_color').length)
    {
        var params = {
            change: function (e, ui) {
                $('input[name="woof_term_color"]').val(ui.color.toString());
                $('input[name="woof_term_color"]').closest('.wp-picker-container').find('.wp-color-result').css('background-color', ui.color.toString())

            }
        }

        $('#product_attribute_color').wpColorPicker(params);
    }

    $('a.load_customer_billing').on('click', function () {
        var user_id = $('#customer_user').val();

        if (!user_id) {
            window.alert(woocommerce_admin_meta_boxes.no_customer_selected);
            return false;
        }

        var data = {
            user_id: user_id,
            type_to_load: 'billing',
            action: 'load_customer_kana_info',
            security: woocommerce_admin_meta_boxes.get_customer_details_nonce
        };

        $(this).closest('.edit_address').block({
            message: null,
            overlayCSS: {
                background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
                opacity: 0.6
            }
        });

        $.ajax({
            url: woocommerce_admin_meta_boxes.ajax_url,
            data: data,
            type: 'POST',
            success: function (response) {
                var info = JSON.parse(response);
                if (info) {
                    $('#_billing_first_name_kana').val(info.billing_first_name_kana);
                    $('#_billing_last_name_kana').val(info.billing_last_name_kana);
                }
                $('.edit_address').unblock();
            }
        });
        return false;
    });
    $('a.load_customer_shipping').on('click', function () {
        var user_id = $('#customer_user').val();

        if (!user_id) {
            window.alert(woocommerce_admin_meta_boxes.no_customer_selected);
            return false;
        }

        var data = {
            user_id: user_id,
            type_to_load: 'billing',
            action: 'load_customer_kana_info',
            security: woocommerce_admin_meta_boxes.get_customer_details_nonce
        };

        $(this).closest('.edit_address').block({
            message: null,
            overlayCSS: {
                background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
                opacity: 0.6
            }
        });

        $.ajax({
            url: woocommerce_admin_meta_boxes.ajax_url,
            data: data,
            type: 'POST',
            success: function (response) {
                var info = JSON.parse(response);
                if (info) {
                    $('#_shipping_first_name_kana').val(info.shipping_first_name_kana);
                    $('#_shipping_last_name_kana').val(info.shipping_last_name_kana);
                }
                $('.edit_address').unblock();
            }
        });
        return false;
    });
})