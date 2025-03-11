<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();
$user->last_name = $user->last_name ? $user->last_name : get_user_meta($user_id, 'billing_last_name', true);
$user->first_name = $user->first_name ? $user->first_name : get_user_meta($user_id, 'billing_first_name', true);

do_action( 'woocommerce_before_edit_account_form' ); ?>


<form class="woocommerce-EditAccountForm edit-account" action="" method="post" novalidate <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="personal-info row flex-justify-between">
		<div class="personal-info__edit-form col-lg-5 col-md-6 col-12">
		<h3 class="form__copy heading heading--xsmall"><?php esc_html_e( 'Update your personal details', 'zoa' ); ?></h3>
		<div class="form-row">
			<div class="field-wrapper">
				<label class="form-row__label" for="account_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" placeholder="" />
			</div>
		</div>
		<div class="form-row">
			<div class="field-wrapper">
				<label class="form-row__label" for="account_last_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" placeholder="" />
			</div>
		</div>

		<div class="form-row">
			<div class="field-wrapper">
			<label class="form-row__label" for="account_display_name"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
			</div>
		</div>
	
		<div class="form-row">
			<div class="field-wrapper">
			<label class="form-row__label" for="account_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
			</div>
		</div>
		
		<?php do_action( 'woocommerce_edit_account_form_below_email' ); ?>
		
	</div>
	

	<div class="personal-info__password-form col-lg-5 col-md-6 col-12">
		<h3 class="form__copy heading heading--xsmall"><?php esc_html_e( 'Password change', 'woocommerce' ); ?></h3>

		<div class="form-row">
			<div class="field-wrapper">
			<label class="form-row__label" for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
			</div>
		</div>
		<div class="form-row">
			<div class="field-wrapper">
			<label class="form-row__label" for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
			</div>
		</div>
		<div class="form-row">
			<div class="field-wrapper">
			<label class="form-row__label" for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
			</div>
		</div>
		<?php 
			// Check If ACF Plugin is Active
			// Check If BOOKING_FORM_ID defined
			if ( class_exists('ACF') && defined( 'BOOKING_FORM_ID' ) ) {
				// show q_04 fields
				$field_group_fields = acf_get_fields(BOOKING_FORM_ID);
				$save_fields = array();
				foreach ($field_group_fields as $field) {
					loop_to_get_sub_field($field, $save_fields);
				}
				foreach ($save_fields as $field) {
					if ( 'q_04' == $field['parent_name'] ) : ?>
						<div class="form-row">
							<div class="field-wrapper">
							<?php
								$field['value'] = (array) get_user_meta($user_id, $field['name'], true);
								acf_render_field_wrap( $field, 'div', 'label');
							?>
							</div>
						</div>
					<?php endif;
				}
			}
		?>
		<?php do_action( 'woocommerce_edit_account_form_below_password' ); ?>
	</div>
	</div><!--/personal-info-->

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>