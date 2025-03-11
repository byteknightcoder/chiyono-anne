<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

add_action( 'woocommerce_product_meta_end', 'zoa_product_sharing', 5 );
function zoa_product_sharing() {
	if ( ! get_theme_mod( 'show_social_share', true ) ) return;
	global $product;
	$id       = $product->get_id();
	$url      = get_permalink( $id );
	$title    = get_the_title( $id );
	$img_id   = $product->get_image_id();
	$img      = wp_get_attachment_image_src( $img_id, 'full' );
	$tags     = get_the_terms( $id, 'product_tag' );
	$tag_list = '';

	if ( $tags && ! is_wp_error( $tags ) ) {
		$tag_list = implode( ', ', wp_list_pluck( $tags, 'name' ) );
	}
	?>

	<span class="theme-social-icon p-shared">
		<span><?php esc_html_e( 'Share:', 'zoa' ); ?></span>
		<a
			href="<?php echo esc_url_raw( '//facebook.com/sharer.php?u=' . urlencode( $url ) ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			target="_blank"
		>
		</a>
		<a
			href="<?php echo esc_url_raw( '//twitter.com/intent/tweet?url=' . urlencode( $url ) . '&text=' . urlencode( $title ) . '&hashtags=' . urlencode( $tag_list ) ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			target="_blank"
		>
		</a>
		<a
			href="<?php echo esc_url_raw( '//linkedin.com/shareArticle?mini=true&url=' . urlencode( $url ) . '&title=' . urlencode( $title ) ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			target="_blank"
		>
		</a>
		<a
			href="<?php echo esc_url_raw( '//pinterest.com/pin/create/button/?url=' . urlencode( $url ) . '&image_url=' . urlencode( $img[0] ) . '&description=' . urlencode( $title ) ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			target="_blank"
		>
		</a>
		<a
			href="<?php echo esc_url_raw( '//plus.google.com/share?url=' . urlencode( $url ) . '&text=' . urlencode( $title ) ); ?>"
			title="<?php echo esc_attr( $title ); ?>"
			target="_blank"
		>
		</a>
	</span>

	<?php
}
