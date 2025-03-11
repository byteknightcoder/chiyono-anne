<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

function zoa_get_size_guide_content( $post_id ) {
	if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
		$elementor_instance = Elementor\Plugin::instance();
		$content            = $elementor_instance->frontend->get_builder_content_for_display( $post_id );

		return $content;
	}

	$content_post = get_post( $post_id );
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);

	return $content;
}

function zoa_render_meta_box( $post ) {
	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'zoa_meta_box', 'zoa_meta_box_nonce' );

	$current_size_guide    = get_option( 'current-size-guide' );
	$is_current_size_guide = $current_size_guide === $post->ID ? true : false;
	?>
	<label for="size-guide-checkbox">
		<input type="checkbox" id="size-guide-checkbox" name="size-guide-checkbox" <?php checked( $is_current_size_guide, true ); ?>>
		<?php echo esc_html__( 'Display this as the default size guide' ); ?>
	</label>
	<?php if ( ! $is_current_size_guide && $current_size_guide ) : ?>
		<span class="theme-location-set"><?php echo sprintf( '(Currently set to: %s)', get_the_title ( get_option( 'current-size-guide' ) ) ); ?></span>
	<?php
	endif;
}
