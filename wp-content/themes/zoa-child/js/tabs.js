jQuery(document).ready(function ($) {
    $('.tab_content').hide();
    $('.tab_content:first').show();
    $('.tabs li:first').addClass('active');
    $('.tabs li').click(function (event) {
        $("html, body").animate({scrollTop: $('.tabs').offset().top - 52}, 1000);
        $('.tabs li').removeClass('active');
        $(this).addClass('active');
        $('.tab_content').hide();

        var selectTab = $(this).find('a').attr("href");

        $(selectTab).fadeIn();
    });
});