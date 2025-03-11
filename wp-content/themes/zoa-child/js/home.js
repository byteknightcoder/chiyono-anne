
jQuery(document).ready(function ($) {
    var $win = $(window);
    var $bp_lm = 992;
    var $bp_md = 768;
    var $bp_sm = 575;
    var seriesImg = $('.series-items-images');
    var seriesNav = $('.series-items');
    function SeriesSlick() {
        $(seriesImg).slick({
            dots: true,
            centerMode: true,
            centerPadding: '0px',
            infinite: true,
            arrows: false,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            //autoplay: true,
            responsive: [{
                breakpoint: $bp_lm,
                settings: {
                    asNavFor: seriesNav,
                    slidesToShow: 2
                }
            }]
        });
        $('body').on('click', '.series-link .sl-link', function () {
            if($(this).hasClass('active')){
                return true;
            }
            $('.series-link .sl-link').removeClass('active');
            $(this).addClass('active');
            var index = $(this).attr('index');
            $('.series-items-images').slick('slickGoTo', index);
            var wd=$('.series-items-images .slick-current').css('width');
            wd = wd.replace("px", "");
            wd=wd*1.2;
            setTimeout(function () {
                $('.series-items-images .slick-current').css('width',wd);
            }, 10);
        });
        $(seriesImg).on('afterChange', function () {
            var dataId = $('.series-items-images .slick-current').attr("data-slick-index");
            var wd=$('.series-items-images .slick-current').css('width');
            wd = wd.replace("px", "");
            wd=wd*1.2;
            setTimeout(function () {
                $('.series-items-images .slick-current').css('width',wd);
            }, 10);
            $('.series-link .sl-link').removeClass('active');
            $('.sl-item a[index="' + dataId + '"]').addClass('active');
        });
    }
    function SeriesSlickNav() {
        $(seriesNav).slick({
            draggable: true,
            accessibility: false,
            variableWidth: true,
            arrows: false,
            slidesToShow: 1,
            asNavFor: seriesImg,
            focusOnSelect: true,
            swipeToSlide: true,
            infinite: true
        });
    }
    function latest_catalog() {
        if ($('.latest-catalog').length) {
            var slick_detect = $('.latest-catalog ul.products');
            if ($win.width() > $bp_lm) {
                if (!slick_detect.hasClass('slick-initialized')) {
                    slick_detect.slick({
                        dots: true,
                        infinite: true,
                        arrows: false,
                        speed: 300,
                        slidesToShow: 4,
                        slidesToScroll: 4
                    });
                }
            } else {
                if (slick_detect.hasClass('slick-initialized')) {
                    slick_detect.slick('unslick');
                }
            }
        }
    }
    latest_catalog();
    function series_init_slick() {
        if ($('.series-items-images').length) {
            setTimeout(function () {
                $('.series-items-images .slick-slide').each(function () {
                    var wd = $(this).css('width');
                    $(this).css('transform', 'translateX(-' + wd + ')');
                });
            }, 10);
            setTimeout(function () {
                var wd = $('.series-items-images .slick-current').css('width');
                wd = wd.replace("px", "");
                wd = wd * 1.2;

                $('.series-items-images .slick-current').css('width', wd);
                $('.series-items-images .slick-slide').last().hide();
            }, 10);
        }
    }
    series_init_slick();
    $win.on('load resize', function () {
        latest_catalog();
        //for .series-items-images slick
        series_init_slick();
        //for category slick
        $catSlick = '.cl-items';
        if($($catSlick).length){
            var cl_slick=$('.cl-items');
            if ($win.width() > $bp_lm) {
                if (cl_slick.hasClass('slick-initialized')) {
                    cl_slick.slick('unslick');
                }
            } else {
                if (!cl_slick.hasClass('slick-initialized')) {
                        cl_slick.slick({
                        dots: false,
                        infinite: true,
                        arrows: false,
                        speed: 300,
                        centerPadding: $($catSlick).find('.slick-slide').outerWidth() / 3,
                        slidesToShow: 2,
                        slidesToScroll: 1,
                        //centerMode: true
                    });
                }
            }
        }
        //for blog post home page
        if($('.home .post-item__latest').length){
            var pl_slick=$('.home .post-item__latest');
            if ($win.width() > $bp_lm) {
                if (pl_slick.hasClass('slick-initialized')) {
                    pl_slick.slick('unslick');
                }
            } else {
                if (!pl_slick.hasClass('slick-initialized')) {
                        pl_slick.slick({
                        dots: false,
                        infinite: false,
                        arrows: false,
                        speed: 300,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        centerMode: true,
                        centerPadding: '0 20% 0 0',
                    });
                }
            }
        }
    });
    if ($('.series-link').length) {
        SeriesSlick();
        if ($win.width() < $bp_lm) {
            SeriesSlickNav();
        }
        
    }
    if ($('.gl-image').length > 1) {
        $('.content-cahome .home-left').slick({
            dots: true,
            infinite: true,
            arrows: false,
            speed: 300,
            slidesToShow: 1,
            slidesToScroll: 1
        });
    }
    //show +xx colors
    if ($('.iconic-was-swatches--loop').length) {
        $('.iconic-was-swatches--loop').each(function () {
            var total = $(this).find('.iconic-was-swatches__item').length;
            var total_out_of=$(this).find('.iconic-was-swatches__item.iconic-was-swatches__item--out-of-stock').length;
            total=total-total_out_of;
            if (total > 3) {
                $(this).find('.iconic-was-swatches__item:not(.iconic-was-swatches__item--out-of-stock):gt(2)').hide();
                $(this).append('<li class="more-colors">+ '+(total-3)+' colors</li>');
            }
        });
    }
});