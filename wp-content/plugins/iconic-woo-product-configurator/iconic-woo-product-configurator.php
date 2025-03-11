<?php
/**
 * Plugin Name: WooCommerce Product Configurator by Iconic
 * Plugin URI: https://iconicwp.com/products/woocommerce-product-configurator/
 * Description: Product Configurator plugin for WooCommerce
 * Version: 1.23.0
 * Author: Iconic
 * Author Email: support@iconicwp.com
 * Author URI: https://iconicwp.com/
 * WC requires at least: 2.6.14
 * WC tested up to: 9.0.0
 *
 * @package iconic_wpc
 */

use Iconic_PC_NS\StellarWP\ContainerContract\ContainerInterface;

/**
 * Class Iconic_PC.
 */
class Iconic_PC {
	/**
	 * Name
	 *
	 * @var $name
	 */
	public static $name = 'WooCommerce Product Configurator';

	/**
	 * Slug
	 *
	 * @var $slug
	 */
	protected $slug = 'jckpc';

	/**
	 * Transient Prefix
	 *
	 * @var $transient_prefix
	 */
	public static $transient_prefix = 'iconic-pc-';

	/**
	 * Version
	 *
	 * @var $version
	 */
	public static $version = '1.23.0';

	/**
	 * Uplaods path
	 *
	 * @var $uploads_path
	 */
	protected $uploads_path;

	/**
	 * Uploads URL
	 *
	 * @var $uploads_url
	 */
	protected $uploads_url;

	/**
	 * Upload Directory
	 *
	 * @var $upload_dir
	 */
	protected $upload_dir;

	/**
	 * Notices class
	 *
	 * @since  1.1.4
	 * @var Iconic_PC_Transient_Notices
	 */
	public $notices;

	/**
	 * Class prefix
	 *
	 * @since  1.0.0
	 * @var string $class_prefix
	 */
	protected $class_prefix = 'Iconic_PC_';

	/**
	 * Settings array.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * The singleton instance of the plugin.
	 *
	 * @var Iconic_PC
	 */
	private static $instance;

	/**
	 * The DI container.
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->upload_dir   = wp_upload_dir();
		$this->uploads_path = $this->upload_dir['basedir'] . '/jckpc-uploads';
		$this->uploads_url  = $this->upload_dir['baseurl'] . '/jckpc-uploads';

		$this->define_constants();
		$this->load_classes();
		$this->container = new Iconic_PC_Core_Container();

		if ( ! Iconic_PC_Helpers::is_plugin_active( 'woocommerce/woocommerce.php' ) && ! Iconic_PC_Helpers::is_plugin_active( 'woocommerce-old/woocommerce.php' ) ) {
			return;
		}

		// Hook up to the init and plugins_loaded actions.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'initiate' ) );
		add_action( 'admin_init', array( $this, 'add_attribute_term_fields' ), 10 );
	}

	/**
	 * Define Constants.
	 */
	private function define_constants() {
		$this->define( 'ICONIC_PC_FILE', __FILE__ );
		$this->define( 'ICONIC_PC_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'ICONIC_PC_INC_PATH', ICONIC_PC_PATH . 'inc/' );
		$this->define( 'ICONIC_PC_VENDOR_PATH', ICONIC_PC_INC_PATH . 'vendor/' );
		$this->define( 'ICONIC_PC_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'ICONIC_PC_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'ICONIC_PC_PLUGIN_PATH_FILE', str_replace( trailingslashit( wp_normalize_path( WP_PLUGIN_DIR ) ), '', wp_normalize_path( __FILE__ ) ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Definition name.
	 * @param string|bool $value Definition value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Load classes.
	 */
	private function load_classes() {
		require_once ICONIC_PC_PATH . 'vendor-prefixed/autoload.php';
		require_once ICONIC_PC_INC_PATH . 'class-core-autoloader.php';

		Iconic_PC_Core_Autoloader::run(
			array(
				'prefix'   => 'Iconic_PC_',
				'inc_path' => ICONIC_PC_INC_PATH,
			)
		);

		$this->init_license();
		$this->init_telemetry();

		Iconic_PC_Core_Settings::run(
			array(
				'vendor_path'   => ICONIC_PC_VENDOR_PATH,
				'title'         => self::$name,
				'version'       => self::$version,
				'menu_title'    => 'Configurator',
				'settings_path' => ICONIC_PC_INC_PATH . 'admin/settings.php',
				'option_group'  => 'iconic_woo_product_configurator',
				'docs'          => array(
					'collection'      => 'woocommerce-product-configurator/',
					'troubleshooting' => 'woocommerce-product-configurator/wpc-troubleshooting/',
					'getting-started' => 'woocommerce-product-configurator/wpc-getting-started/',
				),
				'cross_sells'   => array(
					'iconic-woo-attribute-swatches',
					'iconic-woothumbs',
				),
			)
		);

		$this->notices = new Iconic_PC_Transient_Notices();
		Iconic_PC_Settings::run();
		Iconic_PC_WPML::run();
		Iconic_PC_Shortcodes::run();
		Iconic_PC_Inventory::run();
		Iconic_PC_Compat_WooThumbs::run();
		Iconic_PC_Compat_Divi::run();
		Iconic_PC_Compat_WC_Product_Bundles::run();
		Iconic_PC_Compat_Be_Theme::run();
		Iconic_PC_Ajax::run();
		Iconic_PC_Product::run();
		Iconic_PC_Blocks::run();
		Iconic_PC_Product_Editor::run();

		add_action( 'plugins_loaded', array( 'Iconic_PC_Core_Onboard', 'run' ), 10 );
	}

	/**
	 * Init license class.
	 */
	public function init_license() {
		// Allows us to transfer Freemius license.
		if ( file_exists( ICONIC_PC_PATH . 'class-core-freemius-sdk.php' ) ) {
			require_once ICONIC_PC_PATH . 'class-core-freemius-sdk.php';

			new Iconic_PC_Core_Freemius_SDK(
				array(
					'plugin_path'          => ICONIC_PC_PATH,
					'plugin_file'          => ICONIC_PC_FILE,
					'uplink_plugin_slug'   => 'iconic-wpc',
					'freemius'             => array(
						'id'         => '1039',
						'slug'       => 'iconic-woo-product-configurator',
						'public_key' => 'pk_fed17532221f66e11a200b70db56c',
					),
				)
			);
		}

		Iconic_PC_Core_License_Uplink::run(
			array(
				'basename'        => ICONIC_PC_BASENAME,
				'plugin_slug'     => 'iconic-wpc',
				'plugin_name'     => self::$name,
				'plugin_version'  => self::$version,
				'plugin_path'     => ICONIC_PC_PLUGIN_PATH_FILE,
				'plugin_class'    => self::class,
				'option_group'    => 'iconic_woo_product_configurator',
				'urls'            => array(
					'product' => 'https://iconicwp.com/products/woocommerce-product-configurator/',
				),
				'container_class' => self::class,
			)
		);
	}

	/**
	 * Init telemetry class.
	 *
	 * @return void
	 */
	public function init_telemetry() {
		Iconic_PC_Core_Telemetry::run(
			array(
				'file'                  => __FILE__,
				'plugin_slug'           => 'iconic-wpc',
				'option_group'          => 'iconic_woo_product_configurator',
				'plugin_name'           => self::$name,
				'plugin_url'            => ICONIC_PC_URL,
				'opt_out_settings_path' => 'sections/license/fields',
				'container_class'       => self::class,
			)
		);
	}

	/**
	 * Set settings.
	 */
	public function set_settings() {
		$this->settings = Iconic_PC_Core_Settings::$settings;
	}

	/**
	 * Run on plugins_loaded
	 */
	public function plugins_loaded() {
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_thumbnail' ), 10, 3 );
	}

	/**
	 * Run on init
	 */
	public function initiate() {
		load_plugin_textdomain( 'jckpc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		$this->create_uploads_folder();

		if ( is_admin() ) {
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_config_tab' ) );
			add_action(
				Iconic_PC_Deprecation::get_hook( 'woocommerce_product_data_panels' ),
				array(
					$this,
					'product_config_tab_options',
				)
			);
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_product_config_tab' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
			add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'admin_order_item_thumbnail' ), 10, 3 );
		} else {
			add_action( 'woocommerce_before_single_product', array( $this, 'setup_configurator_image' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
			add_action( 'woocommerce_order_item_class', array( $this, 'order_item_class' ), 20, 3 );
			add_filter( 'woocommerce_order_item_thumbnail', array( $this, 'order_item_thumbnail' ), 10, 2 );
			add_filter( 'woocommerce_email_order_items_args', array( $this, 'email_order_items_args' ), 10, 1 );
		}
	}

	/**
	 * Create uplaods folder
	 */
	private function create_uploads_folder() {
		if ( file_exists( $this->uploads_path ) ) {
			return;
		}

		mkdir( $this->uploads_path, 0775, true );
	}

	/**
	 * Add default image to attribute column
	 *
	 * @param string $content     Content.
	 * @param string $column_name Column name.
	 * @param int    $term_id     Term ID.
	 *
	 * @return string
	 */
	public function attribute_column_content( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'jckpc_default_img':
				$default_image_id  = self::get_default_image( $term_id );
				$default_image_src = wp_get_attachment_image_src( $default_image_id, 'thumbnail' );
				$default_image_src = ! empty( $default_image_id ) ? $default_image_src[0] : wc_placeholder_img_src();
				$content           = '<div style="padding: 2px; background: #fff; border: 1px solid #ccc; float: left; margin: 0 5px 5px 0;"><img src="' . $default_image_src . '" style="width:34px; height: auto; display: block;"></div>';
				break;

			default:
				break;
		}

		return $content;
	}

	/**
	 * Add attribute column.
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	public function add_attribute_column( $columns ) {
		$columns = array( 'jckpc_default_img' => 'Configurator' ) + $columns;

		return $columns;
	}

	/**
	 * Setup configurator image
	 */
	public function setup_configurator_image() {
		$configurator_enabled = Iconic_PC_Product::is_configurator_enabled();

		if ( ! $configurator_enabled ) {
			return;
		}
	}

	/**
	 * Check if configurator is allowed for product.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public function configurator_allowed( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product->is_type( 'variable' ) ) {
			$product_attributes = $product->get_variation_attributes();

			if ( is_array( $product_attributes ) && ! empty( $product_attributes ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add configurator tab to product meta.
	 *
	 * @return void
	 */
	public function product_config_tab() {
		global $post;

		if ( ! $post ) {
			return;
		}

		echo '<li class="jckpc_options_tab"><a href="#jckpc_options"><span>' . esc_attr( __( 'Configurator', 'jckpc' ) ) . '</span></a></li>';
	}

	/**
	 * Get sort order.
	 *
	 * @param int|bool $post_id Post ID.
	 * @param bool     $reverse Reverse sort order.
	 *
	 * @return array|bool
	 */
	public static function get_sort_order( $post_id = false, $reverse = true ) {
		if ( ! $post_id ) {
			return false;
		}

		static $response = array();

		if ( isset( $response[ $post_id ] ) ) {
			return apply_filters( 'iconic_pc_sort_order', $response[ $post_id ], $post_id );
		}

		$response[ $post_id ] = array(
			'string' => '',
			'array'  => array(),
		);

		$sort_order = get_post_meta( $post_id, 'jckpc_sort_order', true );

		if ( $sort_order ) {
			$response[ $post_id ]['string'] = $sort_order;
			$response[ $post_id ]['array']  = array_map( 'trim', explode( ',', $sort_order ) );
		} else {
			$response[ $post_id ]['array']  = array_keys( Iconic_PC_Product::get_attributes( $post_id, true ) );
			$response[ $post_id ]['string'] = implode( ',', $response[ $post_id ]['array'] );
		}

		if ( $reverse ) {
			$response[ $post_id ]['array'] = array_reverse( $response[ $post_id ]['array'] );
		}

		array_unshift( $response[ $post_id ]['array'], 'background' );

		return apply_filters( 'iconic_pc_sort_order', $response[ $post_id ], $post_id );
	}

	/**
	 * Configurator tab contents.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function product_config_tab_options( $post = false ) {
		if ( ! $post ) {
			global $post;
		}

		if ( ! $post ) {
			return;
		}

		echo '<div id="jckpc_options" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">';
		?>

		<div class="inline jckpc-notice notice woocommerce-message jckpc-reload-notice" style="display: none;">
			<p><?php esc_attr_e( "Since this product's attributes have been updated, you may need to refresh the page.", 'jckpc' ); ?></p>
		</div>

		<?php

		if ( ! $this->configurator_allowed( $post->ID ) ) {
			?>

			<div class="inline jckpc-notice notice woocommerce-message">
				<p><?php echo wp_kses_post( __( "Before you can manage the configurator layers you need to add some variations on the <strong>Variations</strong> tab. Once you're done, refresh the page.", 'jckpc' ) ); ?></p>

				<p>
					<a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_attr_e( 'Learn more', 'woocommerce' ); ?></a>
				</p>
			</div>

			<?php
		} else {
			$set_images   = get_post_meta( $post->ID, 'jckpc_images', true );
			$defaults     = get_post_meta( $post->ID, 'jckpc_defaults', true );
			$conditionals = get_post_meta( $post->ID, 'jckpc_conditionals', true );
			$sort_order   = self::get_sort_order( $post->ID, false );

			include 'inc/partials/meta-toolbar.php';

			$attributes    = Iconic_PC_Product::get_attributes( $post->ID );
			$static_layers = $this->get_static_layers( $set_images );
			$layers        = array_merge( $attributes, $static_layers );
			$layers        = ( ! empty( $sort_order['array'] ) ) ? self::sort_array_by_array( $layers, $sort_order['array'] ) : $layers;

			if ( $layers && is_array( $layers ) ) :

				echo '<input type="hidden" name="jckpc_sort_order" id="jckpc_sort_order" value="' . esc_attr( $sort_order['string'] ) . '">';

				echo '<div id="jckpc_sortable">';

				foreach ( $layers as $layer_id => $layer_data ) :

					if ( isset( $layer_data['type'] ) && 'static' === $layer_data['type'] ) {
						$this->get_static_layer_template( $layer_id, $layer_data );
					} else {
						include 'inc/partials/meta-attribute-layer.php';
					}

				endforeach;

				echo '</div>';

			endif;

			echo '<div class="jckpc-layer-options jckpc-layer-options--no-sort options_group custom_tab_options">';

			echo '<h2 class="jckpc-layer-options__title">' . esc_attr( __( 'Background Image', 'jckpc' ) ) . '</h2>';

			echo '<div class="jckpc-layer-options__content-wrapper">';

			echo '<table class="widefat fixed">';

			echo '<thead>';
			echo '<tr>';
			echo '<th>' . esc_attr( __( 'Image', 'jckpc' ) ) . '</th>';
			echo '</tr>';
			echo '</thead>';

			$field_name        = 'jckpc_images[background]';
			$field_id          = 'jckpc_background_image';
			$selected_image_id = isset( $set_images['background'] ) ? $set_images['background'] : '';
			$popup_title       = __( 'Set background image', 'jckpc' );
			$popup_button_text = __( 'Set Image', 'jckpc' );
			$button_text       = __( 'Add Image', 'jckpc' );

			echo $this->image_upload_row(
				array(
					'field_name'        => esc_attr( $field_name ),
					'field_id'          => esc_attr( $field_id ),
					'selected_image_id' => esc_attr( $selected_image_id ),
					'popup_title'       => esc_attr( $popup_title ),
					'popup_button_text' => esc_attr( $popup_button_text ),
					'button_text'       => esc_attr( $button_text ),
					'classes'           => array( 'alternate' ),
					'product_id'        => esc_attr( $post->ID ),
				)
			);

			echo '</table>';

			echo '</div>';

			echo '</div>';

			$this->get_static_layer_template();
		}

		echo '</div>';
	}

	/**
	 * Get static layer template
	 *
	 * @param bool|int   $layer_id   Layer ID.
	 * @param bool|array $layer_data Layer data.
	 */
	public static function get_static_layer_template( $layer_id = false, $layer_data = false ) {
		$blank = ! $layer_id;

		include 'inc/partials/meta-static-layer.php';
	}

	/**
	 * Get static layers.
	 *
	 * @param array $set_images Set images.
	 *
	 * @return array
	 */
	public function get_static_layers( $set_images = null ) {
		if ( empty( $set_images ) ) {
			return array();
		}

		$static_layers = array();

		foreach ( $set_images as $layer_id => $image_id ) {
			if ( strpos( $layer_id, 'jckpc-static-' ) === false ) {
				continue;
			}

			$index = absint( str_replace( 'jckpc-static-', '', $layer_id ) );

			$static_layers[ $layer_id ] = array(
				'type'     => 'static',
				'image_id' => $image_id,
				'index'    => $index,
			);
		}

		return $static_layers;
	}

	/**
	 * Save configurator tab.
	 *
	 * @param int $post_id Post ID.
	 */
	public function process_meta_product_config_tab( $post_id ) {
		if ( $this->configurator_allowed( $post_id ) ) {
			$enabled    = filter_input( INPUT_POST, 'jckpc_enabled', FILTER_SANITIZE_STRING );
			$sort_order = filter_input( INPUT_POST, 'jckpc_sort_order', FILTER_SANITIZE_STRING );
			$defaults   = (array) filter_input( INPUT_POST, 'jckpc_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			update_post_meta( $post_id, 'jckpc_enabled', $enabled );

			if ( $sort_order ) {
				update_post_meta( $post_id, 'jckpc_sort_order', $sort_order );
			}

			$images = $this->validate_image_layers( $post_id );
			update_post_meta( $post_id, 'jckpc_images', $images );

			$conditionals = $this->validate_conditional_layers();
			update_post_meta( $post_id, 'jckpc_conditionals', $conditionals );

			$defaults = isset( $_POST['jckpc_defaults'] ) && is_array( $_POST['jckpc_defaults'] ) ? $_POST['jckpc_defaults'] : array();
			update_post_meta( $post_id, 'jckpc_defaults', $defaults );
		}
	}

	/**
	 * Validate image layers for product.
	 */
	public function validate_image_layers( $product_id ) {
		$validated_images = array();
		$enabled          = filter_input( INPUT_POST, 'jckpc_enabled', FILTER_VALIDATE_BOOLEAN );
		$images           = filter_input( INPUT_POST, 'jckpc_images', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );

		if ( empty( $images ) ) {
			return $images;
		}

		foreach ( $images as $attribute => $values ) {
			$clean_attribute = str_replace( 'jckpc-', '', $attribute );

			// Validate background image.
			if ( 'background' === $attribute ) {
				$image_id = $values;

				if ( empty( $image_id ) ) {
					if ( ! $enabled ) {
						continue;
					}

					$this->notices->add_notice( 'error', __( 'You must add a background image in the configurator tab.', 'jckpc' ) );
					continue;
				}

				if ( ! $this->validate_image( $image_id, 'png' ) ) {
					continue;
				}

				$validated_images[ $attribute ] = $image_id;
				continue;
			}

			// Validate static layer.
			if ( strpos( $attribute, 'jckpc-static-' ) !== false ) {
				$image_id = $values;

				if ( ! $this->validate_image( $image_id, 'png' ) ) {
					continue;
				}

				$validated_images[ $attribute ] = $image_id;
				continue;
			}

			if ( empty( $values ) ) {
				continue;
			}

			// Validate attribute value images.
			foreach ( $values as $value => $image_id ) {
				if ( empty( $image_id ) ) {
					$mapped_attribute = Iconic_PC_Product::get_mapped_attribute( $product_id, $value, $attribute );
					$term             = get_term_by( 'slug', $mapped_attribute, $clean_attribute );

					if ( $term ) {
						$image_id = self::get_default_image( $term->term_id );
					}
				}

				if ( empty( $image_id ) ) {
					continue;
				}

				if ( ! $this->validate_image( $image_id, 'png' ) ) {
					continue;
				}

				$validated_images[ $attribute ][ $value ] = absint( $image_id );
			}
		}

		return $validated_images;
	}

	/**
	 * Validate conditional layers.
	 *
	 * @return array
	 */
	public function validate_conditional_layers() {
		$validated_conditionals = array();
		$conditions             = filter_input( INPUT_POST, 'jckpc_conditionals', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $conditions ) ) {
			return $validated_conditionals;
		}

		foreach ( $conditions as $attribute => $condition ) {
			foreach ( $condition as $id => $data ) {
				// Validate rules.
				$validated_conditionals[ $attribute ][ $id ]['rules'] = array_values( $data['rules'] );

				// Validate images.
				$validated_conditionals[ $attribute ][ $id ]['values'] = array();

				foreach ( $data['values'] as $value => $image_id ) {
					if ( empty( $image_id ) ) {
						continue;
					}

					if ( ! $this->validate_image( $image_id, 'png' ) ) {
						continue;
					}

					$validated_conditionals[ $attribute ][ $id ]['values'][ $value ] = absint( $image_id );
				}
			}

			$validated_conditionals[ $attribute ] = array_values( $validated_conditionals[ $attribute ] );
		}

		return $validated_conditionals;
	}

	/**
	 * Layout helper for image table rows.
	 *
	 * @param array $args Args.
	 *
	 * @return string
	 */
	public function image_upload_row( $args ) {
		global $post_id, $wpdb;

		$disable_inputs = Iconic_PC_Helpers::maybe_disable_layer_inputs();
		$defaults       = array(
			'row_name'          => false,
			'field_name'        => false,
			'field_id'          => false,
			'selected_image_id' => false,
			'popup_title'       => false,
			'popup_button_text' => false,
			'button_text'       => false,
			'classes'           => array(),
			'show_inventory'    => false,
			'show_fee'          => false,
			'product_id'        => $post_id,
		);

		$args = wp_parse_args( $args, $defaults );

		$args['classes'][] = 'uploader';

		$return = '<tr class="' . implode( ' ', $args['classes'] ) . '">';

		$selected_image_src = ! empty( $args['selected_image_id'] ) ? wp_get_attachment_image_src( $args['selected_image_id'], 'thumbnail' ) : false;
		$selected_image_url = ( $selected_image_src ) ? $selected_image_src[0] : false;

		$return .= '<td>';
		$return .= '<input ' . disabled( $disable_inputs, true, false ) . ' type="hidden" name="' . esc_attr( $args['field_name'] ) . '" id="' . esc_attr( $args['field_id'] ) . '" value="' . esc_attr( $args['selected_image_id'] ) . '" />';
		$return .= '<div id="' . $args['field_id'] . '_thumbwrap" class="jckpc_attthumb jckpc-layer-options__thumbnail">';
		if ( $selected_image_src ) {
			$return .= '<img src="' . $selected_image_url . '" width="80" height="80">';
		}
		if ( ! $disable_inputs ) {
			$return .= '<a href="#" class="jckpc-image-button jckpc-image-button--remove" data-uploader_field="#' . $args['field_id'] . '">' . __( 'Remove Image', 'jckpc' ) . '</a>';
			$return .= '<a href="#" class="jckpc-image-button jckpc-image-button--upload" id="' . $args['field_id'] . '_button" data-uploader_title="' . $args['popup_title'] . '" data-uploader_button_text="' . $args['popup_button_text'] . '" data-uploader_field="#' . $args['field_id'] . '">' . $args['button_text'] . '</a>';
		}
		$return .= '</div>';
		$return .= '</td>';

		if ( false !== $args['row_name'] ) {
			$return .= '<td>' . $args['row_name'] . '</td>';
		}

		if ( $args['show_fee'] ) {
			$fee_att_val_id = str_replace( '_image', '', $args['field_id'] );
			$fee_field_name = 'jckpc_fee[' . $fee_att_val_id . ']';

			$fee = '';

			$return .= '<td>';
			$return .= '<input ' . disabled( $disable_inputs, true, false ) . ' type="number" name="' . $fee_field_name . '" id="' . $fee_att_val_id . '_fee" value="' . $fee . '" />';
			$return .= '</td>';
		}

		if ( $args['show_inventory'] ) {
			$inventory_att_val_id = str_replace( '_image', '', $args['field_id'] );
			$inventory_field_name = 'jckpc_inventory[' . $inventory_att_val_id . ']';

			$inventory = Iconic_PC_Inventory::get_inventory(
				array(
					'product_id' => $args['product_id'],
					'att_val_id' => $inventory_att_val_id,
				)
			);

			$return .= '<td>';
			$return .= '<input ' . disabled( $disable_inputs, true, false ) . ' type="number" name="' . $inventory_field_name . '" id="' . $inventory_att_val_id . '_inventory" value="' . $inventory . '" />';
			$return .= '</td>';
		}

		$return .= '</tr>';

		return $return;
	}

	/**
	 * Get WooCommerce attribute taxonomy names
	 *
	 * @return array
	 */
	public function get_woo_attribute_taxonomies() {
		$attributes = wc_get_attribute_taxonomies();
		$return     = array();

		if ( $attributes && is_array( $attributes ) && ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$return[] = esc_html( wc_attribute_taxonomy_name( $attribute->attribute_name ) );
			}
		}

		return $return;
	}

	/**
	 * Get default image for attribute term.
	 *
	 * @param int|bool $term_id Term ID.
	 *
	 * @return string|bool
	 */
	public static function get_default_image( $term_id = false ) {
		if ( ! $term_id ) {
			return false;
		}

		return get_term_meta( $term_id, 'jckpc_default_image', true );
	}

	/**
	 * Display configurator image.
	 *
	 * @param int|null $product_id Product ID.
	 */
	public function display_product_image( $product_id = null ) {
		global $post;

		$product_id = $product_id ? $product_id : $post->ID;
		$images     = $this->get_images_by_attributes( $product_id, $_REQUEST );

		// output images.
		$wrapper_classes = apply_filters(
			'iconic_pc_images_wrapper_classes',
			array(
				'iconic-pc-images',
			)
		);

		$attribute_json_data = Iconic_PC_Product::get_json_data( $product_id );
		$conditionals        = Iconic_PC_Product::get_conditional_json_data( $product_id );

		if ( ! empty( $attribute_json_data ) ) : ?>
			<script class="iconic-pc-layers-<?php echo esc_attr( $product_id ); ?>" type="application/json">
				<?php echo wp_json_encode( $attribute_json_data ); ?>
			</script>
		<?php endif;

		if ( ! empty( $conditionals ) ) : ?>
			<script class="iconic-pc-layers-conditionals-<?php echo esc_attr( $product_id ); ?>" type="application/json">
				<?php echo wp_json_encode( $conditionals ); ?>
			</script>
		<?php endif; ?>

		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<div class="iconic-pc-image-wrap" data-iconic_pc_product_id="<?php echo esc_attr( $product_id ); ?>">
				<?php do_action( 'iconic_pc_before_layers', $product_id ); ?>

				<?php if ( is_array( $images ) ) { ?>
					<?php foreach ( $images as $layer_id => $image_data ) { ?>
						<div class="iconic-pc-image iconic-pc-image--<?php echo esc_attr( $layer_id ); ?>" data-iconic_pc_default_layer="<?php echo esc_attr( $image_data['default'] ); ?>" style="z-index: <?php echo esc_attr( $image_data['zindex'] ); ?>;">
							<?php echo wp_kses( $image_data['html'], array(
								'img' => array(
									'src'                     => true,
									'srcset'                  => true,
									'sizes'                   => true,
									'class'                   => true,
									'id'                      => true,
									'width'                   => true,
									'height'                  => true,
									'alt'                     => true,
									'align'                   => true,
									'loading'                 => true,
									'data-large_image'        => true,
									'data-large_image_width'  => true,
									'data-large_image_height' => true,
									'data-src'                => true,
								),
							) ); ?>
						</div>
					<?php } ?>
				<?php } ?>

				<?php do_action( 'iconic_pc_after_layers', $product_id ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get image data
	 *
	 * @param int   $pid        Product ID.
	 * @param array $attributes Attributes.
	 *
	 * @return array
	 */
	public function get_image_data( $pid, $attributes ) {
		$img_data = array(
			'prodid' => $pid,
		);

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute_slug => $attribute_value ) {
				$attribute_slug = Iconic_PC_Helpers::sanitise_str( $attribute_slug, '', false );

				if ( 'attribute_' !== substr( $attribute_slug, 0, 10 ) ) {
					$attribute_slug = 'attribute_' . $attribute_slug;
				}

				$img_data[ $attribute_slug ] = $attribute_value;
			}
		}

		return $img_data;
	}

	/**
	 * Get single image layer
	 */
	public function get_image_layer() {
		do_action( 'iconic_pc_before_get_image_layer' );

		$response = array(
			'post'       => $_POST,
			'images'     => array(),
			'request_id' => absint( filter_input( INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT ) ),
		);

		$product_id          = filter_input( INPUT_POST, 'prodid', FILTER_SANITIZE_NUMBER_INT );
		$selected_attributes = filter_input( INPUT_POST, 'selected_attributes', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $selected_attributes ) ) {
			wp_send_json_error( $response );
		}

		$response['images'] = $this->get_images_by_attributes( $product_id, $selected_attributes );

		wp_send_json_success( $response );
	}

	/**
	 * Get conditional image id.
	 *
	 * @param int   $product_id
	 * @param array $selected_attributes
	 *
	 * @return array
	 */
	public static function get_conditional_image_ids( $product_id, $selected_attributes = array() ) {
		$image_ids           = array();
		$default_attributes  = Iconic_PC_Product::get_default_attributes( $product_id );
		$selected_attributes = array_filter( $selected_attributes );
		$selected_attributes = wp_parse_args( $selected_attributes, $default_attributes );

		if ( empty( $selected_attributes ) ) {
			return $image_ids;
		}

		$conditionals = Iconic_PC_Product::get_conditionals( $product_id );

		if ( empty( $conditionals ) ) {
			return $image_ids;
		}

		foreach ( $conditionals as $attribute => $conditional ) {
			foreach ( $conditional as $index => $data ) {
				$rules_met = 0;

				foreach ( $data['rules'] as $rule ) {
					$rule_attribute = str_replace( 'jckpc-', '', $rule['attribute'] );

					if ( empty( $selected_attributes[ $rule_attribute ] ) ) {
						continue;
					}

					if ( 'is_equal_to' === $rule['condition'] ) {
						if ( $rule['value'] === $selected_attributes[ $rule_attribute ] ) {
							$rules_met ++;
							continue;
						}
					} elseif ( 'is_not_equal_to' === $rule['condition'] ) {
						if ( $rule['value'] !== $selected_attributes[ $rule_attribute ] ) {
							$rules_met ++;
							continue;
						}
					}
				}

				if ( count( $data['rules'] ) === $rules_met ) {
					$image_ids[ $attribute ] = array();
					$mapped_attributes       = Iconic_PC_Product::get_mapped_attributes( $product_id );

					foreach ( $data['values'] as $attribute_value => $image_id ) {
						$attribute_value = isset( $mapped_attributes[ $attribute ][ $attribute_value ] ) ? $mapped_attributes[ $attribute ][ $attribute_value ] : $attribute_value;

						$image_ids[ $attribute ][ $attribute_value ] = $image_id;
					}

					break;
				}
			}
		}

		return $image_ids;
	}

	/**
	 * Format selected attributes.
	 *
	 * Selected attributes is an array generated by jquery using
	 * serializeArray().
	 *
	 * @param $selected_attributes
	 *
	 * @return array
	 */
	public static function format_selected_attributes( $selected_attributes ) {
		$formatted = array();

		if ( empty( $selected_attributes ) ) {
			return $formatted;
		}

		foreach ( $selected_attributes as $key => $value ) {
			if ( ! is_array( $value ) ) {
				if ( strpos( $key, 'attribute_' ) !== 0 ) {
					continue;
				}

				$name               = str_replace( 'attribute_', '', $key );
				$formatted[ $name ] = $value;
				continue;
			}

			if ( strpos( $value['name'], 'attribute_' ) !== 0 ) {
				continue;
			}

			$name               = str_replace( 'attribute_', '', $value['name'] );
			$formatted[ $name ] = $value['value'];
		}

		return $formatted;
	}

	/**
	 * Generate image paths
	 *
	 * @param int   $prodid            Product ID.
	 * @param array $chosen_attributes Chosen attributes.
	 *
	 * @return array
	 */
	public function generate_img_paths( $prodid, $chosen_attributes ) {
		$image_name = $prodid . '-' . md5( implode( '-', array_filter( $chosen_attributes ) ) );
		/**
		 * Filter the image file name.
		 *
		 * @param string $image_name        Image name.
		 * @param int    $prodid            Product ID.
		 * @param array  $chosen_attributes Array of attribute name -> term name pairs.
		 *
		 * @return string
		 *
		 * @since 1.19.2
		 */
		$image_name = apply_filters( 'iconic_pc_image_name', $image_name, $prodid, $chosen_attributes );

		return array(
			'imgName'          => $image_name,
			'finalImgPath'     => $this->uploads_path . '/' . $image_name . '.png',
			'finalImgUrl'      => $this->uploads_url . '/' . $image_name . '.png',
			'thumbnailImgPath' => $this->uploads_path . '/' . $image_name . '-thumbnail.png',
			'thumbnailImgUrl'  => $this->uploads_url . '/' . $image_name . '-thumbnail.png',
		);
	}

	/**
	 * Get attributes from query string.
	 *
	 * @param array|bool $qarr
	 *
	 * @return array
	 */
	public function get_atts_from_querystring( $qarr = false ) {
		if ( ! $qarr ) {
			$qarr = $_GET;
		}

		// Get defaults.
		$attributes = array();

		if ( is_array( $qarr ) ) {
			foreach ( $qarr as $key => $value ) {
				// If it's a taxonomy, use the slug instead of the name.
				if ( strpos( $key, 'attribute_pa_' ) === 0 ) {
					$taxonomy       = str_replace( 'attribute_', '', $key );
					$attribute_term = get_term_by( 'slug', $value, $taxonomy );

					if ( $attribute_term && ! is_wp_error( $attribute_term ) ) {
						$attributes[ $key ] = $attribute_term->slug;
					}
				} elseif ( strpos( $key, 'attribute_' ) === 0 ) {
					$attributes[ $key ] = wp_unslash( $value );
				}
			}
		}

		if ( isset( $qarr['jckpc-img-size'] ) ) {
			$attributes['jckpc-img-size'] = $qarr['jckpc-img-size'];
		}

		return apply_filters( 'iconic_pc_query_string_attributes', $attributes );
	}

	/**
	 * Get images by attributes.
	 *
	 * @param int   $product_id
	 * @param array $selected_attributes
	 *
	 * @return array
	 */
	public static function get_image_ids_by_attributes( $product_id, $selected_attributes = array() ) {
		$image_ids           = array();
		$selected_attributes = self::format_selected_attributes( $selected_attributes );

		// Add static layers.
		$static_layers = Iconic_PC_Product::get_static_layers( $product_id );
		$image_ids     = array_merge( $static_layers, $image_ids );

		// Add background.
		$image_ids['background'] = Iconic_PC_Product::get_background_layer( $product_id );

		// Add selected layers.
		$product               = wc_get_product( $product_id );
		$attributes            = $product->get_attributes();
		$conditional_image_ids = self::get_conditional_image_ids( $product_id, $selected_attributes );

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute => $attribute_data ) {
				if ( ! $attribute_data->get_variation() ) {
					continue;
				}

				$stripped_attribute = Iconic_PC_Helpers::sanitise_str( $attribute, '', false );
				$attribute          = Iconic_PC_Helpers::sanitise_str( $stripped_attribute );
				$value              = empty( $stripped_attribute ) || ! empty( $selected_attributes[ $stripped_attribute ] ) ? $selected_attributes[ $stripped_attribute ] : null;

				// If a value hasn't been selected for this layer,
				// use the default value from the configurator tab.
				if ( is_null( $value ) ) {
					$defaults = Iconic_PC_Product::get_default_attributes( $product_id );

					if ( ! empty( $defaults[ $stripped_attribute ] ) ) {
						$value = Iconic_PC_Helpers::sanitise_str( $defaults[ $stripped_attribute ] );
					}
				}

				// Set empty values.
				$image_ids[ $attribute ] = null;

				// If conditional image ID is set, use it and continue.
				// Conditional layers use the json escaped attribute value.
				if ( isset( $conditional_image_ids[ $attribute ][ $value ] ) ) {
					$image_ids[ $attribute ] = absint( $conditional_image_ids[ $attribute ][ $value ] );
					continue;
				}

				// Normal layers use the sanitized attribute value.
				$value = Iconic_PC_Helpers::sanitise_str( $value );

				// If image ID is explicitly set, use it and continue.
				$set_images = Iconic_PC_Product::get_set_images( $product_id );

				if ( isset( $set_images[ $attribute ][ $value ] ) ) {
					$image_ids[ $attribute ] = absint( $set_images[ $attribute ][ $value ] );
					continue;
				}

				// If this is a term, use the default term image ID and continue.
				if ( strpos( $attribute, 'jckpc-pa_' ) === 0 ) {
					$mapped_attribute = Iconic_PC_Product::get_mapped_attribute( $product_id, $value, $attribute );
					$term             = get_term_by( 'slug', $mapped_attribute, str_replace( 'jckpc-', '', $attribute ) );

					if ( empty( $term ) || is_wp_error( $term ) ) {
						continue;
					}

					$term_image_id = self::get_default_image( $term->term_id );

					if ( $term_image_id ) {
						$image_ids[ $attribute ] = absint( $term_image_id );
						continue;
					} else {
						// get default image id from product attribute.
						if ( false !== get_post_meta( $product_id, 'jckpc_defaults', true ) ) {
							$default_attribute        = get_post_meta( $product_id, 'jckpc_defaults', true )[ $attribute ];
							$mapped_default_attribute = Iconic_PC_Product::get_mapped_attribute( $product_id, $default_attribute, $attribute );
							$term                     = get_term_by( 'slug', str_replace( 'jckpc-', '', $mapped_default_attribute ), str_replace( 'jckpc-', '', $attribute ) );
							$term_id                  = ( $term ) ? $term->term_id : false;
							$term_image_id            = self::get_default_image( $term_id );
							$image_ids[ $attribute ]  = absint( $term_image_id );
							continue;
						}
					}
				}
			}
		}

		$sort_order = self::get_sort_order( $product_id );
		$image_ids  = self::sort_array_by_array( $image_ids, $sort_order['array'] );

		return apply_filters( 'iconic_pc_image_ids_by_attributes', $image_ids, $product_id, $attributes );
	}

	/**
	 * @param int   $product_id
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function get_images_by_attributes( $product_id, $attributes = array() ) {
		$images     = array();
		$image_ids  = self::get_image_ids_by_attributes( $product_id, $attributes );
		$image_size = Iconic_PC_Product::get_image_size( 'single' );
		$defaults   = get_post_meta( $product_id, 'jckpc_defaults', true );

		if ( ! empty( $image_ids ) ) {
			$zindex = 1;

			foreach ( $image_ids as $attribute => $image_id ) {
				$full_src   = wp_get_attachment_image_src( $image_id, 'full' );
				$image_args = array();

				if ( $full_src && 'background' === $attribute ) {
					$dynamic_image = $this->get_product_image_url(
						array_merge(
							array( 'prodid' => $product_id ),
							self::get_attributes_from_request()
						)
					);

					$image_args['data-large_image']        = $dynamic_image;
					$image_args['data-src']                = $dynamic_image;
					$image_args['data-large_image_width']  = $full_src[1];
					$image_args['data-large_image_height'] = $full_src[2];
				}

				$single_src = wp_get_attachment_image_src( $image_id, $image_size );

				$images[ $attribute ] = array(
					'id'      => $image_id,
					'single'  => ( $single_src ) ? $single_src : array(),
					'full'    => ( $full_src ) ? $full_src : array(),
					'path'    => get_attached_file( $image_id ),
					'html'    => wp_get_attachment_image( $image_id, $image_size, false, $image_args ),
					'zindex'  => $zindex * 10,
					'default' => isset( $defaults[ $attribute ] ) ? Iconic_PC_Product::get_mapped_attribute( $product_id, $defaults[ $attribute ], $attribute ) : '',
				);

				$zindex ++;
			}
		}

		return apply_filters( 'iconic_pc_images_by_attributes', $images, $product_id, $attributes, $image_ids );
	}

	/**
	 * Is attribute disallowed in request?
	 *
	 * @param string $key Attribute slug.
	 * 
	 * @return boolean
	 */
	public static function is_param_disallowed( $key ) {
		$disallow = false;
		
		/**
		 * Filter the disallowed params using
		 * exact or partial key matches.
		 * 
		 * @filter iconic_pc_disallowed_params
		 * @since 1.13.0
		 */
		$disallowed_attributes = apply_filters( 
			'iconic_pc_disallowed_params', 
			array(
				// Extra Product Options & Add-Ons.
				'attribute__tmcartepo',
				'attribute__tm_epo',
				'attribute__tmdata',
				'attribute__tmpost_data',
			)
		);

		foreach ( $disallowed_attributes as $attribute ) {
			if ( str_contains( $key, $attribute ) ) {
				$disallow = true;
				break;
			}
		}

		return $disallow;
	}

	/**
	 * Get attributes from request.
	 *
	 * @return array
	 */
	public static function get_attributes_from_request() {
		$attributes = array();

		if ( empty( $_REQUEST ) || ! is_array( $_REQUEST ) ) {
			return $attributes;
		}

		foreach ( $_REQUEST as $key => $value ) {
			if ( ! str_contains( $key, 'attribute_' ) ) {
				continue;
			}

			$attributes[ $key ] = wp_unslash( $value );
		}

		return $attributes;
	}

	/**
	 * Get images from chosen attributes.
	 *
	 * @param array $set_images        Set images.
	 * @param array $chosen_attributes Chosen attributes.
	 *
	 * @return array
	 */
	public function get_images_from_chosen_atts( $set_images, $chosen_attributes ) {
		$images = array();
		$size   = filter_input( INPUT_GET, 'jckpc-img-size' );
		$size   = $size ? sanitize_text_field( $size ) : 'full';

		if ( is_array( $set_images ) ) {
			foreach ( $set_images as $layer_id => $layer_data ) {
				if ( strpos( $layer_id, 'jckpc-static-' ) === false ) {
					continue;
				}

				$images[ $layer_id ] = $this->get_attachment_image_path( $layer_data, $size );
			}
		}

		if ( is_array( $chosen_attributes ) ) {
			foreach ( $chosen_attributes as $attribute_slug => $attribute_value ) {
				if ( empty( $set_images[ $attribute_slug ][ $attribute_value ] ) ) {
					continue;
				}

				$images[ $attribute_slug ] = $this->get_attachment_image_path( $set_images[ $attribute_slug ][ $attribute_value ], $size );
			}
		}

		if ( ! empty( $set_images['background'] ) ) {
			$images['background'] = $this->get_attachment_image_path( $set_images['background'], $size );
		}

		// Reverse so the layering is correct.
		return $images;
	}

	/**
	 * Get image path from ID
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Size.
	 *
	 * @return bool|string
	 */
	public function get_attachment_image_path( $attachment_id, $size = 'single' ) {
		$size = Iconic_PC_Product::get_image_size( $size );

		$image_src = wp_get_attachment_image_src( $attachment_id, $size );

		if ( ! $image_src ) {
			return false;
		}

		$img_path = realpath( str_replace( $this->upload_dir['baseurl'], $this->upload_dir['basedir'], self::strip_query_string( $image_src[0] ) ) );

		return $img_path;
	}

	/**
	 * Strip query string from URL.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	public static function strip_query_string( $url ) {
		$url_exploded = explode( '?', $url );

		return reset( $url_exploded );
	}

	/**
	 * Get product image URL.
	 *
	 * @param array $image_data Image data.
	 *
	 * @return mixed|string
	 */
	public function get_product_image_url( $image_data ) {
		foreach ( $image_data as $key => $value ) {
			if ( self::is_param_disallowed( $key ) ) {
				unset( $image_data[ $key ] );
				continue;
			}

			if ( 'jckpc-img-size' === $key || strpos( $key, 'jckpc-' ) === false ) {
				continue;
			}

			$new_key                = str_replace( 'jckpc-', 'attribute_', $key );
			$image_data[ $new_key ] = str_replace( 'jckpc-', '', $value );

			unset( $image_data[ $key ] );
		}

		$chosen_attributes = $this->get_atts_from_querystring( $image_data );
		$image_paths       = $this->generate_img_paths( $image_data['prodid'], $chosen_attributes );

		if ( ! $this->settings['general_cache_enable'] ) {
			delete_transient( self::$transient_prefix . $image_paths['imgName'] );

			if ( file_exists( $image_paths['finalImgPath'] ) ) {
				unlink( $image_paths['finalImgPath'] );
			}

			/**
			 * Hook: after deleting cached images.
			 * 
			 * @since 1.22.0
			 * 
			 * @param string $context Either `all` or the image name.
			 */
			do_action( 'iconic_pc_after_delete_from_cache', $image_paths['imgName'] );
		}

		$transient   = get_transient( self::$transient_prefix . $image_paths['imgName'] );
		$file_exists = file_exists( $image_paths['finalImgPath'] );

		if ( false === $transient || ! $file_exists ) {
			$image_data['action'] = 'iconic_pc_generate_image';

			// If the transient has expired, remove the image!
			if ( false === $transient && $file_exists ) {
				unlink( $image_paths['finalImgPath'] );

				/**
				 * Hook: after deleting cached images.
				 * 
				 * @since 1.22.0
				 * 
				 * @param string $context Either `all` or the image name.
				 */
				do_action( 'iconic_pc_after_delete_from_cache', $image_paths['imgName'] );
			}

			foreach ( $image_data as $k => $v ) {
				if ( 0 === strpos( $k, 'attribute_pa_' ) && ! is_array( $v ) ) {
					$image_data[ $k ] = rawurlencode( $v );
				}
			}

			return add_query_arg( array_filter( $image_data ), admin_url( 'admin-ajax.php' ) );
		} else {
			/**
			 * Filter: modify the pre-generated product image URL.
			 * 
			 * Can be used to modify the image URL e.g. if you are
			 * using a CDN to deliver generated images.
			 * 
			 * IMPORTANT: the php directive `allow_url_fopen` must be 
			 * enabled on the server, if a CDN is being used to serve 
			 * generated images.
			 * 
			 * @since 1.22.0
			 * 
			 * @param string $url        Path to the pre-generated image URL.
			 * @param array  $image_data Associative array of image data required to build URL.
			 */
			return apply_filters( 'iconic_pc_product_image_url', $image_paths['finalImgUrl'], $image_data );
		}
	}

	/**
	 * Modify cart thumbnail.
	 *
	 * @param string $thumb         Thumbnail HTML.
	 * @param array  $cart_item     Cart item data.
	 * @param bool   $cart_item_key Cart item key.
	 *
	 * @return string
	 */
	public function cart_thumbnail( $thumb, $cart_item, $cart_item_key = false ) {
		$configurator_enabled = Iconic_PC_Product::is_configurator_enabled( $cart_item['product_id'] );

		if ( ! $configurator_enabled ) {
			return $thumb;
		}

		$cart_item['wrap']       = isset( $cart_item['wrap'] ) ? $cart_item['wrap'] : false;
		$cart_item['image_size'] = isset( $cart_item['image_size'] ) ? $cart_item['image_size'] : array(
			get_option( 'thumbnail_size_w' ),
			get_option( 'thumbnail_size_h' ),
		);
		$cart_item['image_size'] = apply_filters( 'jckpc_thumbnail_image_size', $cart_item['image_size'] );

		$attributes = ( isset( $cart_item['variation'] ) && ! empty( $cart_item['variation'] ) ) ? $cart_item['variation'] : array();

		$image_data                   = $this->get_image_data( $cart_item['product_id'], $attributes );
		$image_data['jckpc-img-size'] = 'woocommerce_thumbnail';
		$img_url                      = $this->get_product_image_url( $image_data );

		$image = '<img src="' . esc_attr( $img_url ) . '" width="' . esc_attr( $cart_item['image_size'][0] ) . '" height="' . esc_attr( $cart_item['image_size'][1] ) . '">';

		if ( $cart_item['wrap'] ) {
			return sprintf( '<div style="margin-bottom: 5px;">%s</div>', $image );
		} else {
			return $image;
		}

		return $thumb;
	}

	/**
	 * Add class to order item.
	 *
	 * @param string $class Class.
	 * @param array  $item  Item.
	 * @param int    $order Order.
	 *
	 * @return string
	 */
	public function order_item_class( $class, $item, $order ) {
		$prodid = $item['product_id'];

		$configurator_enabled = Iconic_PC_Product::is_configurator_enabled( $prodid );

		if ( $configurator_enabled ) {
			$class .= ' jckpc_configurated';
		}

		return $class;
	}

	/**
	 * Register scripts and styles
	 */
	public function register_scripts_and_styles() {
		global $jckpc;

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( is_admin() ) {
			global $pagenow, $post;

			$screen = get_current_screen();

			if (
				( 'post.php' === $pagenow && $post && 'product' === $post->post_type ) ||
				( 'post-new.php' === $pagenow && $post && 'product' === $post->post_type ) ||
				( 'term.php' === $pagenow ) ||
				( 'woocommerce_page_wc-admin' === $screen->id )
			) {
				wp_enqueue_media();
				$this->load_file( 'jckpc-script', '/assets/admin/js/main' . $min . '.js', true, array( 'wp-util', 'jquery-blockui', 'jquery-tiptip' ) );


				wp_register_style( 'jckpc_admin_styles', ICONIC_PC_URL . 'assets/admin/css/main' . $min . '.css', array( 'woocommerce_admin_styles' ), self::$version );

				wp_enqueue_style( 'jckpc_admin_styles' );

				$vars = array(
					'nonce' => wp_create_nonce( 'iconic-pc' ),
					'i18n'  => array(
						'png_only' => __( 'Please upload a PNG image.', 'jckpc' ),
					),
				);

				wp_localize_script( 'jckpc-script', 'jckpc_vars', $vars );
			}
		} else {
			global $post;

			if ( ! $post ) {
				return;
			}

			if ( 'product' === $post->post_type ) {
				$configurator_enabled = Iconic_PC_Product::is_configurator_enabled( $post->ID );
			} else {
				$configurator_enabled = (
					has_shortcode( $post->post_content, 'product_page' ) ||
					has_shortcode( $post->post_content, 'iconic-wpc-gallery' ) ||
					Iconic_PC_Blocks::get_single_product_block_product_id( $post )
				);
			}

			/**
			 * Filter: enqueue front-end assets regardless of whether
			 * a product has product configurator enabled or not.
			 *
			 * @filter iconic_pc_enqueue_frontend_assets
			 * @since 1.7.0
			 * @param bool    $enqueue Whether to enqueue the assets, or not.
			 * @param WP_Post $post    WP_Post object.
			 */
			if ( ! $configurator_enabled && ! apply_filters( 'iconic_pc_enqueue_frontend_assets', false, $post ) ) {
				return;
			}

			if ( Iconic_PC_Blocks::get_single_product_block_product_id( $post ) ) {
				wp_enqueue_script( 'zoom' );
				wp_enqueue_script( 'flexslider' );
				wp_enqueue_script( 'wc-single-product' );
			}

			wp_enqueue_style( 'jckpc_styles', ICONIC_PC_URL . 'assets/frontend/css/main' . $min . '.css', array(), self::$version );

			$this->load_file( $this->slug . '-script', '/assets/frontend/js/main' . $min . '.js', true );

			$vars = apply_filters(
				'iconic_pc_localize_script',
				array(
					'ajaxurl'        => WC()->ajax_url(),
					'nonce'          => wp_create_nonce( 'jckpc_ajax' ),
					'settings'       => $this->settings,
					'preload_layers' => $jckpc->settings['general_cache_preload_layers'],
				)
			);

			wp_localize_script( $this->slug . '-script', $this->slug, $vars );
		}
	}

	/**
	 * Helper function to enqueue styles/scripts.
	 *
	 * @param string $name      Name.
	 * @param string $file_path File path.
	 * @param bool   $is_script Is script.
	 * @param array  $deps      Dependencies.
	 * @param bool   $in_footer In footer.
	 */
	private function load_file( $name, $file_path, $is_script = false, $deps = array( 'jquery' ), $in_footer = true ) {
		$url  = plugins_url( $file_path, __FILE__ );
		$file = plugin_dir_path( __FILE__ ) . $file_path;

		if ( file_exists( $file ) ) {
			if ( $is_script ) {
				wp_register_script( $name, $url, $deps, self::$version, $in_footer ); // Depends on jquery.
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url, array(), self::$version );
				wp_enqueue_style( $name );
			}
		}
	}

	/**
	 * Remove filters/hooks from anonymous classes.
	 *
	 * @param string $hook_name   Hook name.
	 * @param string $class_name  Class name.
	 * @param string $method_name Method name.
	 * @param int    $priority    Priority.
	 */
	public function remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
		global $wp_filter;

		// Take only filters on right hook name and priority.
		if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
			return;
		}

		// Loop on filters registered.
		foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method).
			if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
				// Test if object is a class, class and method is equal to param.
				if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) === $class_name && $filter_array['function'][1] === $method_name ) {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}
	}

	/**
	 * Sort one array by another.
	 *
	 * @param array $array       First array.
	 * @param array $order_array Second array.
	 *
	 * @return array
	 */
	public static function sort_array_by_array( array $array, array $order_array ) {
		$ordered = array();

		// Compare order.
		foreach ( $order_array as $key ) {
			if ( array_key_exists( $key, $array ) ) {
				$ordered[ $key ] = $array[ $key ];
				unset( $array[ $key ] );

				continue;
			}

			$key = str_replace( 'jckpc-', '', $key );

			if ( array_key_exists( $key, $array ) ) {
				$ordered[ $key ] = $array[ $key ];
				unset( $array[ $key ] );

				continue;
			}
		}

		return $ordered + $array;
	}

	/**
	 * Thumbnail: Change thumbnail in order emails
	 *
	 * @param string     $thumbnail_html Thumbnail html.
	 * @param WC_Product $item           Order item.
	 *
	 * @return string
	 */
	public function order_item_thumbnail( $thumbnail_html, $item ) {
		$meta = Iconic_PC_Order_Item::get_meta( $item );

		$args = apply_filters(
			'iconic_email_order_item_thumbnail',
			array(
				'product_id' => $item['product_id'],
				'variation'  => $meta,
				'image_size' => array( 32, 32 ),
				'wrap'       => true,
			)
		);

		$thumbnail_html = $this->cart_thumbnail( $thumbnail_html, $args );

		return $thumbnail_html;
	}

	/**
	 * Thumbnail: Change thumbnail in the admin order view.
	 *
	 * @param string                $thumbnail_html Thumbnail html.
	 * @param int                   $item_id        Order item ID.
	 * @param WC_Order_Item_Product $item           WC_Order_Item_Product instance.
	 *
	 * @return string
	 */
	public function admin_order_item_thumbnail( $thumbnail_html, $item_id, $item ) {
		$meta = Iconic_PC_Order_Item::get_meta( $item );

		$args = apply_filters(
			'iconic_admin_order_item_thumbnail',
			array(
				'product_id' => $item['product_id'],
				'variation'  => $meta,
				'image_size' => array( 32, 32 ),
				'wrap'       => true,
			)
		);

		$thumbnail_html = $this->cart_thumbnail( $thumbnail_html, $args );

		return $thumbnail_html;
	}

	/**
	 * Extract attribute value pairs from array.
	 *
	 * @param array $array Array to extract attribute value pairs from.
	 *
	 * @return array
	 */
	public function extract_att_value_pairs( $array ) {
		if ( ! $array || empty( $array ) ) {
			return array();
		}

		$pairs = array();

		foreach ( $array as $key => $value ) {
			if ( 'attribute_' === substr( $key, 0, 10 ) || 'pa_' === substr( $key, 0, 3 ) ) {
				$pairs[ $key ] = is_array( $value ) ? $value[0] : $value;
			}
		}

		return $pairs;
	}

	/**
	 * Admin: Add attribute term fields
	 */
	public function add_attribute_term_fields() {
		$attributes = wc_get_attribute_taxonomies();

		if ( ! $attributes ) {
			return;
		}

		foreach ( $attributes as $attribute ) {
			add_action( sprintf( 'pa_%s_add_form_fields', $attribute->attribute_name ), array( $this, 'output_attribute_term_fields' ), 100, 2 );
			add_action( sprintf( 'pa_%s_edit_form', $attribute->attribute_name ), array( $this, 'output_attribute_term_fields' ), 100, 2 );
			add_action( sprintf( 'create_pa_%s', $attribute->attribute_name ), array( $this, 'save_attribute_term_fields' ) );
			add_action( sprintf( 'edited_pa_%s', $attribute->attribute_name ), array( $this, 'save_attribute_term_fields' ) );
		}
	}

	/**
	 * Admin: Add attribute term fields.
	 *
	 * @param int|bool $term The concrete term.
	 */
	public function output_attribute_term_fields( $term = false ) {
		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_STRING );
		$tag_id   = filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_STRING );

		if ( empty( $taxonomy ) || empty( $tag_id ) ) {
			return;
		}

		$field_name   = 'jckpc_attribute_image';
		$field_id     = 'jckpc-attribute-image';
		$field_label  = __( 'Default Image', 'jckpc' );
		$value        = get_term_meta( $tag_id, 'jckpc_default_image', true );
		$img          = $value ? wp_get_attachment_image( $value, 'thumbnail' ) : false;
		$upload_class = $value ? sprintf( '%1$s__upload %1$s__upload--edit', $field_id ) : sprintf( '%s__upload', $field_id );

		$fields = array(
			array(
				'label'       => sprintf(
					'<label for="%s-field">%s</label>',
					$field_id,
					$field_label
				),
				'field'       => sprintf(
					'<div class="%1$s">
                        <div class="%1$s__preview">%2$s</div>
                        <input id="%1$s-field" type="hidden" name="%3$s" value="%4$s" class="%1$s__field regular-text">

                        <a href="javascript: void(0);" class="%1$s__button %9$s" title="%5$s" id="upload-%1$s" data-title="%5$s" data-button-text="%6$s"><span class="dashicons dashicons-edit"></span><span class="dashicons dashicons-plus"></span></a>

                        <a href="javascript: void(0);" class="%1$s__button %1$s__remove" title="%7$s" %8$s><span class="dashicons dashicons-no"></span></a>
                    </div>',
					$field_id,
					$img,
					$field_name,
					$value,
					__( 'Upload/Add Image', 'iconic-was' ),
					__( 'Insert Image', 'iconic-was' ),
					__( 'Remove Image', 'iconic-was' ),
					$img ? false : 'style="display: none;"',
					$upload_class
				),
				'description' => '',
			),
		);

		$allowed_html_tags = array(
			'input' => array(
				'type'  => array(),
				'name'  => array(),
				'value' => array(),
				'class' => array(),
				'id'    => array(),
			),
			'a'     => array(
				'href'             => array(),
				'title'            => array(),
				'name'             => array(),
				'value'            => array(),
				'class'            => array(),
				'id'               => array(),
				'data-title'       => array(),
				'data-button-text' => array(),
			),
			'span'  => array(
				'class' => array(),
			),
			'div'   => array(
				'class' => array(),
			),
			'img'   => array(
				'height'  => array(),
				'width'   => array(),
				'src'     => array(),
				'class'   => array(),
				'alt'     => array(),
				'loading' => array(),
			),
		);

		$is_edit_page = is_object( $term );

		if ( $fields ) {
			if ( $is_edit_page ) {
				printf( '<h3>%s</h3>', esc_attr( __( 'Configurator Options', 'iconic-was' ) ) );

				echo "<table class='form-table'>";
				echo '<tbody>';

				foreach ( $fields as $field ) {
					echo "<tr class='form-field'>";
					echo sprintf( '<th scope="row">%s</th>', wp_kses_post( $field['label'] ) );
					echo '<td>';
					echo wp_kses( $field['field'], $allowed_html_tags );
					echo wp_kses_post( $field['description'] );
					echo '</td>';
					echo '</tr>';
				}

				echo '</tbody>';
				echo '</table>';
			} else {
				foreach ( $fields as $field ) {
					echo "<div class='form-field'>";
					echo wp_kses_post( $field['label'] );
					echo wp_kses( $field['field'], $allowed_html_tags );
					echo wp_kses_post( $field['description'] );
					echo '</div>';
				}
			}
		}
	}

	/**
	 * Admin: Save fields for product categories.
	 *
	 * @param int $term_id ID of the term we are saving.
	 */
	public function save_attribute_term_fields( $term_id ) {
		$attribute_image = filter_input( INPUT_POST, 'jckpc_attribute_image', FILTER_SANITIZE_STRING );

		if ( empty( $attribute_image ) ) {
			delete_metadata( 'term', $term_id, 'jckpc_default_image' );
		}

		if ( ! $attribute_image ) {
			return;
		}

		if ( ! $this->validate_image( $attribute_image, 'png' ) ) {
			return;
		}

		update_term_meta( $term_id, 'jckpc_default_image', $attribute_image );
	}

	/**
	 * Helper: Validate image
	 *
	 * @param int    $image_id  Image id.
	 * @param string $file_type File type.
	 *
	 * @return bool
	 */
	public function validate_image( $image_id, $file_type ) {
		if ( empty( $image_id ) ) {
			return true;
		}

		$image_src = wp_get_attachment_image_src( $image_id );

		if ( ! $image_src ) {
			$this->notices->add_notice( 'error', __( 'Sorry, an issue occurred when attaching the image you selected. Please try again.', 'jckpc' ) );

			return false;
		}

		$image_src = explode( '?', $image_src[0] ); // Remove query string if present (#2243).
		$filetype  = wp_check_filetype( $image_src[0] );

		if ( strtolower( $filetype['ext'] ) !== strtolower( $file_type ) ) {
			// Translators: %s is the file type.
			$this->notices->add_notice( 'error', sprintf( __( 'Please make sure your image is a %s file.', 'jckpc' ), $file_type ) );

			return false;
		}

		return true;
	}

	/**
	 * Show image in emails.
	 *
	 * @param array $args Array of args.
	 *
	 * @return array
	 */
	public static function email_order_items_args( $args ) {
		$args['show_image'] = true;

		return $args;
	}

	/**
	 * Delete cached images in folder `wp-content/uploads/jckpc-uploads`
	 *
	 * @return void
	 */
	public function delete_cached_images() {
		$glob_path = sprintf( '%s/*.png', $this->uploads_path );
		$files     = glob( $glob_path );

		foreach ( $files as $file ) {
			if ( ! is_file( $file ) ) {
				continue;
			}

			unlink( $file );
		}

		/**
		 * Hook: after deleting cached images.
		 * 
		 * @param string $context Either `all` or the imgName value.
		 */
		do_action( 'iconic_pc_after_delete_from_cache', 'all' );
	}

	/**
	 * Delete all transient.
	 */
	public function delete_all_transient() {
		global $wpdb, $jckpc;
	
		$query = $wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE `option_name` LIKE %s",
			'_transient_' . $wpdb->esc_like($jckpc::$transient_prefix) . '%'
		);
	
		return $wpdb->query($query);
	}
	
	/**
	 * Declare HPOS compatiblity.
	 *
	 * @since 1.8.8
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}

	/**
	 * Instantiate a single instance of our plugin.
	 *
	 * @return Iconic_PC
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the DI container.
	 *
	 * @return ContainerInterface
	 */
	public function container() {
		return $this->container;
	}
}

$iconic_wpc = Iconic_PC::instance();
$jckpc      = $iconic_wpc; // Backwards compatibility.
