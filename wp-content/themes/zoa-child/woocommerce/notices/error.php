<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<div class="woocommerce-notice-wrapper">
    <ul class="woocommerce-error woo-notice-box" role="alert">
        <?php foreach ( $notices as $notice ): ?>
            <li <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                <?php 
                    if (isset($_REQUEST['digt_countrycode']) && !empty($_REQUEST['digt_countrycode']) && isset($_REQUEST['mobile/email']) && !empty($_REQUEST['mobile/email']) && $_REQUEST['digt_countrycode'] !== '+' && preg_match('#[0-9]#', $notice['notice']) && !is_email($_REQUEST['mobile/email'])) {
                        echo '<strong>エラー</strong>: 携帯番号 <strong>' . trim($_REQUEST['mobile/email']) . '</strong> のパスワードが間違っています。 <a href="' . home_url('/my-account/lost-password/') . '">パスワードをお忘れですか ?</a>';
                    } else {
                        echo wc_kses_notice( $notice['notice'] );
                    }
                ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>