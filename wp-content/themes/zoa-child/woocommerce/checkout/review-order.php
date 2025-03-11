<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */
defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-checkout-review-order-table">
    <div class="checkout-mini-cart">
        <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
            <?php
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
            ?>
                <div class="minicart__product <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <div class="mini-product--group">
                        <?php
                        // Display product thumbnail
                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                        if ( ! $product_permalink ) {
                            echo wp_kses_post( $thumbnail );
                        } else {
                            printf( '<a class="mini-product__link" href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
                        }
                        ?>
                        <div class="mini-product__info">
                            <?php
                            // Product name with link
                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<p class="mini-product__item mini-product__name p5"><a href="%s" class="link">%s</a></p>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                            ?>
                            <div class="mini-product__item mini-product__attribute">
                                <span class="label"><?php _e( 'Quantity:', 'woocommerce' ); ?></span>
                                <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', '<span class="value">' . sprintf( '%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?>
                            </div>
                            <?php
                            // Display item metadata (e.g., variations)
                            echo wc_get_formatted_cart_item_data( $cart_item );
                            ?>
                            <div class="mini-product__item mini-product__price price">
                                <?php
                                // Display product subtotal
                                echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                                ?>
                            </div>
                            <?php
                            // Custom functionality: delivery date and SKU display
                            if ( isset( $cart_item['product_id'] ) && ! empty( $cart_item['product_id'] ) ) {
                                // Fetch delivery information
                                $deliver_date = get_post_meta( $cart_item['product_id'], 'deliver_date', true );
                                $deliver_date_new = get_post_meta( $cart_item['product_id'], 'from_to', true );
                                $specific_deliver_date = get_post_meta( $cart_item['product_id'], 'specific_deliver_date', true );
                                
                                if ( ( ! empty( $deliver_date_new ) && ( ! empty( $deliver_date_new[0] ) || ! empty( $deliver_date_new[2] ) ) ) || ! empty( $specific_deliver_date ) ) {
                                    $text_dwm = '';
                                    if ( ! empty( $specific_deliver_date ) ) {
                                        $text_date = $specific_deliver_date;
                                    } else {
                                        // Prepare delivery date text
                                        if ( ! empty( $deliver_date_new[0] ) && empty( $deliver_date_new[2] ) ) {
                                            $text_dwm = ( $deliver_date_new[1] == 'months' ) ? 'ヶ月' : ( ( $deliver_date_new[1] == 'weeks' ) ? '週間' : '日' );
                                            $text_date = '注文確定後、約 ' . $deliver_date_new[0] . ' ' . $text_dwm . ' で発送';
                                        } elseif ( ! empty( $deliver_date_new[2] ) && empty( $deliver_date_new[0] ) ) {
                                            $text_dwm = ( $deliver_date_new[3] == 'months' ) ? 'ヶ月' : ( ( $deliver_date_new[3] == 'weeks' ) ? '週間' : '日' );
                                            $text_date = '注文確定後、約 ' . $deliver_date_new[2] . ' ' . $text_dwm . ' 以内に発送';
                                        } elseif ( ! empty( $deliver_date_new[0] ) && ! empty( $deliver_date_new[2] ) ) {
                                            $text_dwm = ( $deliver_date_new[1] == 'months' ) ? 'ヶ月' : ( ( $deliver_date_new[1] == 'weeks' ) ? '週間' : '日' );
                                            $text_dwm_to = ( $deliver_date_new[3] == 'months' ) ? 'ヶ月' : ( ( $deliver_date_new[3] == 'weeks' ) ? '週間' : '日' );
                                            
                                            if ( $deliver_date_new[1] == $deliver_date_new[3] && $deliver_date_new[1] == 'days' ) {
                                                $text_date = '受注から約 ' . $deliver_date_new[0] . '〜' . $deliver_date_new[2] . ' 営業日以内に発送';
                                            } else {
                                                $text_date = '注文確定後、約 ' . $deliver_date_new[0] . ' ' . $text_dwm . '〜' . $deliver_date_new[2] . ' ' . $text_dwm_to . ' 以内に発送';
                                            }
                                        }
                                    }
                                    
                                    // Check for express shipping
                                    $tags = get_the_terms( $cart_item['product_id'], 'product_tag' );
                                    $tag_arr = ( $tags && ! is_wp_error( $tags ) ) ? wp_list_pluck( $tags, 'name' ) : array();
                                    
                                    if ( in_array( 'Express Shipping', $tag_arr ) && ! empty( $deliver_date ) ) {
                                        $text_date = '注文確定後、' . $deliver_date;
                                    }
                                    ?>
                                    <div class="mini-product__item mini-product__deliver_date"><?php _e( '納期', 'zoa' ); ?>：<?php echo $text_date; ?></div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="mini-product__item mini-product__deliver_date"><?php _e( '納期', 'zoa' ); ?>：<?php _e( '受注から約3ヶ月', 'zoa' ); ?></div>
                                    <?php
                                }
                            }

                            // Display SKU
                            if ( ! empty( $_product->get_sku() ) ) {
                                ?>
                                <div class="mini-product__item mini-product__id light-copy">
                                    <span class="label"><?php _e( 'Product ID #', 'zoa' ); ?></span>
                                    <span class="value"><?php echo $_product->get_sku(); ?></span>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>
    </div><!--/checkout-mini-cart-->

    <div class="checkout-order-totals">
        <div class="order__summary__contents order-totals-table">
            <p class="order__summary__row heading m-no_topmargin heading--small"><?php _e( 'Cart totals', 'woocommerce' ); ?></p>
            <div class="order__summary__row order-subtotal">
                <span class="label"><?php _e( 'Subtotal', 'woocommerce' ); ?></span>
                <span class="value price-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>

            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <div class="order__summary__row cart-discount coupon-knco7ipuol">
                    <span class="label">
                        クーポンを適用の場合、<span style="font-weight: bold;color:red;">キャンペーンによるディスカウントは適用されません</span>ので、｢<span style="font-weight: bold;color:red;">削除</span>｣クリックにて外してください。
                    </span>
                   
                </div>
                <div class="order__summary__row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                    <span class="label"><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                    <span class="value price-amount"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                </div>
                <?php
                // Custom coupon description handling
                $the_coupon = new WC_Coupon( $code );
                $coupon_id = $the_coupon->get_id();
                $subtitle_copn = get_post_meta( $coupon_id, 'kia_subtitle', true );
                if ( ! empty( $coupon->get_description() ) || ! empty( $subtitle_copn ) ) {
                    ?>
                    <div class="order__summary__row description desc_coupon">
                        <?php
                        echo $coupon->get_description();
                        if ( ! empty( $subtitle_copn ) ) {
                            echo '<small>' . $subtitle_copn . '</small>';
                        }
                        ?>
                    </div>
                    <?php
                }

                $amount = get_post_meta( $coupon_id, 'coupon_amount', true );
                $coupon_discount_amount = WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax );
                $res_amount = $amount - $coupon_discount_amount;
                if ( $res_amount > 0 ) {
                    echo '<p class="red_coupon_amount" style="color:red;">' . sprintf( __( 'You still have %s for gift card', 'zoa' ), wc_price( $res_amount ) ) . '</p>';
                }
                ?>
            <?php endforeach; ?>

            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
                <?php wc_cart_totals_shipping_html(); ?>
                <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
            <?php endif; ?>

            <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                <div class="order__summary__row fee">
                    <span class="label"><?php echo esc_html( $fee->name ); ?></span>
                    <span class="value price-amount"><?php wc_cart_totals_fee_html( $fee ); ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                    <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                        <div class="order__summary__row tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                            <span class="label"><?php echo esc_html( $tax->label ); ?></span>
                            <span class="value price-amount"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="order__summary__row tax-total">
                        <span class="label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
                        <span class="value price-amount"><?php wc_cart_totals_taxes_total_html(); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

            <div class="order__summary__totals">
                <div class="order__summary__row order-total">
                    <span class="label"><?php _e( 'Total', 'woocommerce' ); ?></span>
                    <span class="value price-amount bigger order-value"><?php wc_cart_totals_order_total_html(); ?></span>
                </div>
            </div>

            <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
        </div><!--/order-totals-table-->
    </div><!--/checkout-order-totals-->
</div>
