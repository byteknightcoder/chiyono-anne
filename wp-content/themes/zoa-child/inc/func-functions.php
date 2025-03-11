<?php
function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/* ! FOOTER
  -------------------------------------------------> */
if (!function_exists('zoa_footer')) :

    function zoa_footer()
    {
        $show_footer = zoa_footer_display();
        if (false == $show_footer)
            return;

        $column = get_theme_mod('ft_column', 4);
        $copyright = !empty(get_theme_mod('ft_copyright', '')) ? get_theme_mod('ft_copyright', '') : '&copy; ' . date('Y') . ' <strong>Zoa.</strong> &nbsp; • &nbsp; Privacy Policy &nbsp; • &nbsp; Terms of Use';
        $right_bot_right = get_theme_mod('ft_bot_right', '');

        /* WIDGET */
        if (is_active_sidebar('footer-widget')) :
?>
            <div class="footer-top">
                <div class="container">
                    <div class="c-footer_logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="c-footer_logo_link"><span class="svg-wrapper"><svg class="svg" width="320" height="116" viewBox="0 0 320 116">
                                    <use href="#svg-logo" xlink:href="#svg-logo" />
                                </svg></span></a>
                    </div>
                    <div class="row widget-box footer-col-<?php echo esc_attr($column); ?>">
                        <?php dynamic_sidebar('footer-widget'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php /* BASE */ ?>
        <div class="footer-bot">
            <div class="container">
                <div class="footer-logo"></div>
                <div class="footer-copyright"><?php echo wp_kses_post($copyright); ?></div>
                <div class="footer-bot-right"><?php echo wp_kses_post($right_bot_right); ?></div>
            </div>
        </div>
    <?php
    }

endif;


/* ICON HEADER MENU */
if (!function_exists('zoa_wc_header_action')) :

    function zoa_wc_header_action()
    {
        global $woocommerce;

        // Ensure WooCommerce is loaded before proceeding
        if (!isset($woocommerce)) {
            return;
        }

        $page_account = get_option('woocommerce_myaccount_page_id');
        $page_logout = wp_logout_url(get_permalink($page_account));

        if ('yes' == get_option('woocommerce_force_ssl_checkout')) {
            $logout_url = str_replace('http:', 'https:', $logout_url);
        }

        $count = isset($woocommerce->cart->cart_contents_count) ? $woocommerce->cart->cart_contents_count : 0;
        $wishlist_url = function_exists('tinv_url_wishlist_default') ? tinv_url_wishlist_default() : '#'; // Check if the wishlist function exists

        ?>
        <div class="menu-woo-action">
            <a href="<?php echo get_permalink($page_account); ?>" class="menu-woo-user<?php if (!is_user_logged_in()) : ?> signup_icon<?php else : ?> account_icon<?php endif; ?>">
                <?php if (!is_user_logged_in()) : ?>
                    <?php esc_html_e('Sign up / Login', 'zoa'); ?>
                <?php else : ?>
                    <?php esc_html_e('My Account', 'zoa'); ?>
                <?php endif; ?>
            </a>
        </div>
        <a href="<?php echo wc_get_cart_url(); ?>" id="shopping-cart-btn" class="oecicon oecicon-bag-20 menu-woo-cart js-cart-button">
            <span class="shop-cart-count"><?php echo esc_html($count); ?></span>
        </a>
        <?php if (function_exists('activation_tinv_wishlist')) { ?>
            <a href="<?php echo esc_url($wishlist_url); ?>" class="oecicon oecicon-heart-2-3 menu-woo-favorite"></a>
        <?php } ?>
        <?php
    }

endif;


/* ICON HEADER MENU MOBILE */
if (!function_exists('zoa_wc_header_action_mobile')) :

    function zoa_wc_header_action_mobile()
    {
        global $woocommerce;

        // Ensure WooCommerce is loaded before proceeding
        if (!isset($woocommerce)) {
            return;
        }

        $page_account = get_option('woocommerce_myaccount_page_id');
        $page_logout = wp_logout_url(get_permalink($page_account));

        if ('yes' == get_option('woocommerce_force_ssl_checkout')) {
            $logout_url = str_replace('http:', 'https:', $logout_url);
        }

        $count = isset($woocommerce->cart->cart_contents_count) ? $woocommerce->cart->cart_contents_count : 0;
        $wishlist_url = function_exists('tinv_url_wishlist_default') ? tinv_url_wishlist_default() : '#'; // Check if the wishlist function exists

        ?>
        <a href="<?php echo get_permalink($page_account); ?>" id="headerAccountLink" class="header__user__item header__user__link header__user__link--account">
            <?php if (!is_user_logged_in()) : ?>
                <?php esc_html_e('Sign in / Register', 'zoa'); ?>
            <?php else : ?>
                <?php esc_html_e('My Account', 'zoa'); ?>
            <?php endif; ?>
        </a>
        <?php if (function_exists('activation_tinv_wishlist')) { ?>
            <a href="<?php echo esc_url($wishlist_url); ?>" id="headerWishlistLink" class="header__user__item header__user__link header__user__link--wishlist"><?php esc_html_e('My Wishlist', 'zoa'); ?></a>
        <?php } ?>
        <?php
    }

endif;

// ユーザ権限の取得
function getUserLevel() {
    global $current_user;
    
    // Ensure the current user is properly set up
    if ( ! function_exists( 'wp_get_current_user' ) ) {
        return null;
    }

    wp_get_current_user();  // Ensure we get the current user data

    // Check if the caps property exists and is an array
    if ( isset( $current_user->caps ) && is_array( $current_user->caps ) ) {
        // Get the user levels (caps)
        $userLevel = array_keys( $current_user->caps );

        // Return the first user level if it exists, otherwise return null
        return isset( $userLevel[0] ) ? $userLevel[0] : null;
    }

    // Return null if no valid user level is found
    return null;
}


/**
  language switcher
 * */
function language_selector_flags()
{
    $userLevel = getUserLevel();
    if (function_exists('icl_object_id')) {
        $languages = icl_get_languages('skip_missing=0&orderby=code');
        if (!empty($languages) && $userLevel == "administrator" && is_user_logged_in()) {
            echo '<div class="lang_flag_switcher">';
            foreach ($languages as $l) {
    if (isset($l['url']) && !$l['active']) {
        echo '<div class="lang_flag"><a href="' . $l['url'] . '">';
    }
    if (isset($l['active']) && $l['active']) {
        echo '<div class="lang_flag active">';
    }
    if (isset($l['country_flag_url']) && isset($l['language_code'])) {
        echo '<img src="' . $l['country_flag_url'] . '" height="12" alt="' . $l['language_code'] . '" width="18" />';
    }
    if (isset($l['active']) && $l['active']) {
        echo '</div>';
    }
    if (isset($l['url']) && !$l['active']) {
        echo '</a></div>';
    }
}

            echo '</div>';
        }
    }
}

/* ! BLOG CATEGORIES
  -------------------------------------------------> */
if (!function_exists('zoa_blog_categories')) :

    function zoa_blog_categories()
    {
        return get_the_term_list(get_the_ID(), 'category', esc_html_x('', 'In Uncategorized Category', 'zoa'), ', ', null);
    }

endif;
/* ! BLOG POST INFO
  -------------------------------------------------> */
if (!function_exists('zoa_post_info')) :

    function zoa_post_info()
    {
        global $post;
    ?>
        <span class="if-item if-cat"><?php echo zoa_blog_categories(); ?></span>
        <time class="if-item if-date" itemprop="datePublished" datetime="<?php echo get_the_time('c'); ?>"><?php echo zoa_date_format(); ?></time>
    <?php
    }

endif;

//親ページ判別
function is_child($slug = "")
{
    if (is_singular()) : //投稿ページのとき（固定ページ含）
        global $post;
        if ($post->post_parent) { //現在のページに親がいる場合
            $post_data = get_post($post->post_parent); //親ページの取得
            if ($slug != "") { //$slugが空じゃないとき
                if (is_array($slug)) { //$slugが配列のとき
                    for ($i = 0; $i <= count($slug); $i++) {
                        if ($slug[$i] == $post_data->post_name || $slug[$i] == $post_data->ID || $slug[$i] == $post_data->post_title) { //$slugの中のどれかが親ページのスラッグ、ID、投稿タイトルと同じのとき
                            return true;
                        }
                    }
                } elseif ($slug == $post_data->post_name || $slug == $post_data->ID || $slug == $post_data->post_title) { //$slugが配列ではなく、$slugが親ページのスラッグ、ID、投稿タイトルと同じのとき
                    return true;
                } else {
                    return false;
                }
            } else { //親ページは存在するけど$slugが空のとき
                return true;
            }
        } else { //親ページがいない
            return false;
        }
    endif;
}

//just check child page nby path
function is_subpage()
{
    global $wp;
    $request = explode('/', $wp->request);
    if (count($request) >= 1) {
        $parentslug = $request[0];
    }
}

/* ! PAGE HEADER
  -------------------------------------------------> */
if (!function_exists('zoa_page_header')) :

    function zoa_page_header()
    {
        if (is_404())
            return;

        $c_header = zoa_page_header_slug();

        if ('disable' == $c_header)
            return;
    ?>
        <?php
        $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        global $wp;
        $request = explode('/', $wp->request);
        $is_sub_myaccount = count($request) > 1 && $request[0] == 'my-account';
        ?>
        <?php if (!is_singular('post') && !$is_sub_myaccount && !is_singular('product') && !is_checkout() && !is_page('reservation-thanks')) { ?>
            <div class="<?php if ((!'layout-1' == $c_header)) { ?>breadcrumb_row<?php } else { ?>page-header phd-<?php echo esc_attr($c_header); ?><?php } ?><?php if (has_post_thumbnail() && !is_home() && !is_archive() && !is_woocommerce()) { ?> has-bg js-parallax<?php } ?>" <?php if (has_post_thumbnail() && !is_home() && !is_archive() && !is_woocommerce()) { ?>style="background-image:url(<?php echo $featured_img_url; ?>);" <?php } ?>>
                <div class="max-width--large gutter-padding--full">
                    <?php /* BREADCRUMBS */ ?>
                    <div id="theme-bread" class="<?php if (is_singular('product') || (!'layout-1' == $c_header)) { ?>display--small-up<?php } else { ?>display--mid-up<?php } ?>">
                        <?php
                        if (function_exists('fw_ext_breadcrumbs')) {
                            if (!is_page('event') && !isset($_REQUEST['type']) && !isset($_SESSION['event_access'])) {
                                fw_ext_breadcrumbs();
                            }
                        }
                        ?>
                    </div>
                    <?php
                    /* PAGE TITLE */
                    if (('layout-1' == $c_header) && !is_singular('product')) :
                    ?>
                        <?php if (has_post_thumbnail() && !is_home() && !is_archive() && !is_woocommerce()) { ?><div class="bg_title_cover">
                                <div class="container"><?php } ?>
                                <div id="theme-page-title">
                                    <?php
                                    if (is_page('about')) {
                                        $svgPath = get_stylesheet_directory_uri() . '/fonts/';
                                        $svgId = 'About';
                                        $svgTitle = '<span class="svg-wrapper svg_' . $svgId . '"><svg class="icoca icoca-' . $svgId . '"><use xlink:href="' . $svgPath . 'symbol-icoca.svg#icoca-' . $svgId . '"></use></svg></span>';
                                        echo '<h1 class="page-title entry-title page-title__svg">' . $svgTitle . '</h1>';
                                    } else if (is_page('bespoke')) {
                                        $svgPath = get_stylesheet_directory_uri() . '/fonts/';
                                        $svgId = 'Bespoke';
                                        $svgTitle = '<span class="svg-wrapper svg_' . $svgId . '"><svg class="icoca icoca-' . $svgId . '"><use xlink:href="' . $svgPath . 'symbol-icoca.svg#icoca-' . $svgId . '"></use></svg></span>';
                                        echo '<h1 class="page-title entry-title page-title__svg">' . $svgTitle . '</h1>';
                                    } else {
                                        zoa_page_title();
                                    } ?>
                                    <?php
                                    if (!is_category('blog') && !is_archive('portfolio') && !is_page('event')) {
                                        if (!is_woocommerce() && function_exists('get_the_subtitle') && get_the_subtitle(get_the_ID()) != '') : 
                                            echo '<p class="page-subtitle">' . get_the_subtitle() . '</p>';
                                        endif;
                                    }
                                    ?>
                                </div>
                                <?php if (get_field('summary')) : ?><div class="short_summary"><?php the_field('summary'); ?></div>
                                <?php endif; ?>
                                <?php if (has_post_thumbnail() && !is_home() && !is_archive() && !is_woocommerce()) { ?>
                                </div>
                            </div>
                            <!--/.bg_title_cover---><?php } ?>
                    <?php endif; ?>



                </div>
            </div>
        <?php } else if (is_singular('product') || is_checkout()) { ?>
            <!--no breadcrumbs-->
        <?php } else { ?>
            <?php
            if (
                isset($_SERVER['HTTPS']) &&
                ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
                isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
            ) {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }

            $currenturl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $currenturl_relative = wp_make_link_relative($currenturl);
            if ($currenturl_relative == '/my-account/lost-password/' || $currenturl_relative == '/my-account/lost-password/?show-reset-form=true' || $currenturl_relative == '/my-account/lost-password/?exist_email') {
            ?>
                <div class="page-header phd-layout-1">
                    <div class="max-width--large gutter-padding--full">
                        <?php /* PAGE HEADER FOR BLOG POST */ ?>
                        <div id="theme-bread" class="display--small-up">
                            <?php
                            if (function_exists('fw_ext_breadcrumbs')) {
                                fw_ext_breadcrumbs();
                            }
                            ?>
                        </div>
                        <div id="theme-page-title">
                            <h1 class="page-title entry-title"><?php _e('Reset password', 'zoa'); ?></h1>
                        </div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="breadcrumb_row">
                    <div class="max-width--large gutter-padding--full">
                        <?php /* PAGE HEADER FOR BLOG POST */ ?>
                        <div id="theme-bread" class="display--small-up">
                            <?php
                            if (function_exists('fw_ext_breadcrumbs')) {
                                fw_ext_breadcrumbs();
                            }
                            ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
        <!--if ( !is_singular( 'post' ) )-->
    <?php
    }

endif;

//change custom post archive page title
/* ! PAGE TITLE
  -------------------------------------------------> */
if (!function_exists('zoa_page_title')) :

    function zoa_page_title()
    {

        /* PAGE TITLE */
        $title = get_the_title_product_chiyono();

        /* BLOG TITLE */
        $blog_title = get_theme_mod('blog_title', 'Blog');

        /* SHOP TITLE */
        $shop_title = get_theme_mod('shop_title', 'Shop');
        /* Exhibiton Title */
        $exb_title = "Exhibition Online Store";
    ?>
        <?php
        if (is_page('event')) {
            if (!empty(get_the_subtitle())) {
                $exb_title = get_the_subtitle();
            }
        ?>
            <div class="hello_desc">
                <h1 class="page-title entry-title"><?php echo $exb_title; ?></h1>
            </div>
        <?php } else { ?>
            <h1 class="page-title entry-title">
                <?php
                if (is_day()) :
                    printf(esc_html__('Daily Archives: %s', 'zoa'), get_the_date());
                elseif (is_month()) :
                    printf(esc_html__('Monthly Archives: %s', 'zoa'), get_the_date(esc_html_x('F Y', 'monthly archives date format', 'zoa')));
                elseif (is_home()) :
                    echo esc_html($blog_title);
                elseif (is_author()) :
                    $author = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
                    echo esc_html($author->display_name);
                elseif (is_year()) :
                    printf(esc_html__('Yearly Archives: %s', 'zoa'), get_the_date(esc_html_x('Y', 'yearly archives date format', 'zoa')));
                elseif (class_exists('woocommerce') && is_shop()) :
                    echo esc_html($shop_title);
                elseif (class_exists('woocommerce') && (is_product_tag() || is_tag())) :
                    esc_html_e('Tags: ', 'zoa');
                    single_tag_title();
                elseif (is_page() || is_single()) :
                    if (is_page()) {
                        echo !empty($title) ? $title : esc_html__('This post has no title', 'zoa');
                    } else {
                        echo !empty($title) ? esc_html($title) : esc_html__('This post has no title', 'zoa');
                    }
                elseif (is_tax()) :
                    global $wp_query;
                    $term = $wp_query->get_queried_object();
                    $tex_title = $term->name;
                    echo esc_html($tex_title);
                elseif (is_search()) :
                    esc_html_e('Search results', 'zoa');
                elseif (is_post_type_archive('portfolio')) :
                    esc_html_e('Portfolio', 'zoa');
                elseif (is_category()) :
                    if (is_category('blog')) {
                        echo 'STORIES';
                    } else {
                        echo single_cat_title();
                    }
                else :
                    esc_html_e('Archives', 'zoa');
                endif;
                ?>
            </h1>
    <?php
        }
    }

endif;

function validate_client_info()
{
    $field_group_fields = acf_get_fields(BOOKING_FORM_ID);
    $acf_post = $_POST['acf'];
    $save_fields = array();
    foreach ($field_group_fields as $field) {
        loop_to_get_sub_field($field, $save_fields);
    }
    $errors = array();
    foreach ($save_fields as $field) {
        $key_longs = explode('***', $field['key_long'] ? $field['key_long'] : $field['key']);
        foreach ($key_longs as $key_long) {
            $post_field = !isset($post_field) ? $acf_post[$key_long] : $post_field[$key_long];
        }
        if ($field['required']) {
            if (!$post_field) {
                $errors[$field['key']] = $field['label'] . __(' is required', 'birchschedule');
            }
        }

        $_POST[$field['key']] = $post_field;
        assign_booking_post_data($field);
        unset($post_field);
    }

    if ($_POST['is_register']) {
        $_REQUEST['user_login'] = $_POST['user_login'] = $_REQUEST['user_email'] = $_POST['user_email'] = $_POST['birs_client_email'];
        // Check email is exist or not.
        $user = register_new_user($_POST['user_login'], $_POST['user_email']);
        if (!is_wp_error($user)) {
            //Success
            $result = array();
            $result['result'] = true;
            $result['user'] = get_user_by('id', $user);
            $result['user']->set_role('customer');

            $result['message'] = __('Registration complete. Please check your e-mail.', 'birchschedule');
        } else {
            //Something's wrong
            $errors['is_register'] = $user->get_error_messages();
            if (!empty($errors['is_register']) && count($errors['is_register']) == 2) {
                $errors['is_register'] = $errors['is_register'][1];
            }
        }
    }
    return $errors;
}

function assign_booking_post_data($field)
{
    if ($field['name'] == 'last_name') {
        $_POST['birs_client_name_last'] = $_POST[$field['key']];
    } else if ($field['name'] == 'first_name') {
        $_POST['birs_client_name_first'] = $_POST[$field['key']];
    } else if ($field['name'] == 'tel') {
        $_POST['birs_client_phone'] = $_POST[$field['key']];
    } else if ($field['name'] == 'email') {
        $_POST['birs_client_email'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'address1')) {
        $_POST['birs_client_address1'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'address2')) {
        $_POST['birs_client_address2'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'address')) {
        $_POST['birs_client_address'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'city')) {
        $_POST['birs_client_city'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'state')) {
        $_POST['birs_client_state'] = $_POST[$field['key']];
    } else if (strpos($field['name'], 'zip')) {
        $_POST['birs_client_zip'] = $_POST[$field['key']];
    }
    $_POST['birs_client_country'] = 'JP';
}

function get_booking_form($step = '')
{
    if (!session_id()) {
        session_start();
    }

    // Add this to call acf assets
    ob_start();
    get_client_info_html($_SESSION['appointment_id'], $step);
    $acf_form = ob_get_contents();
    ob_end_clean();

    $acf_form = str_replace('<form id="' . BOOKING_FORM_ID . '" class="acf-form" action="" method="post">', '', $acf_form);
    $acf_form = str_replace('</form>', '', $acf_form);
    $acf_form = str_replace('required="required"', 'required="required" class="validate[required]"', $acf_form);

    echo $acf_form;
}

function get_gift_box_product_id()
{
    if (get_home_url() == 'https://chiyono-anne.com/' || get_home_url() == 'https://chiyono-anne.com') {
        return 384196; //gtw_get_order_gift_wrapper_product();
    } else {
        return 991134;
    }
}

function check_cart_has_no_giftbox()
{
    $is_has = false;
    if (is_cart() || is_checkout()) {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        if (!empty($items) && count($items) >= 1) {
            $product_gift_wrapper_id = get_gift_box_product_id();
            if ($product_gift_wrapper_id > 0) {
                $gift_wrapper = wc_get_product($product_gift_wrapper_id);
                if (is_object($gift_wrapper)) {
                    $gift_wrapper_id = $gift_wrapper->get_parent_id();
                } else {
                    $gift_wrapper_id = 0;
                }
            } else {
                $product_gift_wrapper_id = 0;
                $gift_wrapper_id = 0;
            }
            $gift_card_category_slug = 'mwb_wgm_giftcard';
            $special_service_category_slug = get_option('ch_special_service', '');
            foreach ($items as $key => $item) {
                if ($item['product_id'] == $product_gift_wrapper_id || $item['product_id'] == $gift_wrapper_id || is_product_in_cat($item['product_id'], $gift_card_category_slug) || is_product_in_cat($item['product_id'], $special_service_category_slug)) {
                    $is_has = true;
                    break;
                }
            }
        }
    }
    return $is_has;
}

function did_you_find_data()
{
    $did_you_find = array(
        __('テレビ、ラジオ', 'zoa'),
        __('雑誌', 'zoa'),
        __('インターネット検索結果', 'zoa'),
        __('インスタグラム', 'zoa'),
        __('SNS広告', 'zoa'),
        __('知人からの紹介', 'zoa'),
        __('以前に利用した', 'zoa'),
        __('その他', 'zoa'),
    );
    return $did_you_find;
}

// Utility function, to display BACS accounts details
function ch_get_bacs_account_details_html()
{
    ob_start();
    $bacs_info = get_option('woocommerce_bacs_accounts');
    ?>
    <div style="text-align: center;">
        <p style="text-align: center;">ご注文から5営業日以内にお振込をお願い致します。</p>
        <h2 style="font-style:normal;text-align: center;">お振り込み先銀行口座の詳細</h2>
        <?php
        $i = -1;
        if ($bacs_info) {
            foreach ($bacs_info as $account) {
                $i++;
                $account_name = esc_attr(wp_unslash($account['account_name']));
                $bank_name = esc_attr(wp_unslash($account['bank_name']));
                $account_number = esc_attr($account['account_number']);
                $sort_code = esc_attr($account['sort_code']);
                $iban_code = esc_attr($account['iban']);
                $bic_code = esc_attr($account['bic']);
        ?>
                <ul style="list-style-type:none;text-align: center;padding-left: 0;">
                    <li class="bank_name">銀行名: <strong><?php echo $bank_name; ?></strong></li>
                    <li class="sort_code">支店名: <strong><?php echo $sort_code; ?></strong></li>
                    <li class="iban">種別: <strong><?php echo $iban_code; ?></strong></li>
                    <li class="account_number">口座番号: <strong><?php echo $account_number; ?></strong></li>
                    <?php
                    if (!empty($bic_code)) {
                    ?>
                        <li class="bic"><?php _e('BIC'); ?>: <strong><?php echo $bic_code; ?></strong></li>
                    <?php } ?>
                    <li class="account_number">口座名義: <strong><?php echo $account_name; ?></strong></li>
                </ul>
        <?php
            }
        }
        ?>
    </div>
    <?php
    $output = ob_get_clean();
    return $output;
}

function arr_gift_card_products_use_offline()
{
    return array();
}

function check_gift_cart_product_in_order($order_id) {
    if ($order_id > 0 && function_exists('wc_get_order')) {
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        if (!empty($items)) {
            foreach ($items as $item) {
                $product_id = $item->get_product_id();
                if (in_array($product_id, arr_gift_card_products_use_offline())) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function show_latest_blog_posts($numberposts = 1)
{
    ob_start();

    $args = array(
        'post_type' => 'post',
        'post_status' => array('publish'),
        'nopaging' => false,
        'posts_per_page' => $numberposts,
        'ignore_sticky_posts' => false,
        'post__not_in' => array(get_the_ID()),
        'category_name' => 'blog',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
    ?>
        <div class="post-item__latest">
            <!--start loop-->
            <?php
            while ($query->have_posts()) : $query->the_post();
                $categories = get_the_category();
                if (isset($categories[count($categories) - 1])) :
                    $category = $categories[count($categories) - 1];
                    if ($category->slug != 'limited') {
                        if (has_post_thumbnail()) {
                            $thumb_id = get_post_thumbnail_id();
                            $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'full', true);
                            $thumb_url = isset($thumb_url_array[0]) ? $thumb_url_array[0] : ''; // Safeguard for thumbnail URL
                            $title = get_post($thumb_id)->post_title;
                            $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true) ?: ''; // Safeguard for alt text
                        }
                    }

            ?>
                    <div class="post__item">
                        <a href="<?php the_permalink(); ?>">
                            <div class="post-item__wrap">
                                <div class="post-item__thum">
                                    <div class="inner">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <img src="<?php echo esc_url($thumb_url) ?>" alt="<?php esc_attr_e($alt); ?>" title="<?php esc_attr_e($title); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="post-item__cont">
                                    <span class="post_cname"><?php echo $categories[count($categories) - 1]->name; ?></span>
                                    <h2 class="entry-title"><?php the_title(); ?></h2>
                                    <span class="rm_btn"><?php esc_html_e('Read more', 'zoa'); ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
            <?php
                endif;
            endwhile;
            wp_reset_postdata();
            ?>
            <!--/end loop-->
        </div>
    <?php
    endif;
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function generate_coupon_code()
{
    global $wpdb;

    // Get an array of all existing coupon codes
    $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");

    for ($i = 0; $i < 1; $i++) {
        $generated_code = strtolower(wp_generate_password(10, false));

        // Check if the generated code doesn't exist yet
        if (in_array($generated_code, $coupon_codes)) {
            $i--; // continue the loop and generate a new code
        } else {
            break; // stop the loop: The generated coupon code doesn't exist already
        }
    }
    return $generated_code;
}

function ch_get_taxonomy_hierarchy($taxonomy, $parent = 0, $slug = '', &$children = array())
{
    $taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
    if ($slug) {
        $terms = get_term_by('slug', $slug, $taxonomy);
        if (!empty($terms)) {
            $terms = array($terms);
        }
    } else {
        $terms = get_terms($taxonomy, array('parent' => $parent));
    }
    $children = !empty($children) ? $children : array();
    foreach ($terms as $term) {
        ch_get_taxonomy_hierarchy($taxonomy, $term->term_id, '', $children);
        $children[$term->term_id] = $term;
    }
    return $children;
}

function is_product_in_cat($product_id, $category_slug)
{
    $accessories = ch_get_taxonomy_hierarchy('product_cat', 0, $category_slug);
    $product_cats = get_the_terms($product_id, 'product_cat');
    foreach ($product_cats as $product_cat) {
        if (in_array($product_cat->term_id, array_keys($accessories))) {
            return true;
        }
    }
    return false;
}

function getItemDeliveryDate($package, $item_id)
{
    $product_id = $package['contents'][$item_id]['product_id'];
    //file_put_contents(dirname(__FILE__).'/product_id.txt', $product_id."\n",FILE_APPEND);
    $delivery_date = get_post_meta($product_id, 'deliver_date', true);
    $specific_deliver_date = get_post_meta($product_id, 'specific_deliver_date', true);
    $custom_field_type = get_post_meta($product_id, 'from_to', true);
    $from_to = '';
    if (!empty($custom_field_type)) {
        $from_to = implode("", $custom_field_type);
    }
    //file_put_contents(dirname(__FILE__).'/from_to.txt', $from_to."\n",FILE_APPEND);
    $key = $delivery_date . $specific_deliver_date . $from_to;
    if (empty(trim($key))) {
        $key = 0;
    }
    return $key;
}

function isOrderAllowCancel($order)
{
    if ($order->status == 'cancelled')
        return false;

    $today = date('Y-m-d', current_time('timestamp'));
    $hourNow = date('H', current_time('timestamp'));
    $date_created = $order->date_created->date('Y-m-d');

    $oToday = new DateTime($today);
    $oDateCreated = new DateTime($date_created);
    $dateDiff = $oToday->diff($oDateCreated);
    $nuber_date_diff = $dateDiff->d;

    if ($nuber_date_diff > 1) {
        $isAllow = false;
    } elseif ($nuber_date_diff == 1) {
        if ($hourNow <= 12) {
            // If <= 12 PM, allow
            $isAllow = true;
        } else {
            // If >= 12PM, not allow
            $isAllow = false;
        }
    } else {
        $isAllow = true;
    }
    return $isAllow;
}

function generate_coupon_code_each_auto()
{
    global $wpdb;

    // Get an array of all existing coupon codes
    $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");

    for ($i = 0; $i < 1; $i++) {
        $generated_code = strtolower(wp_generate_password(10, false));

        // Check if the generated code doesn't exist yet
        if (in_array($generated_code, $coupon_codes)) {
            $i--; // continue the loop and generate a new code
        } else {
            break; // stop the loop: The generated coupon code doesn't exist already
        }
    }
    return $generated_code;
}

function coupon_code_each_auto()
{
    // Here below define your coupons discount ammount

    $discount_amounts = array(10);

    // Set some coupon data by default
    $date_expires = date_i18n('Y-m-d', strtotime('+31 days'));
    $discount_type = 'percent';

    // Loop through the defined array of coupon discount amounts
    foreach ($discount_amounts as $coupon_amount) {
        // Get an emty instance of the WC_Coupon Object
        $coupon = new WC_Coupon();

        // Generate a non existing coupon code name
        $coupon_code = generate_coupon_code_each_auto();

        // Set the necessary coupon data (since WC 3+)
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type($discount_type);
        $coupon->set_amount($coupon_amount);

        $coupon->set_date_expires($date_expires);
        $coupon->set_usage_limit(1);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_individual_use(false);

        // Create, publish and save coupon (data)
        $coupon->save();
    }
    return strtoupper($coupon_code);
}

/**
 * Check Parent Category
 */
if (!function_exists('post_is_in_descendant_category')) {

    function post_is_in_descendant_category($cats, $_post = null)
    {
        foreach ((array) $cats as $cat) {
            $descendants = get_term_children((int) $cat, 'category');
            if ($descendants && in_category($descendants, $_post))
                return true;
        }
        return false;
    }
}

function get_exclude_cat_footer_navigation_press($exclude_press = false)
{
    $categoryTerms = get_terms(array(
        'taxonomy' => 'category',
        'hide_empty' => false,
    ));
    $excludeTerm = array();
    foreach ($categoryTerms as $categoryTerm) {
        if ($exclude_press) {
            if ($categoryTerm->slug == 'press') {
                $excludeTerm[] = $categoryTerm->term_id;
            }
        } else {
            if ($categoryTerm->slug != 'press') {
                $excludeTerm[] = $categoryTerm->term_id;
            }
        }
    }
    return $excludeTerm;
}

/* Override item meta data */
if (!function_exists('wc_display_item_meta')) {

    /**
     * Display item meta data.
     *
     * @since  3.0.0
     * @param  WC_Order_Item $item Order Item.
     * @param  array         $args Arguments.
     * @return string|void
     */
    function wc_display_item_meta($item, $args = array())
    {
        $strings = array();
        $html = '';
        $args = wp_parse_args($args, array(
            'before' => '<div class="product__attribute-meta"><div class="mini-product__item mini-product__attribute">',
            'after' => '</div></div>',
            'separator' => '</div><div class="mini-product__item mini-product__attribute">',
            'echo' => true,
            'autop' => false,
        ));

        foreach ($item->get_formatted_meta_data() as $meta_id => $meta) {
            $value = isset($args['autop']) && $args['autop'] ? wp_kses_post($meta->display_value) : wp_kses_post(make_clickable(trim($meta->display_value)));
            if ( 'bespoke' == strtolower($meta->display_key) && 'yes' == strtolower(trim(strip_tags($meta->display_value))) ) {
                $strings[] = '<span class="label"></span><span class="value">' . __('Bespoke Color', 'zoa') . '</span>';
            } else {
                $strings[] = '<span class="label">' . wp_kses_post($meta->display_key) . ': </span><span class="value">' . $value . '</span>';
            }
        }

        if ($strings) {
            $html = $args['before'] . implode($args['separator'], $strings) . $args['after'];
        }

        $html = apply_filters('woocommerce_display_item_meta', $html, $item, $args);

        if ($args['echo']) {
            echo $html; // WPCS: XSS ok.
        } else {
            return $html;
        }
    }
}

function insertAtSpecificIndex($array = [], $item = [], $position = 0)
{
    $previous_items = array_slice($array, 0, $position, true);
    $next_items = array_slice($array, $position, NULL, true);
    return $previous_items + $item + $next_items;
}

function get_taxonomy_hierarchy($taxonomy, $args = array())
{
    $taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
    $terms = get_terms($taxonomy, $args);
    $children = array();
    foreach ($terms as $term) {
        $args['parent'] = $term->term_id;
        $term->children = get_taxonomy_hierarchy($taxonomy, $args);
        $children[$term->term_id] = $term;
    }
    return $children;
}

function renderPortfolioCategories($is_mobile = false)
{
    $args = array(
        'orderby' => 'Date',
        'order' => 'desc',
        'hide_empty' => true,
        'parent' => 0
    );

    $hierarchy = get_taxonomy_hierarchy('portfolio_category', $args);
    if (count($hierarchy) > 0) {
        foreach ($hierarchy as $portfolio_cat) {
            renderPortfolioCategory($portfolio_cat, 0, $is_mobile);
        }
    }
}

function renderPortfolioCategory($portfolio_cat, $depth = 0, $is_mobile = false, $portfolio_parent = null)
{
    if (!empty($portfolio_cat->children)) {
        if (!$is_mobile) {
            echo '<li class="depth_' . $depth . '" data-id="p_' . $portfolio_cat->term_id . '">
				<span class="filter_link cta">' . $portfolio_cat->name . '</span>
				<span class="portfolio_cat_des" style="display:none">' . $portfolio_cat->description . '</span>';
            foreach ($portfolio_cat->children as $portfolio_child) {
                echo '<span class="portfolio_cat_child_title_hidden" style="display:none">' . $portfolio_child->name . '</span>
				<span class="portfolio_cat_child_des_hidden" style="display:none">' . $portfolio_child->description . '</span>';
            }

            echo '</li>';
        } else {
            echo '<li class="depth_' . $depth . '" data-id="p_' . $portfolio_cat->term_id . '" data-value="' . $portfolio_cat->name . '">
				<span class="filter_link cta">' . $portfolio_cat->name . '</span>
				<span class="portfolio_cat_des" style="display:none">' . $portfolio_cat->description . '</span>';
            foreach ($portfolio_cat->children as $portfolio_child) {
                echo '<span class="portfolio_cat_child_title_hidden" style="display:none">' . $portfolio_child->name . '</span>
				<span class="portfolio_cat_child_des_hidden" style="display:none">' . $portfolio_child->description . '</span>';
            }

            echo '</li>';
        }
        $depth++;
        foreach ($portfolio_cat->children as $portfolio_child) {
            renderPortfolioCategory($portfolio_child, $depth, $is_mobile, $portfolio_cat);
        }
    } else {
        if (!$is_mobile) {
            echo '<li class="depth_' . $depth . '" data-id="p_' . $portfolio_cat->term_id . '">
				<span class="filter_link cta">' . $portfolio_cat->name . '</span>
				<span class="portfolio_cat_des" style="display:none">' . $portfolio_cat->description . '</span>';
            if ($portfolio_parent) {
                echo '<span class="portfolio_cat_parent_title_hidden" style="display:none">' . $portfolio_parent->name . '</span>
				<span class="portfolio_cat_parent_des_hidden" style="display:none">' . $portfolio_parent->description . '</span>';
            }

            echo '</li>';
        } else {
            echo '<li class="depth_' . $depth . '" data-id="p_' . $portfolio_cat->term_id . '" data-value="' . $portfolio_cat->name . '">
				<span class="filter_link cta">' . $portfolio_cat->name . '</span>
				<span class="portfolio_cat_des" style="display:none">' . $portfolio_cat->description . '</span>';
            if ($portfolio_parent) {
                echo '<span class="portfolio_cat_parent_title_hidden" style="display:none">' . $portfolio_parent->name . '</span>
				<span class="portfolio_cat_parent_des_hidden" style="display:none">' . $portfolio_parent->description . '</span>';
            }

            echo '</li>';
        }
    }
}

function woo_template_loop_product_title()
{
    //echo '<a href="' . get_permalink() . '" class="c-product-item_link"></a>';
    echo '<h2 class="woocommerce-loop-product__title">' . get_the_title_product_chiyono() . '</h2>';
}

function zoa_wrap_product_image_override($size = 'woocommerce_thumbnail', $args = array())
{
    global $product;

    $image_size = apply_filters('single_product_archive_thumbnail_size', $size);

    $gallery = $product->get_gallery_image_ids();

    if ($product) {
    ?>
        <div class="product-image-wrapper">
            <a href="<?php echo get_permalink($product->get_id()); ?>" class="c-product-item--link_img">
                <?php
                /* PRODUCT IMAGE */
                // open tag <a>
                if (!is_home() && !is_front_page() && !is_shop() && !is_product_category() && !is_tax('series') && !is_page('shop-all') && !is_product()) {
                    woocommerce_template_loop_product_link_open();
                }
                echo zoo_get_product_thumbnail();

                /* HOVER IMAHE */
                if (!empty($gallery)) {
                    $hover = wp_get_attachment_image_src($gallery[0], $image_size);
                ?>
                    <span class="hover-product-image" style="background-image: url(<?php echo esc_url($hover[0]); ?>);"></span>
                <?php
                }
                // close tag </a>
                if (!is_home() && !is_front_page() && !is_shop() && !is_product_category() && !is_tax('series') && !is_page('shop-all') && !is_product()) {
                    woocommerce_template_loop_product_link_close();
                }
                ?>

                <?php
                /* LOOP ACTION */
                $loop_action_classes = 'loop-action';
                $quick_action = get_theme_mod('quick_action', 'false');
                if ($quick_action) {
                    $loop_action_classes .= ' loop-action--visible-on-mobile';
                }
                if (isset($_REQUEST['need_show_again'])) {
                ?>
                    <div class="<?php echo esc_attr($loop_action_classes); ?>">
                        <?php /* SHOW IN QUICK VIEW BTN */ ?>
                        <span data-pid="<?php echo esc_attr($product->get_id()); ?>" class="product-quick-view-btn zoa-icon-quick-view"></span>
                        <?php
                        /* ADD TO WISHLIST BUTTON */
                        echo do_shortcode("[ti_wishlists_addtowishlist loop=yes]");
                        # echo class_exists( 'YITH_WCWL' ) ? do_shortcode( '[yith_wcwl_add_to_wishlist]' ) : '';

                        /* ADD TO CART BUTTON */
                        if ($product) {
                            $defaults = array(
                                'quantity' => 1,
                                'class' => implode(' ', array_filter(array(
                                    'zoa-add-to-cart-btn',
                                    'button',
                                    'product_type_' . $product->get_type(),
                                    $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                    $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : '',
                                ))),
                                'attributes' => array(
                                    'data-product_id' => $product->get_id(),
                                    'data-product_sku' => $product->get_sku(),
                                    'aria-label' => $product->add_to_cart_description()
                                ),
                            );

                            $args = apply_filters('woocommerce_loop_add_to_cart_args', wp_parse_args($args, $defaults), $product);

                            echo sprintf('<a href="%s" data-quantity="%s" class="%s" %s>%s</a>', esc_url($product->add_to_cart_url()), esc_attr(isset($args['quantity']) ? $args['quantity'] : 1), esc_attr(isset($args['class']) ? $args['class'] : 'button'), isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '', esc_html(
                                $product->add_to_cart_text()
                            ));
                        }
                        ?>
                    </div>
                <?php } ?>
                <?php /* PRODUCT LABEL */ ?>
                <?php echo zoa_product_label($product); ?>
            </a>
        </div>
    <?php
    }
}

function loop_to_get_sub_field($field, &$save_fields)
{
    if (isset($field['sub_fields']) && !empty($field['sub_fields'])) {
        foreach ($field['sub_fields'] as $sub_field) {
            $sub_field['name_long'] = isset($field['name_long']) ? ($field['name_long'] . '_' . $sub_field['name']) : ($field['name'] . '_' . $sub_field['name']);
            $sub_field['key_long'] = isset($field['key_long']) ? ($field['key_long'] . '***' . $sub_field['key']) : ($field['key'] . '***' . $sub_field['key']);
            $sub_field['ID'] = $field['ID'];
            $sub_field['parent_name'] = $field['name'];
            $sub_field['sub_depth'] = isset($field['sub_depth']) ? $field['sub_depth'] + 1 : 1;

            $return_field = loop_to_get_sub_field($sub_field, $save_fields);
        }
    } else {
        $return_field = $field;
    }
    $save_fields[$return_field['ID']] = $return_field;
    return $return_field;
}

function getCartGiftCardData()
{
    $giftCardData = array();
    foreach (WC()->cart->get_cart() as $cart) {
        if (isset($cart['tinvwl_formdata'])) {
            $giftCardData = $cart['tinvwl_formdata'];
        }
    }
    return $giftCardData;
}

function isHideShippingByMailGiftCard()
{
    $giftCardData = array();
    foreach (WC()->cart->get_cart() as $cart) {
        // Check if 'tinvwl_formdata' and 'mwb_wgm_send_giftcard' exist
        if (isset($cart['tinvwl_formdata']) && isset($cart['tinvwl_formdata']['mwb_wgm_send_giftcard'])) {
            $giftCardData[$cart['tinvwl_formdata']['mwb_wgm_send_giftcard']] = $cart['tinvwl_formdata']['mwb_wgm_send_giftcard'];
        } else {
            return false;
        }
    }
    return count($giftCardData) == 1 && end($giftCardData) == 'Mail to recipient';
}


if (!function_exists('pll_current_language')) {

    function pll_current_language()
    {
        // Check if 'lang' exists in $_REQUEST and return its value; otherwise, return an empty string or a default value
        return isset($_REQUEST['lang']) ? $_REQUEST['lang'] : ''; // Safely handle missing 'lang' key
    }
}


function get_tbty_page_id()
{
    $post_term = get_page_by_path('guide-tbyb', OBJECT, 'page');
    if (isset($post_term)) {
        return $post_term->ID;
    } else {
        return 0;
    }
}

function get_tracking_url($order_id)
{
    ob_start();
    // Get tracking number and URL from post meta
    $track_numeber = get_post_meta($order_id, '_wcst_order_trackno', true);
    $track_url = get_post_meta($order_id, '_wcst_order_track_http_url', true);
    
    // Ensure both values are set and not empty before proceeding
    if (!empty($track_numeber) && !empty($track_url)) {
    ?>
        <p>
            <?php echo __('Tracking Number', 'zoa'); ?>: <?php echo esc_html($track_numeber); ?><br />
            <?php echo __('You can track below url.', 'zoa'); ?><br />
            <a target="_blank" href="<?php echo esc_url($track_url); ?>"><?php echo __('TRACK NOW', 'zoa'); ?></a>
        </p>
    <?php
    }
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

//for New Home Sections
function get_and_wrap_template_part( $slug,  $name = null,  $args = [])
{
    // Initialize $classType with an empty string to avoid the undefined variable warning
    $classType = '';

    if ($name == 'bloghome' || $name == 'category-link') {
        $classType = 'section_subh ';
    }

    //example, get_and_wrap_template_part('template-parts/partial', 'latest-catalog'); then use 'latest-catalog' as slug
    echo '<section class="section_common ' . $classType . 'section_' . esc_attr($name) . '">';
    get_template_part($slug, $name, $args);
    echo '</section>';
}

//check series show on home page
function is_allow_show_home($term_id){
    $current_time = current_time('timestamp');
    $start_date_show_on_home_page = get_term_meta($term_id, 'start_date_show_on_home_page', true);
    $end_date_show_on_home_page = get_term_meta($term_id, 'end_date_show_on_home_page', true);
    $pass_show_start = true;
    $pass_show_end = true;
    if (!empty($start_date_show_on_home_page) && $current_time < strtotime($start_date_show_on_home_page)) {
        $pass_show_start = false;
    }
    if (!empty($end_date_show_on_home_page) && $current_time > strtotime($end_date_show_on_home_page)) {
        $pass_show_end = false;
    }
    if ($pass_show_start == true && $pass_show_end == true) {
        return true;
    }else{
        return false;
    }
}

function create_developer_role() {
    $role = get_role('developer');
    if (!$role) {
        add_role(
            'developer',
            __('Developer'),
            array(
                'read' => true,
                'edit_posts' => true,
                'edit_published_posts' => true,
                'publish_posts' => true,
                'delete_posts' => true,
                'delete_published_posts' => true,
                'edit_pages' => true,
                'edit_published_pages' => true,
                'publish_pages' => true,
                'delete_pages' => true,
                'delete_published_pages' => true,
                'edit_themes' => true,
                'install_themes' => true,
                'switch_themes' => true,
                'edit_theme_options' => true,
                'edit_plugins' => true,
                'install_plugins' => true,
                'activate_plugins' => true,
                'update_plugins' => true,
                'upload_files' => true,
                'import' => true,
                'unfiltered_html' => true,
                'edit_users' => false,
                'create_users' => false,
                'list_users' => false,
                'promote_users' => false,
                'remove_users' => false,
                'manage_options' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'moderate_comments' => true,
                'edit_others_posts' => true,
                'edit_others_pages' => true,
                'create_shop_orders' => true,
                'edit_shop_orders' => true, // 注文編集の権限を追加
                'read_shop_order' => true,
                'edit_shop_order' => true, // 個別の注文編集権限を追加
                'delete_shop_orders' => false,
                'read_private_shop_orders' => true, // プライベート注文の閲覧権限を追加
                'edit_others_shop_orders' => true, // 他のユーザーの注文編集権限を追加
                'publish_shop_orders' => true, // 注文公開の権限を追加
                'edit_published_shop_orders' => true, // 追加
                'manage_woocommerce' => true, // 追加
                'view_woocommerce_reports' => true, // 追加
            )
        );
    }
}
add_action('init', 'create_developer_role');

function adjust_developer_capabilities() {
    $role = get_role('developer');
    if ($role) {
        // developerロールにのみ必要な権限を追加
        $developer_caps = array(
            'edit_plugins',
            'activate_plugins',
            'manage_options',
            'edit_theme_options',
            'manage_categories',
            'manage_links',
            'create_shop_orders',
            'edit_shop_orders',
            'read_shop_order',
            'edit_shop_order',
            'read_private_shop_orders',
            'edit_others_shop_orders',
            'publish_shop_orders',
            'edit_published_shop_orders',
            'manage_woocommerce',
            'view_woocommerce_reports'
        );

        foreach ($developer_caps as $cap) {
            $role->add_cap($cap);
        }

        // 注文の削除権限は与えない
        $role->remove_cap('delete_shop_orders');
    }
}
add_action('init', 'adjust_developer_capabilities');

function custom_wc_admin_menu_for_developer() {
    if (current_user_can('developer')) {
        // developerロール用のカスタムメニュー
        add_menu_page(__('WooCommerce', 'woocommerce'), __('WooCommerce', 'woocommerce'), 'manage_woocommerce', 'woocommerce', null, null, '55.5');
        add_submenu_page('woocommerce', __('Orders', 'woocommerce'), __('Orders', 'woocommerce'), 'edit_shop_orders', 'edit.php?post_type=shop_order');
        add_submenu_page('woocommerce', __('Create Order', 'woocommerce'), __('Create Order', 'woocommerce'), 'create_shop_orders', 'post-new.php?post_type=shop_order');
        add_submenu_page('woocommerce', __('Products', 'woocommerce'), __('Products', 'woocommerce'), 'edit_products', 'edit.php?post_type=product');
    }
}
add_action('admin_menu', 'custom_wc_admin_menu_for_developer', 99);

function restrict_admin_access_for_developer() {
    // developerロールを持っているが、administratorロールを持っていない場合のみ制限を適用
    if (current_user_can('developer') && !current_user_can('administrator')) {
        // WooCommerce メニューの再構築
        remove_action('admin_menu', 'wc_admin_menu');
        add_action('admin_menu', 'custom_wc_admin_menu', 99);

        // ユーザー一覧ページへのアクセスを制限
        remove_menu_page('users.php');
        
        // WooCommerce Customers Manager へのアクセスを制限
        remove_submenu_page('woocommerce', 'woocommerce-customers-manager');
        
        global $pagenow;
        if ($pagenow == 'users.php') {
            wp_redirect(admin_url());
            exit;
        }
        if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'woocommerce-customers-manager') {
            wp_redirect(admin_url());
            exit;
        }
    }
}
add_action('admin_init', 'restrict_admin_access_for_developer');
