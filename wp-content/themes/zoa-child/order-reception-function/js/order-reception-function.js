jQuery(document).ready(function ($) {
    // dont use this. current move to use php hook
    // if ($('#get_a_coupon_arf').length) {
    //     $(document).on('click', '#get_a_coupon_arf', function (event) {
    //         // $('body').LoadingOverlay('show');
    //         $.ajax({
    //             type: "post",
    //             url: ch_ajaxurl,
    //             data: {action: 'orf_get_a_coupon_code'},
    //             dataType: "json"
    //         }).done(function (response) {
    //             if (response.success == 'ok') {
    //                 //$('#show_coupon_code').html(response.code);
    //                 $('form.checkout_coupon #coupon_code').val(response.code);
    //                 $('form.checkout_coupon button').click();
    //             } else {
    //                 // $('#show_coupon_code').html(response.msg);
    //             }
    //             // $('body').LoadingOverlay('hide');
    //         });
    //     });
    // }

    if ($('.event_products').length) {
        $('.ch_link_event').each(function () {
            let product_id = $(this).attr('id'),
                link = $(this).attr('ch_link_event');
            $(`li.post-${product_id}`).find('a.woocommerce-loop-product__link').attr('href', link);
            $(`li.post-${product_id}`).find('a.add_to_cart_button').attr('href', link);
            $(`li.post-${product_id}`).find('a.hover_link').attr('href', link);
            $(`li.post-${product_id}`).find('a.c-product-name_link').attr('href', link);
        });
    }
});