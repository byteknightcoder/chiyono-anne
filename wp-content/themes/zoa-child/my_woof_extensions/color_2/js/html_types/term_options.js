jQuery(function ($) {
    try {
        $('.woof-color-picker').wpColorPicker();
    } catch (e) {
        console.log(e);
    }
    //***
    $('.woof_term_image_button_upload').click(function ()
    {
        var input_object = jQuery(this).prev('input[type=text]');
        window.send_to_editor = function (html)
        {
            jQuery('#woof_buffer').html(html);
            var imgurl = jQuery('#woof_buffer').find('a').eq(0).attr('href');
            jQuery('#woof_buffer').html("");
            jQuery(input_object).val(imgurl);
            jQuery(input_object).trigger('change');
            tb_remove();
        };
        tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');

        return false;
    });
});

