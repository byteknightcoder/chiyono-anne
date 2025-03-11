<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

function zoa_render_size_guide() {
    $post_id = get_option( 'current-size-guide' );
    if ( ! $post_id ) {
        return;
    }
	$size_guide = zoa_get_size_guide_content( $post_id );

	?>
	<button class="size-guide-button js-open-size-guide">Size Guide</button>
	<div class="size-guide">
        <div class="size-guide__wrapper js-size-guide-wrapper">
            <button class="size-guide__close js-close-size-guide">
                <i class="ion-android-close"></i>
                <span class="screen-reader-text">Close</span>
            </button>
            <?php echo $size_guide; ?>
        </div><!-- .size-guide__wrapper -->
	</div><!-- .size-guide -->
	<?php
}
add_action( 'woocommerce_before_add_to_cart_form', 'zoa_render_size_guide', 5 );

function zoa_add_meta_box() {
	add_meta_box(
		'zoa-meta-box',
		__( 'Size Guide options', 'zoa' ),
		'zoa_render_meta_box',
		'size_guide',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'zoa_add_meta_box' );

function zoa_save_meta_box( $post_id ) {

	// Bail if this is an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Bail if the nonce hasn't been set or the nonce is invalid.
	if ( ! isset( $_POST['zoa_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['zoa_meta_box_nonce'], 'zoa_meta_box' ) ) {
		return;
	}

	// Bail if user can't edit this post.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( isset( $_POST['size-guide-checkbox'] ) ) {
		update_post_meta( $post_id, 'size-guide-checkbox', esc_attr( $_POST['size-guide-checkbox'] ) );
		update_option( 'current-size-guide', $post_id );
	} else {
		delete_post_meta( $post_id, 'size-guide-checkbox' );
		update_option( 'current-size-guide', null );
	}
}
add_action( 'save_post', 'zoa_save_meta_box' );
