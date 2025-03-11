<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php wc_print_notices(); ?>
<div class="auth__container set-division max-width--med-tab">
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="row row-sm--fluid flex-justify-between" id="customer_login">

	<div class="col-md-5 col-xs-12 col-sm--fluid auth__section">

<?php endif; ?>

		<h2 class="auth__title heading heading--small"><?php esc_html_e( 'Sign in', 'zoa' ); ?></h2>
		<p class="form__description p4"><?php esc_html_e( 'Welcome back! If you already have an account with us, please sign in.', 'zoa' ); ?></p>

		<form class="woocommerce-form woocommerce-form-login login auth__form" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>
			<?php
				$default_username='';
				if (! empty( $_POST['mobile/email'] )) {
					$default_username=esc_attr( wp_unslash( $_POST['mobile/email'] ) );
				} elseif (! empty( $_POST['username'] )) {
					$default_username=esc_attr( wp_unslash( $_POST['username'] ) );
				}
			?>
			<div class="form-row required">
			<div class="field-wrapper">
				<label class="form-row__label light-copy" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty($default_username) ) ? $default_username : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</div></div>
			<div class="form-row required">
			<div class="field-wrapper">
				<label class="form-row__label light-copy" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</div></div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="form-row label-inline login-rememberme label-inline form-indent">
				<div class="field-wrapper">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<input class="input-checkbox input-control" name="rememberme" type="checkbox" id="rememberme" value="forever" /> 
				<label class="form-row__inline-label control-label checkbox icon--tick">
					<?php esc_html_e( 'Remember me', 'woocommerce' ); ?>
				</label>
				</div>
			</div>
			
			<div class="form-row form-row-button">
				<button type="submit" class="button button--primary button--full" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</div>
			<div class="align--center"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="cta p6"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a></div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

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