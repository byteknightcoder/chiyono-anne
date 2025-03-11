<?php

/*DISABLE ALL STYLESHEETS*/
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


/*ICON HEADER EMENU*/
if ( ! function_exists( 'zoa_wc_header_action' ) ) :
	function zoa_wc_header_action() {
		global $woocommerce;
		$page_account = get_option( 'woocommerce_myaccount_page_id' );
		$page_logout  = wp_logout_url( get_permalink( $page_account ) );

		if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
			$logout_url = str_replace( 'http:', 'https:', $logout_url );
		}

		$count = $woocommerce->cart->cart_contents_count;
		?>
		<div class="menu-woo-action">
			<a href="<?php echo get_permalink( $page_account ); ?>" class="zoa-icon-user menu-woo-user"></a>
			<ul>
				<?php if ( ! is_user_logged_in() ) : ?>
					<li><a href="<?php echo get_permalink( $page_account ); ?>"
						   class="text-center"><?php esc_html_e( 'Login / Register', 'zoa' ); ?></a></li>
				<?php else : ?>
					<li>
						<a href="<?php echo get_permalink( $page_account ); ?>"><?php esc_html_e( 'Dashboard', 'zoa' ); ?></a>
					</li>
					<li><a href="<?php echo esc_url( $page_logout ); ?>"><?php esc_html_e( 'Logout', 'zoa' ); ?></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<a href="<?php echo wc_get_cart_url(); ?>" id="shopping-cart-btn" class="zoa-icon-cart menu-woo-cart js-cart-button"><span
				class="shop-cart-count"><?php echo esc_html( $count ); ?></span></a>
		<?php
	}
endif;

/*REMOVE BREADCRUMBS*/
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

/*CONTENT WRAPPER*/
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10, 0 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10, 0 );

add_action( 'woocommerce_before_main_content', 'zoa_shop_open_tag', 5 );
function zoa_shop_open_tag() {
	$shop_sidebar = ! is_active_sidebar( 'shop-widget' ) ? 'full' : get_theme_mod( 'shop_sidebar', 'full' );
	$shop_class   = '';

	$shop_class .= is_product() ? 'with-full-sidebar' : 'with-' . $shop_sidebar . '-sidebar';
	if ( get_theme_mod( 'flexible_sidebar' ) ) {
		$shop_class .= ' has-flexible-sidebar';
	}
	?>
	<div class="shop-container container <?php echo esc_attr( $shop_class ); ?>">
	<div class="shop-content">
	<?php
	if ( get_theme_mod( 'flexible_sidebar' ) && 'full' !== $shop_sidebar && ! is_product() ) :
	?>
		<div class="sidebar-overlay"></div>
		<a href="#" class="sidebar-toggle js-sidebar-toggle">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle Shop Sidebar', 'zoa' ); ?></span>
			<i class="ion-android-options toggle-icon"></i>
		</a>
	<?php
	endif;
}

add_action( 'woocommerce_after_main_content', 'zoa_shop_close_tag', 50 );
function zoa_shop_close_tag() {
	?>
	</div>
	<?php
	if ( ! is_singular( 'product' ) ) :
		do_action( 'woocommerce_sidebar' );
	endif;
	?>
	</div>
	<?php
}

/*REMOVE SHOP TITLE*/
add_filter( 'woocommerce_show_page_title', 'zoa_remove_shop_title' );
function zoa_remove_shop_title() {
	return false;
}

/*TOTAL CART ITEM - AJAX UPDATE*/
add_filter( 'woocommerce_add_to_cart_fragments', 'zoa_cart_item' );
function zoa_cart_item( $fragments ) {
	global $woocommerce;
	$total = $woocommerce->cart->cart_contents_count;

	ob_start();
	?>
	<span class="shop-cart-count"><?php echo esc_attr( $total ); ?></span>
	<?php

	$fragments['span.shop-cart-count'] = ob_get_clean();

	return $fragments;
}

/*CART LIST ITEM - AJAX UPDATE*/
add_filter( 'woocommerce_add_to_cart_fragments', 'zoa_cart_list' );
function zoa_cart_list( $fragments ) {
	global $woocommerce;
	$total_item = $woocommerce->cart->cart_contents_count;

	ob_start();
	?>
	<div class="cart-sidebar-content">
		<?php woocommerce_mini_cart(); ?>
	</div>
	<?php

	$fragments['div.cart-sidebar-content'] = ob_get_clean();

	return $fragments;
}

/*MODIFY SEARCH WIDGET*/
add_filter( 'get_product_search_form', 'zoa_product_search_form_widget' );
function zoa_product_search_form_widget( $form ) {
	$form = '<form role="search" method="get" class="search-form" action="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" >';
	$form .= '<label class="screen-reader-text">' . esc_html__( 'Search for:', 'zoa' ) . '</label>';
	$form .= '<input type="text" class="search-field" placeholder="' . esc_attr__( 'Search....', 'zoa' ) . '" value="' . get_search_query() . '" name="s" required/>';
	$form .= '<button type="submit" class="search-submit zoa-icon-search"></button>';
	$form .= '</form>';

	return $form;
}

/*ADD CUSTOM CLASS IN SINGLE PRODUCT*/
add_filter( 'post_class', 'zoa_single_product_cls', 10, 3 );
function zoa_single_product_cls( $classes, $class, $post_id ) {
	if ( is_singular( 'product' ) ) {
		global $product;
		$gallery_id = $product->get_gallery_image_ids();

		if ( ! empty( $gallery_id ) ) {
			$classes[] = 'this-product-has-gallery-image';
		}
	}

	return $classes;
}

/*CHECK PRODUCT ALREADY IN CART*/
if ( ! function_exists( 'zoa_product_check_in' ) ) :
	function zoa_product_check_in( $pid = null, $in_cart = true, $qty_in_cart = false ) {
		global $woocommerce;
		$_cart    = $woocommerce->cart->get_cart();
		$_product = wc_get_product( $pid );
		$variable = $_product->is_type( 'variable' );

		if ( true == $in_cart ) {
			foreach ( $_cart as $key ) {
				$product_id = $key['product_id'];

				if ( $product_id == $pid ) {
					return true;
				}
			}

			return false;
		}

		if ( true == $qty_in_cart ) {
			if ( $variable ) {
				$arr = array();
				foreach ( $_cart as $key ) {
					if ( $key['product_id'] == $pid ) {
						$qty   = $key['quantity'];
						$arr[] = $qty;
					}
				}

				return array_sum( $arr );
			} else {
				foreach ( $_cart as $key ) {
					if ( $key['product_id'] == $pid ) {
						$qty = $key['quantity'];

						return $qty;
					}
				}
			}

			return 0;
		}
	}
endif;

/*PRODUCT ACTION*/
if ( ! function_exists( 'zoa_product_action' ) ) :
	function zoa_product_action() {
		global $woocommerce;
		$total = $woocommerce->cart->cart_contents_count;
		?>
		<div id="shop-quick-view" data-view_id='0'>
			<button class="quick-view-close-btn ion-ios-close-empty"></button>
			<div class="quick-view-content"></div>
		</div>

		<div id="shop-cart-sidebar">
			<div class="cart-sidebar-head">
				<h4 class="cart-sidebar-title"><?php esc_html_e( 'Shopping cart', 'zoa' ); ?></h4>
				<span class="shop-cart-count"><?php echo esc_attr( $total ); ?></span>
				<button id="close-cart-sidebar" class="ion-android-close"></button>
			</div>
			<div class="cart-sidebar-content">
				<?php woocommerce_mini_cart(); ?>
			</div>
		</div>

		<div id="shop-overlay"></div>
		<?php
	}
endif;

/*PRODUCT LABEL*/
if ( ! function_exists( 'zoa_product_label' ) ) {

	/**
	 * Display product label
	 *
	 * @param      $product  The product
	 *
	 * @return     $label markup
	 */
	function zoa_product_label( $product ) {
		if ( ! $product ) {
			return;
		}

		$label = '';

		// product option
		if ( function_exists( 'FW' ) ) {
			$pid         = $product->get_id();
			$label_txt   = fw_get_db_post_option( $pid, 'label_txt', '' );
			$label_color = fw_get_db_post_option( $pid, 'label_color', '#fff' );
			$label_bg    = fw_get_db_post_option( $pid, 'label_bg', '#f00' );

			if ( ! empty( $label_txt ) ) {
				$style = array(
					'color'            => 'color: ' . esc_attr( $label_color ),
					'background-color' => 'background-color: ' . esc_attr( $label_bg ),
				);

				$label = '<span class="zoa-product-label" style="' . implode( '; ', $style ) . '">' . esc_html( $label_txt ) . '</span>';
			}
		}

		// out of stock label
		if ( ! $product->is_in_stock() ) {
			$label = '<span class="zoa-product-label sold-out-label">' . esc_html__( 'Sold out', 'zoa' ) . '</span>';
		}

		return $label;
	}
}
