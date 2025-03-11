<?php
/**
 * register Form
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php wc_print_notices(); ?>
<div class="auth__container set-division max-width--med-tab">
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="row row-sm--fluid flex-justify-between" id="customer_login">

	<div class="col-md-5 col-xs-12 col-sm--fluid auth__section">

		<h2 class="auth__title heading heading--small"><?php esc_html_e( 'New Register', 'zoa' ); ?></h2>
		<p class="form__description p4"><?php esc_html_e( 'Please create your account here.', 'zoa' ); ?></p>

		<form method="post" class="woocommerce-form woocommerce-form-register register auth__form">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

			<div class="form-row required">
			<div class="field-wrapper">
					<label class="form-row__label light-copy" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</div>
			</div>

			<?php endif; ?>

			<div class="form-row required">
			<div class="field-wrapper">
				<label class="form-row__label light-copy" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</div>
			</div>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<div class="form-row required">
			<div class="field-wrapper">
					<label class="form-row__label light-copy" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</div>
			</div>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<div class="form-row form-row-button">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="button button--primary button--full" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Create Account', 'zoa' ); ?></button>
			</div>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div><!--/auth__container-->