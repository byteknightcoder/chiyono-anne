jQuery(document).ready(function ($) {
    $('#my_custom_product_data .hide_if_grouped a').click(function () {
        tbframe_interval = setInterval(function () {
            $('#TB_iframeContent').contents().find('#wpwrap').addClass('iframe-body');
        }, 1000);
        tb_show('', 'edit.php?post_type=product&page=yith_wapo_groups&KeepThis=true&TB_iframe=true&modal=false&width=772&height=566');
        return false;
    });
});