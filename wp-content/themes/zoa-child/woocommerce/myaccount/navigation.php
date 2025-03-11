<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$current_url = ( isset($_SERVER['HTTPS']) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$dashboard_url = get_permalink( get_option('woocommerce_myaccount_page_id'));
$rank_and_amount = mr_get_member_rank(get_current_user_id(), true);
$rank = $rank_and_amount['rank'];
?>

<div class="account__sidebar <?php echo ( $dashboard_url == $current_url ) ? 'col-12 col-lg-4 col-md-3' : 'col-12 col-lg-2 col-md-3'; ?>">
    <h2 class="account__nav__heading heading heading--xlarge serif flex-justify-between flex-align-center toggle_act toggle_sp" data-toggle="account__nav"><?php esc_html_e('My Account', 'zoa'); ?>&nbsp;<span class="account_rank stl_<?php echo $rank; ?>"><?php echo '<span class="rank_you">現在の会員ランク</span><span class="rank_name rank_' . $rank . '">' . $rank . '</span>'; ?></span></h2>
	<nav class="account__nav <?php if($dashboard_url == $current_url){ ?>account__nav--landing<?php } ?>">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php if ($endpoint !== 'customer-logout') : ?>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="account__nav__link link <?php echo wc_get_account_menu_item_classes( $endpoint ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php else : ?>
				<?php if ( $dashboard_url == $current_url ) : ?>
					<div class="account__nav__item account__nav__link account__nav__logout">
						<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="cta--underlined"><?php echo esc_html( $label ); ?></a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
