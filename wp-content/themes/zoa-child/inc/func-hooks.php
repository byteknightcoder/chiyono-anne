<?php

// Start session early enough to avoid 'headers already sent' issue
// add_action('init', 'zoa_init_session', 0);  // Set the priority to 0

// function zoa_init_session() {
//     if (session_status() === PHP_SESSION_NONE) {
//         session_start();
//     }
// }

function get_the_title_product_chiyono( $post = 0 ) {
	$post = get_post( $post );

	$post_title = isset( $post->post_title ) ? $post->post_title : '';
	$post_id    = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {

			/* translators: %s: Protected post title. */
			$prepend = __( 'Protected: %s', 'zoa' );

			/**
			 * Filters the text prepended to the post title for protected posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Protected: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$protected_title_format = apply_filters( 'protected_title_format', $prepend, $post );

			$post_title = sprintf( $protected_title_format, $post_title );
		} else if ( isset( $post->post_status ) && 'private' === $post->post_status ) {

			/* translators: %s: Private post title. */
			$prepend = __( 'Private: %s', 'zoa' );

			/**
			 * Filters the text prepended to the post title of private posts.
			 *
			 * The filter is only applied on the front end.
			 *
			 * @since 2.8.0
			 *
			 * @param string  $prepend Text displayed before the post title.
			 *                         Default 'Private: %s'.
			 * @param WP_Post $post    Current post object.
			 */
			$private_title_format = apply_filters( 'private_title_format', $prepend, $post );

			$post_title = sprintf( $private_title_format, $post_title );
		}
	}

	/**
	 * Filters the post title.
	 *
	 * @since 0.71
	 *
	 * @param string $post_title The post title.
	 * @param int    $post_id    The post ID.
	 */
	return apply_filters( 'the_title_product_chiyono', $post_title, $post_id );
}

function the_title_product_chiyono( $before = '', $after = '', $echo = true ) {
	$title = get_the_title_product_chiyono();

	if ( strlen( $title ) == 0 ) {
		return;
	}

	$title = $before . $title . $after;

	if ( $echo ) {
		echo $title;
	} else {
		return $title;
	}
}



//crop portfolio image
add_theme_support('post-thumbnails');
add_image_size('portfolio', 600, 600, true);

//add custom css for elementor
//change post number for porfolo archive
function change_posts_per_page($query) {
    if (is_admin() || !$query->is_main_query())
        return;
    if ($query->is_archive('portfolio')) { //カスタム投稿タイプを指定
        $query->set('posts_per_page', '-1'); //表示件数を指定
    }
}

add_action('pre_get_posts', 'change_posts_per_page');

/**
 * Mailpoet remove first last name from setting
*/
add_filter('mailpoet_manage_subscription_page_form_fields', 'mp_remove_manage_fields', 10);

function mp_remove_manage_fields($form) {

    unset($form[0]); // First Name
    unset($form[1]); // Last Name

    return $form;
}

/**
 * Mailpoet unsubscribe message
*/
add_filter('mailpoet_unsubscribe_confirmation_page', 'mp_modify_unsubscribe_confirmation_page', 10, 2);

function mp_modify_unsubscribe_confirmation_page($HTML, $unsubscribeUrl) {
    $HTML = '<div class="deco_hr">';
    $HTML .= '<p class="p_sm p_unsubscribe">ニュースレーター購読解除は以下をクリックしてください。</p><a href="' . $unsubscribeUrl . '" class="unsubscribe_button">購読解除</a>';
    $HTML .= '</div>';
    return $HTML;
}

function hide_update_noticee_to_all_but_admin_users() {
    if (!is_super_admin()) {
        remove_all_actions('admin_notices');
    }
}

add_action('admin_head', 'hide_update_noticee_to_all_but_admin_users', 1);

//remove MW FORM MENU
function remove_menus() {
    if (!current_user_can('level_10')) {
        remove_menu_page('edit.php?post_type=mw-wp-form');
        remove_menu_page('edit.php?post_type=giftcard');
        remove_menu_page('edit.php?post_type=rl_gallery');
        remove_menu_page('edit.php?post_type=elementor_library');
        remove_menu_page('elementor');
        remove_menu_page('wcst-shipping-tracking');
        remove_menu_page('quadmenu_welcome');
        remove_menu_page('duplicator-pro');
        remove_menu_page('edit.php?post_type=birs_appointment');
        remove_menu_page('edit.php?post_type=birs_client');
        //remove_menu_page('');
    }
    remove_menu_page('edit.php?post_type=size_guide');
}

add_action('admin_menu', 'remove_menus', 9999999);

//remove status woo menu
add_action('admin_menu', 'remove_menu_pages', 999);

function remove_menu_pages() {
    //global $current_user;
    //$user_roles = $current_user->roles;
    //$user_role = array_shift($user_roles);
    if (!current_user_can('level_10')) {
        $remove_submenu = remove_submenu_page('woocommerce', 'wc-status');
    }
}

//remove customer manage option menu
add_action('admin_menu', 'remove_wccm_sub_menu_pages', 999);

function remove_wccm_sub_menu_pages() {
    if (!current_user_can('level_10')) {
        remove_submenu_page('woocommerce-customers-manager', 'wccm-options-page');
        remove_submenu_page('wcst-shipping-tracking', 'wcst-shipping-companies');
        remove_submenu_page('quadmenu_pro', 'manage_options');
        remove_submenu_page('woocommerce-customers-manager', 'acf-options-email-templates-configurator');
    }
}

// add code after opening body tag
add_action('after_body_open_tag', 'custom_content_after_body_open_tag');
function custom_content_after_body_open_tag() {
    ?>
    <div id="as-root"></div>
    <script>
        (function (e, t, n) {
            var r, i = e.getElementsByTagName(t)[0];
            if (e.getElementById(n))
                return;
            r = e.createElement(t);
            r.id = n;
            r.src = "//button.aftership.com/all.js";
            i.parentNode.insertBefore(r, i)
        })(document, "script", "aftership-jssdk")
    </script>
    <?php
}


add_filter('wp', '__deactivate_rocket_lazyload_portfolio');
function __deactivate_rocket_lazyload_portfolio() {
    if (is_post_type_archive('portfolio') || is_page('about')) {
        add_filter('do_rocket_lazyload', '__return_false');
        add_filter('wp_lazy_loading_enabled', '__return_false');
    }
}

add_filter('jetpack_lazy_images_blocked_classes', 'bbloomer_exclude_custom_logo_class_from_lazy_load', 9999);
function bbloomer_exclude_custom_logo_class_from_lazy_load($classes) {
    $classes[] = 'attachment-woocommerce_thumbnail';
    $classes[] = 'portfolio_img';
    return $classes;
}

/**
 * Theme functions file
 */
/* * *Remove Admin Notification except super admin** */
// add_action('admin_head', 'get_user_role');

// function get_user_role() {
//     global $current_user;
//     $user_roles = $current_user->roles;
//     $user_role = array_shift($user_roles);
//     if ($user_role != "administrator") {
//         add_action('init', create_function('$a', "remove_action( 'init', 'wp_version_check' );"), 2);
//         add_filter('pre_option_update_core', create_function('$a', "return null;"));
//     }
// }

/**
 * Google API Domain Auth Varify
 */
add_action('wp_head', 'verify_domain_google');
function verify_domain_google() {
    echo '<meta name="google-site-verification" content="J_KSfi4i16Ibz1SRU8er2Biv6mq7ekPl7UI3j-b9vq8" />';
}

/**
 * Enqueue scripts and styles.
 */
/* hide adminbar */
add_filter('show_admin_bar', '__return_false');
// Remove the product rating display on product loops
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);


/* * **************
  Booked Form Step Button
 * *************** */
// add_action('booked_btn_hook', 'booked_button', 7);
// function booked_button() {
//     echo '<div class="btn-group">
//             <input type="button" class="btn btn--inverse btn--1 btn--prev js-prev" value="' . __('Previous', 'zoa') . '" /> 
//             <input type="button" class="btn btn--next js-next" value="' . __('Next', 'zoa') . '" />
//             <input type="submit" class="btn btn--2" value="' . __('Book an Appointment', 'booked') . '" />
//          </div>';
// }

//add WPML lang class to body
if (function_exists('icl_object_id')) {
    add_filter('body_class', 'append_language_class');

    function append_language_class($classes) {

        $classes[] = ICL_LANGUAGE_CODE;  //or however you want to name your class based on the language code
        return $classes;
    }

}

add_action('after_setup_theme', 'lab_setup');
if (!function_exists('lab_setup')) {
    function lab_setup() {
        register_nav_menus(array(
            'footer_left' => 'Footer Left',
            'footer_right' => 'Footer Right',
        ));
    }
}

add_action('init', 'remove_cart_actions_parent_theme');
//override elementor widget by child theme
/* ! override PARENT THEME WIDGETS
  -------------------------------------------------> */
function zoa_widgetsv2() {

    $widgets = glob(get_stylesheet_directory() . '/elementor/widgets/*.php');

    foreach ($widgets as $key) {
        if (file_exists($key)) {
            require_once $key;
        }
    }
}

add_action('template_redirect', 'elementor_ovverides');
function elementor_ovverides() {
    remove_all_actions('elementor/widgets/widgets_registered');
    add_action('elementor/widgets/widgets_registered', 'zoa_widgetsv2');
}

// exclude press post from blog page
add_filter('pre_get_posts', 'exclude_category_home');
function exclude_category_home($query) {
    if ($query->is_home) {
        $query->set('cat', '-134');
    }
    return $query;
}

add_action('pre_current_active_plugins', 'hide_plugin_order_by_product');
function hide_plugin_order_by_product() {
    global $wp_list_table;
    $hidearr = array(
        'birchschedule/birchschedule.php',
    );
    $active_plugins = get_option('active_plugins');

    $myplugins = $wp_list_table->items;
    foreach ($myplugins as $key => $val) {
        if (in_array($key, $hidearr) && in_array($key, $active_plugins)) {
            unset($wp_list_table->items[$key]);
        }
    }
}

add_action('register_new_user', 'autoLoginUser', 10, 1);
function autoLoginUser($user_id) {
    $user = get_user_by('id', $user_id);
    if ($user && isset($_POST['is_register'])) {
        if ($_POST['user_password']) {
            wp_set_password($_POST['user_password'], $user_id);
        }
        wp_set_current_user($user_id, $user->user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $user->user_login, $user);
    }
}

add_filter('loop_shop_columns', 'zoa_four_shop_column', 1000, 1);
function zoa_four_shop_column($column) {
    if (is_page('special-event')){
        return 4;
    }else{
        return 3;
    }
}
// replace wishlist page id with myaccount page id when necessary
add_filter('tinvwl_addtowishlist_return_ajax', 'zoa_addtowishlist_return_ajax', 20);
add_filter('tinvwl_addtowishlist_dialog_box', 'zoa_addtowishlist_return_ajax', 20);
#tinvwl_addtowishlist_dialog_box

function zoa_addtowishlist_return_ajax($data) {
    #$data['msg'] = str_replace($data['wishlist_url'], tinv_url_wishlist_default(), $data['msg']);
    $data['wishlist_url'] = tinv_url_wishlist_default();
    if (!empty($data['redirect'])) {
        $data['redirect'] = $data['wishlist_url'];
    }

    return $data;
}

function zoa_wpml_object_id($page_id, $type) {
    if ('page' == $type && $page_id == tinv_get_option('page', 'wishlist')) {
        if (!is_page($page_id)) {
            $page_id = wc_get_page_id('myaccount');
        }
    }

    return $page_id;
}

// replace wishlist page id with myaccount page id when necessary
add_action('wp_loaded', 'zoa_filter_wpml_object_id', 10);
function zoa_filter_wpml_object_id() {
    add_filter('wpml_object_id', 'zoa_wpml_object_id', 10, 2);
}

// reverse the change of wishlist page id with myaccount page id
add_action('wp', 'zoa_unfilter_wpml_object_id', 10);
function zoa_unfilter_wpml_object_id() {
    remove_filter('wpml_object_id', 'zoa_wpml_object_id', 10, 2);
}

add_filter('manage_edit-birs_appointment_columns', 'zoa_manage_birs_appointment_columns', 1000, 1);
function zoa_manage_birs_appointment_columns($columns) {
    unset($columns['title']);
    unset($columns['date']);
    $columns['id'] = __('Appointment Number', 'zoa');
    $columns['customer_name'] = __('Customer Name', 'zoa');
    $columns['customer_phone'] = __('Phone', 'zoa');
    $columns['customer_email'] = __('Email', 'zoa');
    $columns['booked_date'] = __('Booked Date', 'zoa');
    $columns['date'] = __('Date', 'zoa');
    return $columns;
}

add_filter('manage_birs_appointment_posts_custom_column', 'zoa_modify_birs_appointment_column', 1000, 2);
function zoa_modify_birs_appointment_column($column, $postid) {
    global $birchschedule;
    $appointment = $birchschedule->model->mergefields->get_appointment_merge_values($postid);

    $args = array(
        'meta_key' => '_birs_appointment_id',
        'meta_value' => $postid,
        'post_status' => 'publish',
        'post_type' => 'birs_appointment1on1',
        'posts_per_page' => 1
    );
    $appointment1on1 = get_posts($args);
    $client_id = get_post_meta($appointment1on1[0]->ID, '_birs_client_id', true);

    $appointment = $birchschedule->model->get($postid, array(
        'base_keys' => array(),
        'meta_keys' => $birchschedule->model->get_appointment_fields()
    ));

    if ($column == 'id') {
        echo '<strong><a href="' . get_edit_post_link($postid) . '">' . $postid . '</a></strong>';
        echo '
			<div class="row-actions">
				<span class="edit">
					<a href="' . get_edit_post_link($postid) . '" >編集</a>
					|
				</span>
				<span class="trash">
					<a href="' . get_delete_post_link($postid, '', true) . '" class="submitdelete" aria-label="">ゴミ箱へ移動</a>
					|
				</span>
			</div>
		';
    } else if ($column == 'customer_name') {
// 		echo '<a target="_blank" href="'. site_url('wp-admin/post.php?post='. $client_id .'&action=edit') .'">';
        echo get_post_meta($client_id, '_birs_client_name_last', true) . get_post_meta($client_id, '_birs_client_name_first', true);
        echo ' (' . get_post_meta($client_id, 'name_kana_last_name', true) . get_post_meta($client_id, 'name_kana_first_name', true) . ')';
// 		echo '</a>';
    } else if ($column == 'customer_phone') {
        echo get_post_meta($client_id, '_birs_client_phone', true);
    } else if ($column == 'customer_email') {
        echo get_post_meta($client_id, '_birs_client_email', true);
    } else if ($column == 'booked_date') {
        echo $appointment['_birs_appointment_datetime'];
    }
    return $column;
}

add_action("add_meta_boxes", "zoa_add_custom_order_detail_meta_box");
function zoa_add_custom_order_detail_meta_box($postType) {
    if ($postType == 'shop_order') {
        add_meta_box("order-deliver-option-meta-box", __('Deliver Option', 'zoa'), "meta_box_deliver_option_markup", "shop_order", "side");
    }
}

add_action("save_post", "zoa_save_custom_order_detail_meta_box", 10, 3);
function zoa_save_custom_order_detail_meta_box($post_id, $post, $update) {
    if (isset($_POST['shipping_delivery_option'])) {
        update_post_meta($post_id, 'shipping_delivery_option', $_POST['shipping_delivery_option']);
    }
}

add_filter('haet_mail_use_template', 'zoa_haet_mail_use_template', 10, 2);
function zoa_haet_mail_use_template($use_template, $mail_data) {
    if (strpos((string)$mail_data['headers'], 'reservation=true') !== false) {
        $use_template = false;
    }
    return $use_template;
}

add_filter('body_class', 'zoa_body_class', 10, 1);
function zoa_body_class($classes) {
    $classes[] = get_locale();
    return $classes;
}

add_action('admin_init', 'post_limit_general_section');
function post_limit_general_section() {
    add_settings_section(
        'post_limit_settings_section', // Section ID
        __('Header Latest Banner Limit', 'zoa'), // Section Title
        'post_limit_section_options_callback', // Callback
        'general' // Page (General Settings Page)
    );

    add_settings_field( // Option 1
        'post_limit_banner_header', // Option ID
        __('Limit Banner Number', 'zoa'), // Title
        'post_limit_textbox_callback', // Callback
        'general', // Page (General Settings)
        'post_limit_settings_section', // Section Name
        array( // Arguments
            'post_limit_banner_header' // Option ID
        )
    );

    register_setting('general', 'post_limit_banner_header', 'esc_attr');
}

function post_limit_textbox_callback($args) {  // Textbox Callback
    $option = get_option($args[0]);
    echo '<input type="text" id="' . $args[0] . '" name="' . $args[0] . '" value="' . $option . '" />';
}

function post_limit_section_options_callback() { // Section Callback
}

// add new column for Appointments list in woocommerce-customers-manager plugin
add_filter('manage_customers_columns', 'ch_manage_customers_columns');
function ch_manage_customers_columns($columns) {
    $columns['appointments_list'] = __('Appointments list', 'woocommerce-customers-manager');
    //$columns['customer_rank'] = __('Customer Rank', 'woocommerce-customers-manager');
    return $columns;
}

add_filter('manage_customers_custom_column', 'ch_manage_customers_custom_column', 10, 3);
function ch_manage_customers_custom_column($abs = null, $column_name, $item) {
    if ($column_name == 'appointments_list') {
        return '<a class="" target="_blank" href="' . admin_url('admin.php?page=appointments-list&user_id=' . $item) . '"><span class="wp-menu-image dashicons-before dashicons-calendar-alt"></span></a>';
    } elseif ($column_name == 'customer_rank') {
//        if (in_array('woocommerce-customers-manager/customers-manager.php', apply_filters('active_plugins', get_option('active_plugins')))) {
//            $rank=mr_get_member_rank($item,true);
//            return $rank['rank'];
//        }else{
//            return '';
//        }
    } else {
        return $item[$column_name];
    }
}

add_action('admin_init', 'ch_rename_plugin_menus');
function ch_rename_plugin_menus() {
    global $menu;

    $updates = array(
        "YITH" => array(
            'name' => __('Other Tools', 'zoa')
        )
    );
    if (!empty($menu)) {
        foreach ($menu as $k => $props) {

            // Check for new values
            $new_values = ( isset($updates[$props[0]]) ) ? $updates[$props[0]] : false;
            if (!$new_values)
                continue;

            // Change menu name
            $menu[$k][0] = $new_values['name'];
        }
    }
}

// process to save portfolio image for booked
add_action('template_redirect', 'ch_portfolio_image_for_booked');
function ch_portfolio_image_for_booked() {
    if (isset($_GET['pid'])) {
        if (is_page('reservation-form')) {
            $_SESSION['pid'] = $_GET['pid'];

            if (isset($_REQUEST['serie_index']) && $_REQUEST['serie_index']) {
                $images_series = get_field('images_series', $_GET['pid']);
                foreach ($images_series as $loop_series_index => $images_serie) {
                    if ($_REQUEST['serie_index'] == $loop_series_index + 1) {
                        $pt_categorized_images = $images_serie['images'];
                        foreach ($pt_categorized_images as $pt_categorized_image) {
                            $image = $pt_categorized_image['sizes']['woocommerce_thumbnail'];
                            $_SESSION['image_id'] = $pt_categorized_image['ID'];
                        }
                        break;
                    }
                }
            } else {
                $image = get_the_post_thumbnail_url($_GET['pid']);
            }
            if ($image == '') {
                $image = get_stylesheet_directory_uri() . '/images/pf_sample_thum.jpg';
            }
            $_SESSION['p_image'] = $image;
        } else {
            unset($_SESSION['pid']);
            unset($_SESSION['p_image']);
            unset($_SESSION['image_id']);
        }
    } else {
        unset($_SESSION['pid']);
        unset($_SESSION['p_image']);
        unset($_SESSION['image_id']);
    }
}

// show only 1st and 2nd sub menus booked for user is shop manager role
// add_action('admin_menu', 'ch_booked_menu_user_shop_manager_role', 999);
function ch_booked_menu_user_shop_manager_role() {
    $user = wp_get_current_user();
    if (in_array('shop_manager', (array) $user->roles)) {
        remove_submenu_page('booked-appointments', 'booked-pending');
        remove_submenu_page('booked-appointments', 'edit-tags.php?taxonomy=booked_custom_calendars');
        remove_submenu_page('booked-appointments', 'booked-settings');
        remove_submenu_page('booked-appointments', 'booked-welcome');
        remove_submenu_page('booked-appointments', 'booked-feeds');
        remove_submenu_page('booked-appointments', 'booked_wc_payment_options');
        remove_submenu_page('booked-appointments', 'booked-install-addons');
    }
}

add_action('wp_footer', 'ch_ss_hidden_fields');
function ch_ss_hidden_fields() {
    if (is_user_logged_in() && is_page('reservation-form')) {
        if (isset($_SESSION['ss_date'])) {
            echo '<input type="hidden" id="ss-date" value="' . $_SESSION['ss_date'] . '" /> ';
            unset($_SESSION['ss_date']);
        }
        if (isset($_SESSION['ss_title'])) {
            echo '<input type="hidden" id="ss-title" value="' . $_SESSION['ss_title'] . '" /> ';
            unset($_SESSION['ss_title']);
        }
        if (isset($_SESSION['ss_timeslot'])) {
            echo '<input type="hidden" id="ss-timeslot" value="' . $_SESSION['ss_timeslot'] . '" /> ';
            unset($_SESSION['ss_timeslot']);
        }
        if (isset($_SESSION['ss_calendar_id'])) {
            echo '<input type="hidden" id="ss-calendar-id" value="' . $_SESSION['ss_calendar_id'] . '" /> ';
            unset($_SESSION['ss_calendar_id']);
        }
    }
}


add_filter('wppb_register_pre_form_message', 'elsey_wppb_register_pre_form_message', 999, 1);
function elsey_wppb_register_pre_form_message($message) {
    return '';
}

add_action('init', 'cptui_register_my_cpts');
function cptui_register_my_cpts() {

    /**
     * Post Type: Portfolios.
     */
    $labels = array(
        "name" => __("Portfolios", "custom-post-type-ui"),
        "singular_name" => __("Portfolio", "custom-post-type-ui"),
        "not_found" => __("No Portfolio Found", "custom-post-type-ui"),
        "archives" => __("Portfolio", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Portfolios", "custom-post-type-ui"),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "delete_with_user" => false,
        "show_in_rest" => false,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => "portfolio",
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "exclude_from_search" => true,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => array("slug" => "portfolio", "with_front" => true),
        "query_var" => true,
        "supports" => array("title", "editor", "thumbnail"),
    );

    register_post_type("portfolio", $args);
}

add_action('init', 'cptui_register_my_taxes');
function cptui_register_my_taxes() {

    /**
     * Taxonomy: Series.
     */
    $labels = array(
        "name" => __("Series", "custom-post-type-ui"),
        "singular_name" => __("Series", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Series", "custom-post-type-ui"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'series', 'with_front' => true,),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "series",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => true,
    );
    register_taxonomy("series", array("product"), $args);

    /**
     * Taxonomy: Body Shapes.
     */
    $labels = array(
        "name" => __("Body Shapes", "custom-post-type-ui"),
        "singular_name" => __("Body Shape", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Body Shapes", "custom-post-type-ui"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'bodyshape', 'with_front' => true,),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "bodyshape",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => true,
    );
    register_taxonomy("bodyshape", array("product"), $args);

    /**
     * Taxonomy: Body Shapes.
     */
    $labels = array(
        "name" => __("Bust Types", "custom-post-type-ui"),
        "singular_name" => __("Bust Type", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Bust Types", "custom-post-type-ui"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'busttype', 'with_front' => true,),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "busttype",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => true,
    );
    register_taxonomy("busttype", array("product"), $args);
    /**
     * Taxonomy: Category.
     */
    $labels = array(
        "name" => __("Category", "custom-post-type-ui"),
        "singular_name" => __("category", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Category", "custom-post-type-ui"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'portfolio_category', 'with_front' => true,),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "portfolio_category",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => true,
    );
    register_taxonomy("portfolio_category", array("portfolio"), $args);

    /**
     * Taxonomy: Bespoke Types.
     */
    $labels = array(
        "name" => __("Bespoke Types", "custom-post-type-ui"),
        "singular_name" => __("Bespoke Type", "custom-post-type-ui"),
    );

    $args = array(
        "label" => __("Bespoke Types", "custom-post-type-ui"),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => false,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => array('slug' => 'bespoke_type', 'with_front' => true,),
        "show_admin_column" => false,
        "show_in_rest" => false,
        "rest_base" => "bespoke_type",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "show_in_quick_edit" => false,
    );
    register_taxonomy("bespoke_type", array("portfolio"), $args);
}

add_action('template_redirect', 'redirect_page', 999);
function redirect_page() {

    if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }

    $currenturl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $currenturl_relative = wp_make_link_relative($currenturl);
    $urlto='';
    switch ($currenturl_relative) {
        case '/reservation':
            $urlto = home_url('reservation-form');
            break;
        case '/reservation/':
            $urlto = home_url('reservation-form');
            break;
        case '/reservation-confirm/':
            $urlto = home_url('reservation-form');
            break;
        case '/shop':
            $urlto = home_url('shop-all');
            break;
        case '/shop/':
            $urlto = home_url('shop-all');
            break;
        case '/news/info/コンサルテーションフィッティング予約受付を再/':
            $urlto = home_url('news/info/news20012201/');
            break;
        case '/press/jan/':
            $urlto = home_url('press/25ans/');
            break;
        case '/press/talkies/':
            $urlto = home_url('press/mery-vol2/');
            break;
        default:
            break;
    }
    //for blog 230908 redirect
    if (strpos($_SERVER['REQUEST_URI'], 'blog/her-order/230908') != false){
        $urlto = str_replace("blog/her-order/230908", "blog/her-order/time230908", $currenturl);
    }
    //end
    if ($currenturl != $urlto&&!empty($urlto))
        exit(wp_redirect($urlto, 301));
}


add_filter('protected_title_format', 'remove_protected_text');
function remove_protected_text() {
    return __('%s', 'zoa');
}

// disable update function on some plugins, that customized
add_filter('site_transient_update_plugins', 'filter_plugin_updates');
function filter_plugin_updates($value) {
    unset($value->response['booked/booked.php']);
    unset($value->response['iconic-woo-product-configurator-premium/iconic-woo-product-configurator.php']);
    unset($value->response['import-export-order-customer-csv/export-import.php']);
    unset($value->response['mw-wp-form/mw-wp-form.php']);
    unset($value->response['material-wp/material-wp.php']);
    unset($value->response['woocommerce-customers-manager/customers-manager.php']);
    unset($value->response['woocommerce-delivery-notes/woocommerce-delivery-notes.php']);
    unset($value->response['woocommerce-products-filter/index.php']);
    unset($value->response['woocommerce-shipping-pro/woocommerce-shipping-pro.php']);
    unset($value->response['woocommerce-ultimate-gift-card/woocommerce-ultimate-gift-card.php']);
    unset($value->response['woo-paydesign/woo-paydesign.php']);
    unset($value->response['kia-subtitle/kia-subtitle.php']);
    unset($value->response['kirki/kirki.php']);
    unset($value->response['digits/digit.php']);
    unset($value->response['quadmenu/quadmenu.php']);
    unset($value->response['yith-woocommerce-advanced-product-options-premium/init.php']);
    unset($value->response['iconic-woo-attribute-swatches-premium/iconic-woo-attribute-swatches.php']);
    unset($value->response['woothumbs-premium/iconic-woothumbs.php']);
    unset($value->response['yith-infinite-scrolling/init.php']);
    unset( $value->response['free-gifts-for-woocommerce/free-gifts-for-woocommerce.php'] );
    unset($value->response['wp-html-mail/wp-html-mail.php']);
    //unset($value->response['ga-ecommerce/ga-ecommerce.php']);
    return $value;
}

if (is_admin()) {
    add_action('wp_default_scripts', 'wp_default_custom_scripts');
    function wp_default_custom_scripts($scripts) {
        $scripts->add('wp-color-picker', "/wp-admin/js/color-picker.js", array('iris'), false, 1);
        did_action('init') && $scripts->localize(
                        'wp-color-picker', 'wpColorPickerL10n', array(
                    'clear' => __('Clear'),
                    'clearAriaLabel' => __('Clear color'),
                    'defaultString' => __('Default'),
                    'defaultAriaLabel' => __('Select default color'),
                    'pick' => __('Select Color'),
                    'defaultLabel' => __('Color value'),
                        )
        );
    }

}

add_filter('wpforms_process_smart_tags', 'wpf_smart_tags_shortcodes', 12, 1);
function wpf_smart_tags_shortcodes($content) {
    return do_shortcode($content);
}

//function custom_wpforms_email_message($message, $instance) {
//    $message = str_replace(array("<br />", "<br/>"), '', $message);
//    return $message;
//}

// add_filter('wpforms_email_message', 'custom_wpforms_email_message', 10, 2);
add_action('media_buttons', 'add_my_media_button', 99);
function add_my_media_button() {
    echo '<div><strong>Shortcode of Reservation page: [reservation_page]</strong></div>';
}

add_filter('quadmenu_nav_menu_start_el', 'my_hook_nav_menu_start_el', 10, 4);
function my_hook_nav_menu_start_el($output = '', $item, $args, $depth) {
    if ($item->post_name == 'stories') {
        $date_expires = date_i18n('Y-m-d', strtotime('-7 days'));
        $arr_date_post = explode("-", $date_expires);
        $args = array(
            'post_type' => 'post',
            'post_status' => array('publish'),
            'posts_per_page' => -1,
            'category_name' => 'blog',
            'date_query' => array(
                array(
                    'after' => array(
                        'year' => $arr_date_post[0],
                        'month' => $arr_date_post[1],
                        'day' => $arr_date_post[2],
                    ),
                ),
            ),
        );

        $query = new WP_Query($args);
        if ($query->found_posts > 0) {
            $output = $output . '<span class="has_new"><span>' . __('NEW', 'zoa') . '</span></span>';
        }
    }
    return $output;
}

add_filter('aioseop_title', 'wporg_only_title_home_page', 10, 1);
function wporg_only_title_home_page($title) {
    if (is_category('blog')) {
        $title = 'STORIES | Chiyono Anne';
    }

    return $title;
}

add_filter('the_password_form', 'pipdig_cppm_filter_text', 999);
function pipdig_cppm_filter_text($output) {

    $value = '閲覧するには以下にパスワードを入力してください。';

    if (!empty($value)) {

        // Divi
        $output = str_replace(__('To view this protected post, enter the password below:', 'Divi'), $value, $output); // older
        $output = str_replace(__('To view this protected post, enter the password below', 'Divi'), $value, $output); // newer
        // Shapley
        $output = str_replace(__('This post is password protected. To view it please enter your password below:', 'shapely'), $value, $output);

        // Werkstatt
        $output = str_replace(__('This is a protected area. Please enter your password:', 'werkstatt'), $value, $output);

        // Standard
        $output = str_replace(__('This content is password protected. To view it please enter your password below:', 'zoa'), $value, $output);
        $output = str_replace(__('このコンテンツはパスワードで保護されています。閲覧するには以下にパスワードを入力してください。', 'zoa'), $value, $output);
    }
    return $output;
}

// Adding Tag Options product admin pages
add_action('admin_enqueue_scripts', 'colorpk_theme_load_scripts');
function colorpk_theme_load_scripts() {
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
}

add_action('save_post', 'ch_save_tag_setting', 10, 1);
if (!function_exists('ch_save_tag_setting')) {

    function ch_save_tag_setting($post_id) {
        update_post_meta($post_id, 'show_tag_front', $_POST['show_tag_front']);
        update_post_meta($post_id, 'bg_color_tag', $_POST['bg_color_tag']);
    }

}

add_action('add_meta_boxes', 'ch_mtb_shipping_fee_table');
if (!function_exists('ch_mtb_shipping_fee_table')) {

    function ch_mtb_shipping_fee_table() {
        add_meta_box('ch_mtb_shipping_fee_table', __('Shipping Fee Table', 'zoa'), 'ch_mtb_shipping_fee_table_callback', 'shop_order', 'side', 'core');
    }

}

function ch_mtb_shipping_fee_table_callback() {
    ob_start();
    ?>
    <table class="table table__simple">
        <caption>送料一覧</caption>
        <thead>
            <tr>
                <th>地域</th>
                <th>BOX 60</th>
                <th>BOX 80</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">東京都</th>
                <td>800</td>
                <td>1010</td>
            </tr>
            <tr>
                <th scope="row">北海道</th>
                <td>1280</td>
                <td>1500</td>
            </tr>
            <tr>
                <th scope="row">滋賀県, 京都府, 大阪府, 兵庫県, 奈良県, 和歌山県</th>
                <td>950</td>
                <td>1180</td>
            </tr>
            <tr>
                <th scope="row">鳥取県, 島根県, 岡山県, 広島県, 山口県, 徳島県, 香川県, 愛媛県, 高知県</th>
                <td>1080</td>
                <td>1290</td>
            </tr>
            <tr>
                <th scope="row">福岡県, 佐賀県, 長崎県, 熊本県, 大分県, 宮崎県, 鹿児島県</th>
                <td>1280</td>
                <td>1500</td>
            </tr>
            <tr>
                <th scope="row">沖縄</th>
                <td>1330</td>
                <td>1600</td>
            </tr>
            <tr>
                <th scope="row">その他</th>
                <td>850</td>
                <td>1080</td>
            </tr>
        </tbody>
    </table>
    <p>*その他は青森県, 岩手県, 宮城県, 秋田県, 山形県, 福島県, 茨城県, 栃木県, 群馬県, 埼玉県,千葉県,神奈川県,新潟県,富山県,石川県,福井県,山梨県,長野県,岐阜県,静岡県,愛知県,三重県</p>
    <p>*レターパックは全て370円</p>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('save_post', 'ch_save_did_you_find', 1000, 1);
if (!function_exists('ch_save_did_you_find')) {

    function ch_save_did_you_find($post_id) {
        if (isset($_REQUEST['did_you_find'])) {
            update_post_meta($post_id, 'did_you_find', implode(", ", $_REQUEST['did_you_find']));
        }
    }

}

add_action('add_meta_boxes', 'ch_mtb_did_you_find');
if (!function_exists('ch_mtb_did_you_find')) {

    function ch_mtb_did_you_find() {
        add_meta_box('ch_mtb_did_you_find', __('当ブランドをどこでお知りになりましたか?', 'zoa'), 'ch_mtb_did_you_find_callback', 'shop_order', 'side', 'core');
    }

}

function ch_mtb_did_you_find_callback() {
    global $post;
    echo get_post_meta($post->ID, 'did_you_find', true);
}

add_filter( 'gettext', 'change_mwb_wgm_to_name_optional_translate', 20, 3 );
function change_mwb_wgm_to_name_optional_translate( $text ) {
	$text = str_ireplace( 'Enter the Recipient Name ',  '受取人のお名前を入力（任意）',  $text );
        if ($text=='Giftcard Value: '||$text=='Gift Card Value: '){
            $text='Voucher Value: ';
        }
    return $text;
}


function mwb_wgm_price_meta_data_remove_message($item_meta, $the_cart_data, $product_id, $variation_id){
    if (isset($item_meta['mwb_wgm_message'])){
        unset($item_meta['mwb_wgm_message']);
    }
    return $item_meta;
}
add_filter( 'mwb_wgm_price_meta_data', 'mwb_wgm_price_meta_data_remove_message', 99,4 );

//custom query to get child products when filter on custom taxonomy
function ch_yith_wcbep_product_list_query_args($query_args) {

    if (isset($query_args['tax_query'])) {
        foreach ($query_args['tax_query'] as $key => $value) {
            if (isset($value['taxonomy']) && $value['taxonomy'] == 'series') {//only for series
                $ids = array();
                if (isset($value['terms']) && !empty($value['terms'])) {
                    //get parent products
                    $args_parent = array(
                        'post_type' => 'product',
                        'tax_query' => array(
                            array(
                                'taxonomy' => $value['taxonomy'],
                                'field' => 'term_id',
                                'terms' => $value['terms'],
                                'operator' => 'IN',
                            ),
                        ),
                        'fields' => 'ids',
                        'posts_per_page' => -1,
                    );
                    $query_products = new WP_Query($args_parent);
                    $parent_product = array();
                    if ($query_products->have_posts()) {
                        while ($query_products->have_posts()) {
                            $query_products->the_post();
                            $parent_product[] = get_the_ID();
                        }
                        wp_reset_postdata();
                    }
                    //get child products
                    $visible_only_args = array(
                        'post_type' => 'product_variation',
                        'fields' => 'ids',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'post_parent__in' => $parent_product
                    );
                    $the_query = new WP_Query($visible_only_args);
                    $ids = $parent_product;
                    if ($the_query->have_posts()) {
                        while ($the_query->have_posts()) {
                            $the_query->the_post();
                            $ids[] = get_the_ID();
                        }
                        wp_reset_postdata();
                    }
                    if ($value['operator'] == 'IN') {
                        $query_args['post__in'] = $ids;
                    } else {
                        $query_args['post__not_in '] = $ids;
                    }

                    //remove filter by parent product
                    unset($query_args['tax_query'][$key]);
                }
            }
        }
    }
    
    return $query_args;
}

add_filter('yith_wcbep_product_list_query_args', 'ch_yith_wcbep_product_list_query_args', 99, 2);

add_filter('fgf_is_valid_notice','ch_fgf_is_valid_notice');
function ch_fgf_is_valid_notice($return){
    $fgf_gift_product=array();
    $fgf_gift_product_double=array();
    //get gift free doubble
    //get qty of Silk Soft Bra && Silk Thong && Silk High Leg
    $qty_silk_soft_bra=0;
    $qty_silk_thong=0;
    $qty_silk_high_leg=0;
    
    $qty_free_gift=0;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id']==9136){//Silk Soft Bra
            $qty_silk_soft_bra+=$cart_item['quantity'];
        }elseif ($cart_item['product_id']==2025417){//Silk High Leg
            $qty_silk_high_leg+=$cart_item['quantity'];
        }elseif ($cart_item['product_id']==36968){//Silk Thong
            $qty_silk_thong+=$cart_item['quantity'];
        }
        if (isset($cart_item['fgf_gift_product'])&&!empty($cart_item['fgf_gift_product'])){
            $qty_free_gift+=$cart_item['quantity'];
            if (in_array($cart_item['fgf_gift_product']['product_id'], $fgf_gift_product)){
                $fgf_gift_product_double[]=$cart_item['fgf_gift_product']['product_id'];
            }else{
                $fgf_gift_product[]=$cart_item['fgf_gift_product']['product_id'];
            }
        }
    }
    if (!empty($fgf_gift_product_double)){
        $fgf_gift_product_double= array_unique($fgf_gift_product_double);
    }
    //remove gift free product if double
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['fgf_gift_product'])&&!empty($cart_item['fgf_gift_product'])){
            if (in_array($cart_item['fgf_gift_product']['product_id'], $fgf_gift_product_double)){
                 WC()->cart->remove_cart_item( $cart_item_key );
                if (($key = array_search($cart_item['fgf_gift_product']['product_id'], $fgf_gift_product_double)) !== false) {
                        unset($fgf_gift_product_double[$key]);
                    }
            }
            //remove gift for rank in guest
            $rule_id=$cart_item['fgf_gift_product']['rule_id'];
            $rule = fgf_get_rule($rule_id);
            if (!is_user_logged_in()&&($rule->get_name() == 'bronze'||$rule->get_name() == 'silver'||$rule->get_name() == 'gold'||$rule->get_name() == 'royal')){
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }
    }
    //hide notice for guest only rank free gift
    $manual_gift_products=FGF_Rule_Handler::get_manual_gift_products();
    $pass=false;
    foreach ($manual_gift_products as $key => $mn_gift_product) {
        $rule_id=$mn_gift_product['rule_id'];
        $rule = fgf_get_rule($rule_id);
        if (!is_user_logged_in()&&($rule->get_name() == 'bronze'||$rule->get_name() == 'silver'||$rule->get_name() == 'gold'||$rule->get_name() == 'royal')){
            $pass=true;
            break;
        }
    }
    if (!is_user_logged_in()&&$pass==true){
        return false;
    }
    //hide notice if exist free gift in cart
    if ($qty_silk_soft_bra>=2&&$qty_silk_high_leg>=2&&$qty_silk_thong>=2&&$qty_free_gift!=$qty_silk_thong){
        $return;
    } else {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item['fgf_gift_product'])&&!empty($cart_item['fgf_gift_product'])){//this mean if exist free gift will don't show other rule too.
                return false;
            }
        }
    }
    return $return;
}

add_action('acf/init', 'my_acf_op_init');
function my_acf_op_init() {

    // Check function exists.
    if ( function_exists('acf_add_options_page') ) {

        // Register options page.
        $option_page = acf_add_options_page(array(
            'page_title'    => __('Common settings'),
            'menu_title'    => __('Common settings'),
            'menu_slug'     => 'common-general-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));
    }
}

add_action('template_redirect', 'temp_maintenance');
function temp_maintenance(){
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    $maintenance = function_exists('get_field') ? get_field('maintenance', 'option') : '';
    $from = function_exists('get_field') ? get_field('maintenance_from_date','option') : '';
    $to = function_exists('get_field') ? get_field('maintenance_to_date','option') : '';
    $current_time = current_time('timestamp');
    
    if ( 'administrator' != $user_role && 'yes' == $maintenance && $current_time < strtotime($to) && $current_time > strtotime($from)) {
        wp_redirect(home_url('maintenance'));
        exit();
    }
}

add_action( 'wp_footer', 'auto_fill_contact_form' );
function auto_fill_contact_form() {
    if ( is_user_logged_in() && is_page('contact') ) {
        $user_id=get_current_user_id();
        $current_user = wp_get_current_user();
        $name=get_user_meta($user_id, 'last_name', true).' '.get_user_meta($user_id, 'first_name', true);
        $kana=get_user_meta($user_id, 'billing_last_name_kana', true).' '.get_user_meta($user_id, 'billing_first_name_kana', true);
        $email=get_user_meta($user_id, 'billing_email', true);
        if (empty($email)){
           $email = $current_user->user_email;
        }
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $('#wpforms-2027257-field_0').val('<?php echo $name; ?>');
                $('#wpforms-2027257-field_3').val('<?php echo $kana; ?>');
                $('#wpforms-2027257-field_1').val('<?php echo $email; ?>');
            });
        </script>
        <?php
    }
}

add_action('admin_head', 'my_custom_revenue_page_css');
function my_custom_revenue_page_css() {
    ob_start();
?>
        <style>
            .woocommerce_page_wc-admin .DayPicker_weekHeader_ul {
                display: flex;
            }
            .woocommerce_page_wc-admin .DayPicker_focusRegion svg{
                width: 20px;
            }
            .woocommerce_page_wc-admin .DayPickerNavigation {
                display: flex;
                justify-content: space-around;
            }
        </style>
        <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}

add_action('woocommerce_account_content', 'account_page_modal');
function account_page_modal() {
        $posts = get_page_by_path(basename('modal-my-account'), OBJECT, 'post');
        if (!empty($posts)&&$posts->post_status=='publish') {
            ?>
            <div class="remodal remodal_hbody" data-remodal-id="event_modal_myaccount" id="event_modal_myaccount" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
                <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
                <div class="remodal_wraper">
                    <div class="modal_image">
                        <img class="img" src="<?php echo wp_get_attachment_url(get_post_thumbnail_id($posts->ID)); ?>" />
                    </div>
                    <div class="modal_head">
                        <?php echo $posts->post_title; ?>
                    </div>
                    <div class="modal_body">
                        <?php
                        $content = apply_filters('the_content', get_the_content(null, false, $posts->ID));
                        echo $content;
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
}

add_action( 'woocommerce_after_mini_cart', 'ch_force_recalculate_wc_totals' );
function ch_force_recalculate_wc_totals() {
    // Calculate totals
    WC()->cart->calculate_totals();
    // Save cart to session
    WC()->cart->set_session();
    // Maybe set cart cookies
    WC()->cart->maybe_set_cart_cookies();
}

// add_filter('woocommerce_coupon_is_valid_for_product', 'exclude_product_from_coupon_by_tag', 9999, 4);
// function exclude_product_from_coupon_by_tag($valid, $product, $coupon, $values ){
//    //Check if product has tag/s
//    $has_term = has_term('sale', 'product_tag', $product->get_id());
//    if ($has_term){
//        $valid=false;
//    }
//    return $valid;
// }

add_action('init', 'ch_set_billing_address_same_as_shipping');
function ch_set_billing_address_same_as_shipping() {
    // Ensure WooCommerce is active before running the function
    if (class_exists('WooCommerce')) {

        // チェックボックスを最初からチェックされた状態にする
        add_filter('woocommerce_ship_to_different_address_checked', '__return_true');

        // Billing DetailsをShipping Detailsと同じにする
        add_filter('woocommerce_checkout_get_value', 'ch_copy_shipping_to_billing', 10, 2);

        // JavaScriptを追加して、動的な更新を行う
        if (function_exists('is_checkout') && is_checkout()) {
            add_action('wp_footer', 'ch_copy_shipping_to_billing_js');
        }
    }
}

function ch_copy_shipping_to_billing($value, $input) {
    if (strpos($input, 'billing_') === 0) {
        $shipping_field = str_replace('billing_', 'shipping_', $input);
        $shipping_value = WC()->checkout->get_value($shipping_field);
        if ($shipping_value) {
            return $shipping_value;
        }
    }
    return $value;
}

function ch_copy_shipping_to_billing_js() {
    ?>
    <script type="text/javascript">
    jQuery(function($){
        $(document).ready(function(){
            $('[name^="shipping_"]').on('change', function() {
                var shipping_field = $(this).attr('name');
                var billing_field = shipping_field.replace('shipping_', 'billing_');
                $('[name="' + billing_field + '"]').val($(this).val()).trigger('change');
            });
        });
    });
    </script>
    <?php
}

add_action('wp_footer', 'add_tbyb_modal', 100);
function add_tbyb_modal() {
    if (is_page('guide-tbyb') && get_locale() === 'en_US') {
        ?>
        <div class="remodal remodal_base" data-remodal-id="new_modal" id="new_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
            <button data-remodal-action="close" class="remodal-close remodal-close_small" aria-label="Close"></button>
            <div class="remodal_wraper">
                <div class="pop-up">
                    <div class="pop-head">
                        <h2 class="pop-title modal_title"><?php esc_html_e('Notice', 'zoa'); ?></h2>
                    </div>
                    <div class="pop-content">
                        <p><?php esc_html_e('This Service is available only in Japan.', 'zoa'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } elseif (is_product() && get_locale() === 'en_US') {
        global $product;
        if ($product && $product->get_sku() === 'SPSV-01') {
            ?>
            <div class="remodal remodal_base" data-remodal-id="product_specific_modal" id="product_specific_modal" role="dialog" data-remodal-options="hashTracking: false, closeOnOutsideClick: true">
                <button data-remodal-action="close" class="remodal-close remodal-close_small" aria-label="Close"></button>
                <div class="remodal_wraper">
                    <div class="pop-up">
                        <div class="pop-head">
                            <h2 class="pop-title modal_title"><?php esc_html_e('Important Notice: Domestic Orders Only', 'zoa'); ?></h2>
                        </div>
                        <div class="pop-content">
                            <p><?php esc_html_e('This product/service is available for purchase only within Japan due to return shipping requirements.', 'zoa'); ?></p>
                            <p><?php esc_html_e('We regret that we cannot accept orders from outside Japan at this time.', 'zoa'); ?></p>
                            <p><?php esc_html_e('For any inquiries, please contact our customer service.', 'zoa'); ?></p>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
    }
}
