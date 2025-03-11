<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.13
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly
?>

<div class="yith-wcwl-share share_wish_link">
	<span class="sharing-tools">
		<span class="sharing-tools__layer-1">
			<span class="link-icon -no-anim -share cta">
				<?php echo $share_title ?>
			</span>
		</span>
		<span class="sharing-tools__layer-2">
			<ul class="social-icons-lists icons-background-none wishlist-share">
        <?php if( $share_facebook_enabled ): ?>
            <li class="social-icons-list-item">
                <a target="_blank" class="social-icon facebook" href="https://www.facebook.com/sharer.php?s=100&amp;p%5Btitle%5D=<?php echo $share_link_title ?>&amp;p%5Burl%5D=<?php echo urlencode( $share_link_url ) ?>" title="<?php _e( 'Facebook', 'yith-woocommerce-wishlist' ) ?>"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_twitter_enabled ): ?>
            <li class="social-icons-list-item">
                <a target="_blank" class="social-icon twitter" href="https://twitter.com/share?url=<?php echo urlencode( $share_link_url ) ?>&amp;text=<?php echo $share_twitter_summary ?>" title="<?php _e( 'Twitter', 'yith-woocommerce-wishlist' ) ?>"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_pinterest_enabled ): ?>
            <li class="social-icons-list-item">
                <a target="_blank" class="social-icon pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( $share_link_url ) ?>&amp;description=<?php echo $share_summary ?>&amp;media=<?php echo $share_image_url ?>" title="<?php _e( 'Pinterest', 'yith-woocommerce-wishlist' ) ?>" onclick="window.open(this.href); return false;"></a>
            </li>
        <?php endif; ?>

        <?php if( $share_googleplus_enabled ): ?>
            <li class="social-icons-list-item">
                <a target="_blank" class="social-icon googleplus" href="https://plus.google.com/share?url=<?php echo urlencode( $share_link_url ) ?>&amp;title=<?php echo $share_link_title ?>" title="<?php _e( 'Google+', 'yith-woocommerce-wishlist' ) ?>" onclick='javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'></a>
            </li>
        <?php endif; ?>

        <?php if( $share_email_enabled ): ?>
            <li class="social-icons-list-item">
                <a class="social-icon email" href="mailto:?subject=<?php echo urlencode( apply_filters( 'yith_wcwl_email_share_subject', $share_link_title ) )?>&amp;body=<?php echo apply_filters( 'yith_wcwl_email_share_body', urlencode( $share_link_url ) ) ?>&amp;title=<?php echo $share_link_title ?>" title="<?php _e( 'Email', 'yith-woocommerce-wishlist' ) ?>"><span class="socicon socicon-mail"></span></a>
            </li>
        <?php endif; ?>
    </ul>
		</span>
	</span>
</div>