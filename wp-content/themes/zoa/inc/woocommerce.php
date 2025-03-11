<?php

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

$wc = glob( get_template_directory() . '/inc/woocommerce/*.php' );

foreach ( $wc as $key ) {
	if ( file_exists( $key ) ) {
		require_once $key;
	}
}

?>
