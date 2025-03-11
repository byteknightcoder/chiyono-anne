<?php
// @codingStandardsIgnoreStart
defined( 'ABSPATH' ) || exit;


/* THEME SETUP
***************************************************/
add_action( 'after_setup_theme', 'zoa_action_theme_setup' );
if ( ! function_exists( 'zoa_action_theme_setup' ) ):
	function zoa_action_theme_setup() {
		load_theme_textdomain( 'zoa', get_template_directory() . '/languages' );

		add_theme_support( 'woocommerce' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo', array(
			'height'      => 64,
			'width'       => 20,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		add_theme_support( 'post-formats', array(
			'aside',
			'image',
			'video',
			'audio',
			'quote',
			'link',
			'gallery',
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		add_filter( 'use_default_gallery_style', '__return_false' );
	}
endif;


/* BODY CLASSES
***************************************************/
add_filter( 'body_class', 'zoa_body_classes' );
function zoa_body_classes( $classes ) {
	/*! PRELOADER
	------------------------------------------------->*/
	if ( true === get_theme_mod( 'loading', false ) ) {
		$classes[] = 'is-page-loading';
	}

	if ( true === get_theme_mod( 'sticky_add_to_cart_and_checkout', false ) ) {
	    $classes[] = 'shop-buttons-is-sticky';
    }

	/*! BROWSER DETECT
	------------------------------------------------->*/
	global $is_IE, $is_edge, $is_safari, $is_iphone;

	if ( $is_iphone ) {
		$classes[] = 'iphone-detected';
	} elseif ( $is_IE ) {
		$classes[] = 'ie-detected';
	} elseif ( $is_edge ) {
		$classes[] = 'edge-detected';
	} elseif ( $is_safari ) {
		$classes[] = 'safari-detected';
	}

	/* THEME START
	------------------------------------------------->*/
	if ( ! class_exists( 'kirki' ) ) {
		$classes[] = 'no-kirki-customize';
	}

	/*! BLOG CLASS
	------------------------------------------------->*/
	if ( zoa_blog() ) {
		$classes[] = 'group-blog';

		$sidebar   = is_active_sidebar( 'blog-widget' ) ? get_theme_mod( 'blog_sidebar', 'full' ) : 'full';
		if ( 'full' != $sidebar ) {
			$classes[] = 'group-blog-sidebar-' . $sidebar;
		}
	}

	/*! SINGLE PRODUCT GALLERY
	------------------------------------------------->*/
	if ( is_singular( 'product' ) ) {
		$layout = get_theme_mod( 'shop_gallery_layout', 'vertical' );

		if ( function_exists( 'FW' ) ) {
			$page_id  = get_queried_object_id();
			$p_layout = fw_get_db_post_option( $page_id, 'layout' );

			if ( isset( $p_layout ) && 'default' !== $p_layout ) {
				$layout = $p_layout;
			}
		}

		if ( 'vertical' === $layout || 'horizontal' === $layout ) {
			$classes[] = 'single-gallery-slider single-gallery-' . $layout;
		} else {
			$classes[] = 'single-gallery-image single-gallery-' . $layout;
		}
	}

	/*! PAGE HEADER
	------------------------------------------------->*/
	$classes[] = 'is-page-header-' . zoa_page_header_slug();

	/*OPTION ENABLE AJAX SINGLE ADD TO CART*/
	if ( true === get_theme_mod( 'ajax_single_atc', true ) ) {
		$classes[] = 'ajax-single-add-to-cart';
	}

	// Theme version.
	$theme     = wp_get_theme();
	$classes[] = 'zoa-' . $theme->get( 'Version' );

	// Mobile sticky header menu.
	if ( true == get_theme_mod( 'sticky_header', false ) && true == get_theme_mod( 'mobile_sticky_header', false ) ) {
		$classes[] = 'mobile-header-menu-sticky';
	}

	// Menu layout.
	$classes[] = 'has-menu-' . zoa_menu_slug();

	return $classes;
}


/* REGISTER WIDGET AREA
***************************************************/
add_action( 'widgets_init', 'zoa_widgets_init' );
function zoa_widgets_init() {
	/*! BLOG WIDGET
	------------------------------------------------->*/
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Widget Area', 'zoa' ),
		'id'            => 'blog-widget',
		'description'   => esc_html__( 'Appears in the blog sidebar section of the site.', 'zoa' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>',
	) );

	/*! FOOTER WIDGET
	------------------------------------------------->*/
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'zoa' ),
		'id'            => 'footer-widget',
		'description'   => esc_html__( 'Appears in the footer section of the site.', 'zoa' ),
		'before_widget' => '<aside id="%1$s" class="widget footer-widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>',
	) );

	/*! WOOCOMMERCE WIDGET
	------------------------------------------------->*/
	if ( class_exists( 'woocommerce' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Shop Widget Area', 'zoa' ),
			'id'            => 'shop-widget',
			'description'   => esc_html__( 'Appears in the sidebar of shop page.', 'zoa' ),
			'before_widget' => '<aside id="%1$s" class="widget shop %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>',
		) );
	}
	/*! SUBSCRIBTION WIDGET
	------------------------------------------------->*/
	register_sidebar( array(
		'name'          => esc_html__( 'Subcription Widget Area', 'zoa' ),
		'id'            => 'subscription-widget',
		'description'   => esc_html__( 'Appears in subscription section of 404 page.', 'zoa' ),
		'before_widget' => '<div id="%1$s" class="widget subscription-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	) );
}


/* KIRKI: COMPILES INLINE CSS TO THEME STYLESHEET
***************************************************/
add_filter( 'kirki_zoa_stylesheet', 'zoa_stylesheet_filter', 99 );
function zoa_stylesheet_filter( $stylesheet ) {
	return 'zoa-theme-style';
}


/* INSTALL DEMO CONTENT
***************************************************/
add_filter( 'fw:ext:backups-demo:demos', 'zoa_backups_demos' );
function zoa_backups_demos( $demos ) {
	$demos_array = array(
		'zoa' => array(
			'title'        => esc_html__( 'Zoa Demo Full', 'zoa' ),
			'screenshot'   => get_template_directory_uri() . '/screenshot.png',
			'preview_link' => '//haintheme.com/demo/wp/zoa/',
		),
	);

	$download_url = 'http://haintheme.com/ht-demos/';

	foreach ( $demos_array as $id => $data ) {
		$demo = new FW_Ext_Backups_Demo( $id, 'piecemeal', array(
			'url'     => $download_url,
			'file_id' => $id,
		) );
		$demo->set_title( $data['title'] );
		$demo->set_screenshot( $data['screenshot'] );
		$demo->set_preview_link( $data['preview_link'] );

		$demos[ $demo->get_id() ] = $demo;

		unset( $demo );
	}

	return $demos;
}


/* ADD A CUSTOM COLUMN IN POSTS AND CUSTOM POST TYPES ADMIN SCREEN
***************************************************/

/*GET FEATURED IMAGE*/
function zoa_get_featured_image( $post_ID ) {
	$img_id = get_post_thumbnail_id( $post_ID );
	if ( $img_id ) {
		$img_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );

		return $img_src[0];
	}
}

/*ADD NEW COLUMN | `post`*/
add_filter( 'manage_post_posts_columns', 'zoa_columns_head', 10 );
function zoa_columns_head( $defaults ) {
	$order = array();
	/* `cb` = checkbox*/
	$checkbox = 'cb';
	foreach ( $defaults as $key => $value ) {
		$order[ $key ] = $value;
		if ( $key === $checkbox ) {
			$order['thumbnail_image'] = esc_attr__( 'Image', 'zoa' );
		}
	}

	return $order;
}

/*SHOW THE FEATURED IMAGE | `post` */
add_action( 'manage_post_posts_custom_column', 'zoa_columns_content', 10, 2 );
function zoa_columns_content( $column_name, $post_ID ) {
	if ( 'thumbnail_image' === $column_name ) {
		$_img_src = zoa_get_featured_image( $post_ID );
		if ( $_img_src ) {
			?>
			<a href="<?php echo get_edit_post_link( $post_ID ); ?>">
				<img src="<?php echo esc_url( $_img_src ); ?>"/>
			</a>
		<?php } else { ?>
			<a href="<?php echo get_edit_post_link( $post_ID ); ?>">
				<img src="<?php echo get_template_directory_uri() . '/images/thumbnail-default.jpg'; ?>"/>
			</a>
			<?php
		}
	}
}

/**
 * Open content container
 */
if ( ! function_exists( 'zoa_before_content' ) ) {
	function zoa_before_content() {
		do_action( 'zoa_before_content' );
	}
}

if ( ! function_exists( 'zoa_open_content_container' ) ) {
	function zoa_open_content_container() {
		if ( ! function_exists( 'hfe_render_header' ) ) :
			$menu_layout            = zoa_menu_slug();
			$sidebar_menu           = 'sidebar-menu';
			$sidebar_menu_links     = get_theme_mod( 'sidebar_menu_links', '<ul class="sidebar-menu-links">
	<li><a href="#">About us</a></li>
	<li><a href="#">Order & shipping</a></li>
	<li><a href="#">FAQs</a></li>
	</ul>' );
			$sidebar_menu_social    = get_theme_mod( 'sidebar_menu_social', '<ul class="menu-social sidebar-menu-social">
	<li><a href="//facebook.com/zoa"></a></li>
	<li><a href="//twitter.com/zoa"></a></li>
	<li><a href="//instagram.com/zoa"></a></li>
	</ul>' );
			$sidebar_menu_copyright = get_theme_mod( 'sidebar_menu_copyright', '<div class="sidebar-menu-copyright">&copy; 2018 <a href="#"><strong>Zoa</strong></a>. All Rights Reserved.</div>' );

			if ( 'layout-5' === $menu_layout ) {
				$sidebar_menu = $sidebar_menu . ' sidebar-menu--md-visible';
			}
			?>

			<div id="sidebar-menu-content" class="menu-layout menu-<?php echo esc_attr( $menu_layout ); ?> <?php echo esc_attr( $sidebar_menu ); ?>">
				<div class="sidebar-menu-top">
					<?php
					if ( 'layout-5' === $menu_layout ) :
						zoa_logo_image();
						?>

						<div class="header-action">
							<button id="theme-search-btn" class="zoa-icon-search js-search-button"></button>
							<?php
							if ( class_exists( 'woocommerce' ) ) {
								zoa_wc_header_action();
							}
							?>
						</div><!-- .header-action -->

					<?php
					else :
						get_search_form();
					endif;
					?>

				</div><!-- .sidebar-menu-top -->

				<div class="sidebar-menu-middle">
					<?php
					if ( 'layout-5' === $menu_layout ) {
						if ( has_nav_menu( 'quaternary' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'quaternary',
								'menu_class'     => 'theme-primary-menu theme-sidebar-menu',
								'container'      => '',
							) );
						}
					} else {

						if ( has_nav_menu( 'primary' ) ) :
							wp_nav_menu( array(
								'theme_location' => 'primary',
								'menu_class'     => 'theme-primary-menu theme-sidebar-menu',
								'container'      => '',
							) );
						else :
							?>
							<a class="add-menu" href="<?php echo esc_url( get_admin_url() . 'nav-menus.php' ); ?>"
							>
								<?php esc_html_e( 'Add Menu', 'zoa' ); ?>
							</a>
						<?php
						endif;

						if ( 'layout-6' == $menu_layout ) {
							if ( has_nav_menu( 'tertiary' ) ) {
								wp_nav_menu( array(
									'theme_location' => 'tertiary',
									'menu_class'     => 'theme-primary-menu theme-sidebar-menu layout-6-sidebar-menu',
									'container'      => '',
								) );
							}
						}
					}
					?>
				</div><!-- .sidebar-menu-middle -->

				<div class="sidebar-menu-bottom">
					<?php
					if ( 'layout-5' !== $menu_layout ) {
						zoa_wc_sidebar_action();
					}

					if ( ! empty( $sidebar_menu_links ) && 'layout-5' === $menu_layout ) {
						echo wp_kses_decode_entities( $sidebar_menu_links );
					}

					if ( ! empty( $sidebar_menu_social ) && 'layout-5' === $menu_layout ) {
						echo wp_kses_decode_entities( $sidebar_menu_social );
					}

					if ( ! empty( $sidebar_menu_copyright ) && 'layout-5' === $menu_layout ) {
						echo wp_kses_decode_entities( $sidebar_menu_copyright );
					}
					?>
				</div><!-- .sidebar-menu-bottom -->
			</div><!-- #sidebar-menu-content -->

		<?php
		endif;
		$menu_layout = zoa_menu_slug();
		if ( 'layout-5' === $menu_layout ) {
			echo '<div class="content-container"><div id="theme-menu-pusher">';
		} else {
			echo '<div id="theme-menu-pusher">';
		}
	}

	add_action( 'zoa_before_content', 'zoa_open_content_container' );
}

/**
 * Close content container
 */
if ( ! function_exists( 'zoa_after_content' ) ) {
	function zoa_after_content() {
		do_action( 'zoa_after_content' );
	}
}

if ( ! function_exists( 'zoa_close_content_container' ) ) {
	function zoa_close_content_container() {
		$menu_layout = zoa_menu_slug();

		if ( 'layout-5' === $menu_layout ) {
			echo '</div></div>';
		} else {
			echo '</div>';
		}
	}

	add_action( 'zoa_after_content', 'zoa_close_content_container' );
}

/**
 * Ajax search for WooCommerce Products
 */
if ( ! function_exists( 'zoa_ajax_search_handler' ) ) {
	function zoa_ajax_search_handler() {
		check_ajax_referer( 'search_nonce' );

		$suggestions = array();

		$args = array(
			's'                   => sanitize_text_field( $_REQUEST['query'] ),
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => 1,
			'order'               => 'asc',
			'orderby'             => 'title',
		);

		$products = get_posts( $args );

		if ( ! empty( $products ) ) {
			foreach ( $products as $post ) {
				$product = wc_get_product( $post );

				$suggestions[] = array(
					'id'        => $product->get_id(),
					'value'     => strip_tags( $product->get_title() ),
					'url'       => $product->get_permalink(),
					'thumbnail' => $product->get_image( 'shop_thumbnail' ),
					'price'     => $product->get_price_html(),
					'excerpt'   => wp_trim_words( $post->post_excerpt, 20 ),
				);
			}
		} else {
			$suggestions[] = array(
				'id'    => -1,
				'value' => esc_html__( 'No results', 'zoa' ),
				'url'   => '',
			);
		}

		wp_reset_postdata();

		$suggestions = array(
			'suggestions' => $suggestions,
		);

		wp_send_json( $suggestions );
	}
	add_action( 'wp_ajax_zoa_ajax_search_handler', 'zoa_ajax_search_handler' );
	add_action( 'wp_ajax_nopriv_zoa_ajax_search_handler', 'zoa_ajax_search_handler' );
}

/**
 * Disable admin notice of Header Footer Elementor plugin
 */
if ( ! function_exists( 'zoa_header_footer_elementor_support' ) ) {
	function zoa_header_footer_elementor_support() {
		add_theme_support( 'header-footer-elementor' );
	}
	add_action( 'after_setup_theme', 'zoa_header_footer_elementor_support' );
}

// display custom admin notice
function zoa_custom_admin_notice() { ?>
	
	<div class="notice notice-info is-dismissible">
		<p style="font-size: 14px;"><?php _e('Introduce official Zoa Facebook community group for all users. <a href="https://www.facebook.com/groups/413663839168030/">Say Hi Now!</a> '); ?></p>
	</div>
	
<?php }
//add_action('admin_notices', 'zoa_custom_admin_notice');
