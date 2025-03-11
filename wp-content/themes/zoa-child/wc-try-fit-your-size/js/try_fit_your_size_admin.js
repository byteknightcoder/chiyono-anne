jQuery(document).ready(function ($) {
    if ($('#zoom_metting_url').length) {
        $('.save_order').on('click', function (e) {//only allow save if zoom id have value
            if ($('#zoom_metting_url').val() == '') {
                alert('Please enter zoom meeting url value.');
                return false;
            }
        });
    }
});