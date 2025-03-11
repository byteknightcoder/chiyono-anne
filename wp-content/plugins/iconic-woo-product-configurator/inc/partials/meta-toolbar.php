<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$disable_inputs = Iconic_PC_Helpers::maybe_disable_layer_inputs();

if ( ! $disable_inputs ) {
	?>
	<div class="jckpc-meta-toolbar">

		<button disabled="true" type="button" id="jckpc-add-static-layer" class="button jckpc-meta-toolbar__button">
			<?php esc_html_e( 'Add Static Layer', 'jckpc' ); ?>
		</button>

		<div class="jckpc-meta-toolbar__actions">

			<?php
			woocommerce_wp_checkbox(
				array(
					'id'            => 'jckpc_enabled',
					'wrapper_class' => 'jckpc_toggle',
					'label'         => 'Configurator enabled?',
					'description'   => '',
					'value'         => get_post_meta( $post->ID, 'jckpc_enabled', true ),
				)
			);
			?>

			<input type="submit" name="save" id="jckpc-publish" class="button button-primary button-large" value="Update">

		</div>

	</div>
	<?php
}
