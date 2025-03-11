<?php

namespace YAYDP\License;

defined( 'ABSPATH' ) || exit;

class Core_Plugin {

	public static function get( $name ) {
		$data = array(
			'path'        => YAYDP_PLUGIN_PATH,
			'url'         => YAYDP_PLUGIN_URL,
			'basename'    => YAYDP_PLUGIN_BASENAME,
			'version'     => YAYDP_VERSION,
			'slug'        => 'yaypricing',
			'link'        => 'https://yaycommerce.com/yaypricing-woocommerce-dynamic-pricing-and-discounts/',
			'download_id' => '14440',
		);

		if ( isset( $data[ $name ] ) ) {
			return $data[ $name ];
		}
		return null;
	}
}
