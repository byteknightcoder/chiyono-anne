<?php

/**
 * Admin CSS
 */
add_action('admin_enqueue_scripts', 'load_admin_style');
function load_admin_style() {
    wp_enqueue_style('admin_css', get_stylesheet_directory_uri() . '/css/admin-page.css', false, '1.0.0');
}

/**
 * Enqueue parent theme styles first
 * Replaces previous method using @import
 * <http://codex.wordpress.org/Child_Themes>
 */
// remove_filter ('wp_mail', 'wpautop');

// add_filter('style_loader_src', 'elsey_change_cssjs_ver', 1000);
// add_filter('script_loader_src', 'elsey_change_cssjs_ver', 1000);
function elsey_change_cssjs_ver($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
        $src = add_query_arg(array('ver' => '1.0003'), $src);
    }
    return $src;
}

/**
 **  load custom css for woocommerce-customers-manager
 **/
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');
function load_custom_wp_admin_style() {
    // $hook is string value given add_menu_page function.
    if (!class_exists('WCCM_CustomerDetails')) {
        return;
    }
    wp_register_script('admin_custom_js', get_stylesheet_directory_uri() . '/admin/js/admin-custom.js', array(), time(), true);
    if (isset($_REQUEST['page']) && in_array($_REQUEST['page'], array('woocommerce-customers-manager', 'wccm-add-new-customer', 'wccm-discover-customer', 'wccm-bulk-email-customer', 'wccm-import-customers', 'wccm-export-customers', 'wccm-options-page', 'acf-options-email-templates-configurator'))) {
        wp_register_style('wcm_plugin_page_css', get_stylesheet_directory_uri() . '/admin/css/wcm-custom.css');
        wp_enqueue_style('wcm_plugin_page_css');
    }
    wp_enqueue_script('admin_custom_js');
}

add_action('wp_enqueue_scripts', 'zoa_enqueue_parent_theme_style', 99);
function zoa_enqueue_parent_theme_style() {
    wp_enqueue_style('zoa-theme-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('zoa-child-style', get_stylesheet_directory_uri() . '/style.css', array('zoa-theme-style'), time());
}

// Ensure is_plugin_active function is available
if (function_exists('is_plugin_active')) {
    // Check if both plugins are active
    if (is_plugin_active('woo-variation-swatches-pro/woo-variation-swatches-pro.php') && is_plugin_active('woo-variation-gallery-pro/woo-variation-gallery-pro.php')) {
        add_action('wp_enqueue_scripts', 'load_theme_scripts', 100); // Modify add-to-cart JS for WooCommerce variations
    }
} else {
    // Optional: Handle case where is_plugin_active() is not available
    // This can log a message or handle it gracefully
    error_log('is_plugin_active function is not available.');
}

function load_theme_scripts() {
    // add-to-cart-variation override
    if (woo_variation_swatches()->get_option('enable_single_variation_preview') || woo_variation_swatches()->get_option('disable_threshold')) {
        wp_deregister_script('wc-add-to-cart-variation');
        wp_register_script('wc-add-to-cart-variation', get_stylesheet_directory_uri() . '/woocommerce/js/wvs-add-to-cart-variation.js', array(), time());
        wp_deregister_script('yith_wapo_frontend');
        wp_register_script('yith_wapo_frontend', get_stylesheet_directory_uri() . '/woocommerce/js/yith-wapo-frontend.js', array(), time());
    }
}

add_action('wp_enqueue_scripts', 'custom_styles');
function custom_styles() {
    wp_register_style('labelauty-style', get_stylesheet_directory_uri() . '/js/labelauty/jquery-labelauty.min.css', array(), ''); //compiled
    wp_register_style('validation_engine_css', get_stylesheet_directory_uri() . '/js/validationEngine.jquery.css', array(), '');
    // wp_register_style('blog-style', get_stylesheet_directory_uri() . '/blog.css', array(), '');
    // wp_register_style('form-style', get_stylesheet_directory_uri() . '/css/form.css', array(), '');
    // wp_register_style('portani-style', get_stylesheet_directory_uri() . '/css/port-animation.css', array(), '');
    // wp_register_style('portfolio-style', get_stylesheet_directory_uri() . '/css/portfolio.css', array(), '');
    // wp_register_style('nano-scroll-style', get_stylesheet_directory_uri() . '/css/nanoscroller.css', array(), '');
    // wp_register_style('yithoption-style', get_stylesheet_directory_uri() . '/css/yithoption.css?v=' . microtime(), array(), '');
    // wp_register_script('icoca', get_stylesheet_directory_uri() . '/icons/icoca/svgxuse.js', array(), false, true);
    // wp_register_style('tabs-style', get_stylesheet_directory_uri() . '/css/tabs.css', array(), '');

    wp_enqueue_style('cal-style', get_stylesheet_directory_uri() . '/js/calendar/pignose.calendar.css', array(), '');
    wp_enqueue_style('gfont-style', 'https://fonts.googleapis.com/css?family=Noto+Sans+JP:300,400,500,700', array(), '');
    if (is_page('guide-tbyb')) {
        wp_enqueue_style('cmgaramond-style', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&display=swap', array(), '');
    }

    //*****load styles for js plugin
    wp_enqueue_style('labelauty-style', array('remodal-style'));
    wp_enqueue_style('select-style', get_stylesheet_directory_uri() . '/js/selectbox/selectbox.min.css', array('zoa-theme-style'), '');
    wp_enqueue_style('bootstrap-grid', get_stylesheet_directory_uri() . '/css/bootstrap-grid.min.css', array('zoa-theme-style'), '');

    //*****changed load from cdn
    wp_enqueue_style('slick-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.css', array('zoa-theme-style'), '');
    wp_enqueue_style('slicktheme-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.min.css', array('slick-style'), '');
    wp_enqueue_style('remoda_theme', '//cdnjs.cloudflare.com/ajax/libs/remodal/1.1.1/remodal-default-theme.min.css', array('bootstrap-grid'), '');
    wp_enqueue_style('remodal-style', '//cdnjs.cloudflare.com/ajax/libs/remodal/1.1.1/remodal.min.css', array('remoda_theme'), '');

    //***** New Updated Styles
    wp_enqueue_style('updated-style', get_stylesheet_directory_uri() . '/update.css', array('zoa-child-style'), time());

    if (is_page('register')) {
        wp_enqueue_style('validation_engine_css', array('zoa-theme-style'));
    }
}

add_action('wp_enqueue_scripts', 'add_scripts');
function add_scripts() {
    wp_register_script('moment-js', get_stylesheet_directory_uri() . '/js/calendar/moment.min.js', array(), false, true);
    //wp_register_script( 'calmain-js', get_stylesheet_directory_uri() . '/js/calendar/main.js');
    // wp_register_script('formstep-js', get_stylesheet_directory_uri() . '/js/form-steps.js', array(), false, true);
    // $translation_array = array(
    //     'step3_string' => __('Confirmation', 'zoa')
    // );
    // wp_localize_script('formstep-js', 'object_name', $translation_array);
    wp_register_script('less-js', '//cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js', array(), false, true);
    wp_register_script('checkout-js', get_stylesheet_directory_uri() . '/js/checkout.js?v=' . microtime(), array(), false, true);
    wp_register_script('labelauty-js', get_stylesheet_directory_uri() . '/js/labelauty/jquery-labelauty.js', array(), false, true);
    wp_register_script('reservation-js', get_stylesheet_directory_uri() . '/js/reservation.js', array(), false, true);
    wp_register_script('gp-js', get_stylesheet_directory_uri() . '/js/grid-parallax.js', array(), false, true);
    wp_register_script('rellax-js', get_stylesheet_directory_uri() . '/js/rellax.min.js', array(), false, true);
    wp_register_script('masonry-js', get_stylesheet_directory_uri() . '/js/masonry.pkgd.min.js', array(), false, true);
    wp_register_script('portfolio-js', get_stylesheet_directory_uri() . '/js/portfolio.js', array(), false, true);
    wp_register_script('newhome-js', get_stylesheet_directory_uri() . '/js/home.js?v=' . microtime(), array(), false, true);

    $get_url = array('siteurl' => get_option('siteurl'));
    wp_localize_script('home-js', 'get_url', $get_url);
    wp_register_script('remodal', get_stylesheet_directory_uri() . '/js/remodal/remodal.js', array(), false, true);
    wp_register_script('slick-js', get_stylesheet_directory_uri() . '/js/slick/slick.js', array(), false, true);
    wp_register_script('slick-active', get_stylesheet_directory_uri() . '/js/slick-active.js', array(), false, true);
    wp_register_script('shopsingle-js', get_stylesheet_directory_uri() . '/js/shopsingle.js?v=' . microtime(), array(), false, true); //single shop
    wp_register_script('popup-js', get_stylesheet_directory_uri() . '/js/popup.js', array(), false, true); //popup tooltip
    wp_register_script('woof-js', get_stylesheet_directory_uri() . '/js/woof.js', array(), false, true);
    wp_register_script('booked-js', get_stylesheet_directory_uri() . '/js/booked-custom.js?v=' . microtime(), array(), false, true);
    wp_register_script('booked-steps', get_stylesheet_directory_uri() . '/js/booked-formsteps.js?v=' . microtime(), array(), false, true);
    wp_register_script('tabs-js', get_stylesheet_directory_uri() . '/js/tabs.js', array(), false, true); //compile later
    wp_register_script('ajax-con', get_stylesheet_directory_uri() . '/js/ajax-con.js', array(), false, true); //compile later
    wp_register_script('register', get_stylesheet_directory_uri() . '/js/register.js', array(), false, true); //compile later
    wp_register_script('nano-scroll', get_stylesheet_directory_uri() . '/js/jquery.nanoscroller.js', array(), false, true); //compile later
    wp_register_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.js', array(), false, true); //compile later
    wp_register_script('masonry', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js', array(), false, true);
    $translation_array = array(
        'name_label' => __('Name', 'zoa'),
        'kana_label' => __('Kana Name', 'zoa'),
        'dbirth_label' => __('Date of Birth', 'zoa'),
        'year_label' => __('Year', 'zoa'),
        'month_label' => __('Month', 'zoa'),
        'day_label' => __('Day', 'zoa')
    );
    wp_localize_script('register', 'translation', $translation_array);
    wp_register_script('register-js', get_stylesheet_directory_uri() . '/js/registration.js', array(), false, true);
    //blog font charset="utf-8"
    wp_register_script('fontplus-js', '//webfont.fontplus.jp/accessor/script/fontplus.js?nhmkvHEfzoA%3D&box=q0P1gf1eUSQ%3D&aa=1&ab=2' . microtime(), array(), false, true);
    wp_localize_script('register-js', 'translation', $translation_array);
    // wp_enqueue_script('icoca');
    wp_enqueue_script('remodal');
    wp_register_script('imagesloaded.pkgd.min-js', get_stylesheet_directory_uri() . '/js/imagesloaded.pkgd.min.js', array(), false, true);

    if (is_home() || is_front_page()) {
        wp_enqueue_script('gp-js');
        wp_enqueue_script('rellax-js');
        wp_enqueue_script('slick-js');
        wp_enqueue_script('slick-active');
        //new style will be compile
        wp_enqueue_script('newhome-js');
    } else if (is_page('reservation')) {
        // wp_enqueue_style('labelauty-style');
        // wp_enqueue_style('form-style');
        wp_enqueue_script('moment-js');
        wp_enqueue_script('formstep-js');
        wp_enqueue_script('labelauty-js');
        wp_enqueue_script('reservation-js');
    } else if (is_page('reservation-form')) {
        // wp_enqueue_style('labelauty-style');
        // wp_enqueue_style('form-style');
        // wp_enqueue_style('tabs-style');
        // wp_enqueue_style('woof-style');
        wp_enqueue_script('labelauty-js');
        wp_enqueue_script('booked-js');
        wp_enqueue_script('booked-steps');
        wp_enqueue_script('tabs-js');
        wp_enqueue_script('ajax-con');
        wp_enqueue_script('woof-js');
        wp_enqueue_script('popup-js');
    } else if (is_page('about')) {
        // wp_enqueue_style('portani-style');
        // wp_enqueue_style('portfolio-style');
        // wp_enqueue_style('slick-style');
        // wp_enqueue_style('slicktheme-style');
        wp_enqueue_script('imagesloaded.pkgd.min-js');
        wp_enqueue_script('masonry-js');
        wp_enqueue_script('slick-js');
        wp_enqueue_script('portfolio-js');
    } else if (is_page('guide-tbyb') || is_page('zoomforbegginer') || (is_checkout() && !empty(is_wc_endpoint_url('order-received')))) {
        // wp_enqueue_style('slick-style');
        // wp_enqueue_style('slicktheme-style');
        wp_enqueue_script('slick-js');
        wp_enqueue_script('slick-active');
    } else if (is_post_type_archive('portfolio')) {
        // wp_enqueue_style('portani-style');
        // wp_enqueue_style('portfolio-style');
        // wp_enqueue_style('slick-style');
        // wp_enqueue_style('slicktheme-style');
        wp_enqueue_script('imagesloaded.pkgd.min-js');
        wp_enqueue_script('masonry-js');
        wp_enqueue_script('slick-js');
        wp_enqueue_script('portfolio-js');
    } else if (is_checkout()) {
        wp_enqueue_script('formstep-js');
        wp_enqueue_script('checkout-js');
    } else if (is_shop() || is_product_category() || is_tax('series') || is_page('shop-all')) {
        wp_enqueue_script('woof-js');
        wp_enqueue_script('popup-js');
        wp_enqueue_script('newhome-js');
    } else if (is_product()) {
        // wp_enqueue_style('slick-style');
        // wp_enqueue_style('slicktheme-style');
        // wp_enqueue_style('giftcard-style');
        // wp_enqueue_style('nano-scroll-style');
        // wp_enqueue_style('yithoption-style');
        wp_enqueue_script('slick-js');
        wp_enqueue_script('popup-js');
        wp_enqueue_script('shopsingle-js');
        wp_enqueue_script('nano-scroll');
        wp_enqueue_script('newhome-js');
    } else if (is_page('register')) {
        wp_enqueue_script('register');
        wp_enqueue_script('register-js');
    } else if (is_singular('post') && (in_category(array('blog')) || post_is_in_descendant_category(get_category_by_slug('blog')->term_id))) {
        wp_enqueue_script('fontplus-js');
        wp_enqueue_script('parallax');
        wp_enqueue_script('masonry');
    }
}

add_action('wp_print_scripts', 'zoa_dequeue_script', 100);
function zoa_dequeue_script() {
    wp_deregister_script('quadmenu');
    wp_dequeue_script('quadmenu');
}

add_action('wp_enqueue_scripts', 'custom_scripts', 100000);
function custom_scripts() {

    wp_enqueue_script('autokana-js', get_stylesheet_directory_uri() . '/js/jquery.autoKana.js');
    wp_enqueue_script('validation_engine_js', get_stylesheet_directory_uri() . '/js/jquery.validationEngine.js');
    wp_enqueue_script('validation_engine_ja_js', get_stylesheet_directory_uri() . '/js/jquery.validationEngine-ja.js');
    if (!is_product()) {
        wp_enqueue_script('selectbox-js', get_stylesheet_directory_uri() . '/js/selectbox/selectbox.js');
    }
    wp_enqueue_script('overlay', get_stylesheet_directory_uri() . '/js/loadingoverlay.js');

    wp_register_script('quadmenu_new', get_stylesheet_directory_uri() . '/js/quadmenu/quadmenu.js', array('hoverIntent'), time(), true);
    wp_enqueue_script('quadmenu_new');
    //Birthday YYYY MM DD
    wp_enqueue_script('date-picks', get_stylesheet_directory_uri() . '/js/rolldate.min.js');
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array(), time(), true);

    if (is_page('contact')) {
        $aFormIds = array(6765, 6779);
        $validations = array();
        foreach ($aFormIds as $formID) {
            $form_data = get_post_meta($formID, 'mw-wp-form', true);
            $validations[] = array('form_id' => $formID, 'validation' => !empty($form_data['validation']) ? $form_data['validation'] : array());
        }
        wp_add_inline_script('custom-js', 'var contact_validations = ' . json_encode($validations) . ';', 'before');
    }
}


// validation script
add_action('wp_enqueue_scripts', 'validation_scripts');
function validation_scripts() {
    wp_register_script('parsley-js', get_stylesheet_directory_uri() . '/js/parsley.min.js', array(), false, true);
    wp_register_script('parsley-lang-en', get_stylesheet_directory_uri() . '/js/i18n/en.js', array(), false, true);
    wp_register_script('parsley-lang-ja', get_stylesheet_directory_uri() . '/js/i18n/ja.js?201812042322', array(), false, true);
    wp_register_script('parsley-script', get_stylesheet_directory_uri() . '/js/parsley-script.js?20181211132441', array('parsley-js'), false, true);
    // wp_register_style('validation-style', get_stylesheet_directory_uri() . '/css/validation.css?201812131628');
    if (is_page('contact') || is_checkout() || is_product() || is_page('register')) {
        wp_enqueue_script('parsley-js');
        if (get_locale() == 'ja') {
            wp_enqueue_script('parsley-lang-ja');
        } else {
            wp_enqueue_script('parsley-lang-en');
        }

        if (function_exists('validation_scripts')) {
            if (get_locale() == 'ja') {
                $tag = "window.Parsley.setLocale('ja');";
                wp_add_inline_script('parsley-lang-ja', $tag, 'after');
            } else {
                $tag = "window.Parsley.setLocale('en');";
                wp_add_inline_script('parsley-lang-en', $tag, 'after');
            }
        }
        wp_enqueue_script('parsley-script');
        // wp_enqueue_style('validation-style');
    }
}

// Add slug class to body
add_filter('body_class', 'body_class_section');
function body_class_section($classes) {
    global $post;
    
    if (is_page()) {
        if ($post->post_parent) {
            $parent = end(get_post_ancestors($post->ID)); // Replace $current_page_id with $post->ID
        } else {
            $parent = $post->ID;
        }

        $post_data = get_post($parent, ARRAY_A);
        $classes[] = 'parent-' . $post_data['post_name'];
    }
    
    return $classes;
}


/* Woo Add On plugin replace addon.js file */
add_action('wp_enqueue_scripts', 'my_addon_script');
function my_addon_script() {
    wp_enqueue_script('woocommerce-addons', get_stylesheet_directory_uri() . '/js/addons.js', array('jquery', 'accounting'), '1.0', true);
}

// Remove CSS and/or JS for Select2 used by WooCommerce
add_action('wp_enqueue_scripts', 'wsis_dequeue_stylesandscripts_select2', 100);
function wsis_dequeue_stylesandscripts_select2() {
    if (class_exists('woocommerce')) {
        wp_dequeue_style('selectWoo');
        wp_deregister_style('selectWoo');

        // Amin Shoukt
        // Causing issues for Yith Product Add-ons & Extra Options
        // If not product single page
        if ( ! is_product() ) {
            wp_dequeue_script('selectWoo');
            wp_deregister_script('selectWoo');
        }
    }
}

add_action('admin_enqueue_scripts', 'load_custom_wp_admin_custom_script');
function load_custom_wp_admin_custom_script() {
    wp_enqueue_script('admin_js', get_stylesheet_directory_uri() . '/js/admin.js?v=' . microtime(), array(
        'jquery'
    ));
    wp_enqueue_style('admin_css', get_stylesheet_directory_uri() . '/js/admin.css');

    if ( isset( $_REQUEST['page'] ) && 'birchschedule_settings' == $_REQUEST['page'] ) {
        wp_enqueue_script(
            'field-date-js',
            'Field_Date.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
            time(),
            true
        );
    }

    wp_enqueue_style('jquery-ui-datepicker', get_stylesheet_directory_uri() . '/css/jquery-ui.min.css');
}
