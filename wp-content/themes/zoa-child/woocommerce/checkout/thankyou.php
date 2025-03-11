<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 * 
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
$thanks_message = get_option('woocommerce_order_thanks_message');
// common notice for TBYB
$notice_tbyb_common = notice_tbyb_common();
$notice_tbyb_zoom = notice_tbyb_zoom();
$notice_tbyb_refund = notice_tbyb_refund();
$notice_tbyb_kome01 = notice_tbyb_kome01();
$notice_tbyb_more = notice_tbyb_more();
?>
<div class="woocommerce-order col-md-7 col-xs-12">

    <?php
    if ($order) : 
        
        do_action( 'woocommerce_before_thankyou', $order->get_id() );
        ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

            <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
                <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
                <?php endif; ?>
            </p>

        <?php else : ?>
            <div class="order--checkout__form__section">
                <h2 class="heading heading--xlarge serif"><?php esc_html_e('Thank you for your order!', 'zoa'); ?> - #<?php echo $order->get_order_number(); ?></h2>
            </div>
            <div class="order--checkout__review__section order-thanks-summary">
                <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'woocommerce'), $order); ?></p>

                <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

                    <li class="woocommerce-order-overview__order order">
                        <?php esc_html_e('Order number:', 'woocommerce'); ?>
                        <strong><?php echo $order->get_order_number(); ?></strong>
                    </li>

                    <li class="woocommerce-order-overview__date date">
                        <?php esc_html_e('Date:', 'woocommerce'); ?>
                        <strong><?php echo wc_format_datetime($order->get_date_created()); ?></strong>
                    </li>

                    <?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
                        <li class="woocommerce-order-overview__email email">
                            <?php esc_html_e( 'Email:', 'woocommerce' ); ?>
                            <strong><?php echo $order->get_billing_email(); ?></strong>
                        </li>
                    <?php endif; ?>

                </ul>
            </div><!--/order-thanks-summary-->
            <!--if try fit your size service-->
            <?php
                $ch_is_special_service = get_post_meta($order->get_id(), '_ch_product_fee', true);
                if (isset($ch_is_special_service) && !empty($ch_is_special_service)) {
                    $online_consultation = get_post_meta($order->get_id(), '_ch_online_consultation', true) ? get_post_meta($order->get_id(), '_ch_online_consultation', true) : '';
                    if ( isset($online_consultation) && 'yes' == $online_consultation ) {
                        if ( is_user_logged_in() ) {
                            $rank = mr_get_member_rank(get_current_user_id());
                        if ( 'gold' == $rank['rank'] ) {
                            update_user_meta(get_current_user_id(), 'online_consultation_free', 'yes' );
                        }
                    }
                ?>
                    <!--if including online consultation-->
                    <div id="steps_zoom" class="thanks_message thanks_message__tfys">
                            <div class="instruction_container instruction_container_thanks">
                                <div class="ttl_wrap inst_head">
                                    <div class="ttl_inner">
                                        <h4 class="ttl"><?php esc_html_e('Service Guide', 'zoa'); ?></h4>
                                        <h5 class="subttl subttl_ja"><span class="fw_400">サービスご利用の流れ</span></h5>
                                    </div>
                                </div>
                                <div class="inst_body">
                                    <div class="responsive-slick grids grids_style02">
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-01.png" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 01', 'zoa'); ?></p>
                                            <p>ご注文確認メールが届きますので、ご確認ください。そのメール内の「<strong>ご予約はこちら</strong>」のリンクをクリックしてください。</p>
                                        </div>
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-02.png" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 02', 'zoa'); ?></p>
                                            <p>予約サービス名「<strong>オンラインコンサルテーションのご予約</strong>」が選択されたカレンダーが表示されますので、そちらから日程と時間をご指定いただき、予約を完了してください。</p>
                                        </div>
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-03.png" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 03', 'zoa'); ?></p>
                                            <p>ご予約完了のメールが届きますので、そちらにて<strong><span class="underline">ご予約日、時間にお間違いがないか</span>、必ずご確認ください</strong>。マイアカウントページ内の「<a class="underline fw_700" href="<?php echo home_url( 'my-account/appointment' ); ?>">アポイントメント履歴</a>」からもご確認いただけます。</p>
                                        </div>
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-04.png" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 04', 'zoa'); ?></p>
                                            <p>お客様のご予約を確認した後、<strong>ご予約日の5営業日前にT.B.Y.Bキットを発送</strong>いたします。</p>
                                            <p>発送完了メールにて、<strong>ZOOM招待URL</strong>が記載されておりますので、当日まで大切にお控えください。</p>
                                        </div>
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-05.png?20210729" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 05', 'zoa'); ?></p>
                                            <p>ZOOMにて千代乃とオンラインコンサルテーションを行います。</p>
                                            <p>事前の準備につきましては<a class="underline fw_700" href="https://chiyono-anne.com/guide-tbyb/#elementor-tab-title-8612" target="_blank">「オンラインコンサルテーション事前準備」</a>をご参照ください。</p>
                                        </div>
                                        <div class="grid">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-06.png?20210729" alt="">
                                            <p class="p_en upper"><?php esc_html_e('Step 06', 'zoa'); ?></p>
                                            <p>T.B.Y.Bキットは<strong>お手元に届いた日から10日間以内に返却</strong>手続きをお願いいたします。（T.B.Y.Bキットに<strong>同封された着払いの伝票を必ずご利用</strong>ください）</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="inst_foot">
                                    <div class="inner">
                                        <!-- T.B.Y.Bサービスに関するご注意点 -->
                                        <p class="p_ttl"><?php echo notice_tbyb_common_p_ttl(); ?></p>
                                        <?php echo $notice_tbyb_common; ?>
                                    </div>
                                    <div class="inner">
                                        <!-- オンラインコンサルテーションに関するご注意点 -->
                                        <p class="p_ttl"><?php echo notice_tbyb_zoom_p_ttl(); ?></p>
                                        <?php echo $notice_tbyb_zoom; ?>
                                    </div>
                                    <!--Common for both-->
                                    <div class="inner">
                                        <!-- ※1 サンプル保証金の返金条件 -->
                                        <p class="p_ttl"><?php echo notice_tbyb_refund_p_ttl(); ?></p>
                                        <?php echo $notice_tbyb_refund; ?>
                                    </div>
                                    <div class="inner">
                                        <!-- ※2 T.B.Y.B.キット損害金・返却遅延金について-->
                                        <p class="p_ttl"><?php echo notice_tbyb_kome01_p_ttl(); ?></p>
                                        <?php echo $notice_tbyb_kome01; ?>
                                    </div>
                                    <div class="inner">
                                        <p class="p_more"><?php echo $notice_tbyb_more; ?></p>
                                    </div>
                                    <!--/Common for both-->
                                </div>
                            </div>
                    </div>
                    <!--end if including online consultation-->
                <?php } else { ?>
                    <div id="steps_self" class="thanks_message thanks_message__tfys">
                        <div class="instruction_container instruction_container_thanks">
                            <div class="ttl_wrap inst_head">
                                <div class="ttl_inner">
                                    <h4 class="ttl"><?php esc_html_e('Service Guide', 'zoa'); ?></h4>
                                    <h5 class="subttl subttl_ja"><span class="fw_400">サービスご利用の流れ</span></h5>
                                </div>
                            </div>
                            <div class="inst_body">
                                <div class="responsive-slick">
                                    <div class="grid">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-04.png" alt="">
                                        <p class="p_en upper"><?php esc_html_e('Step 01', 'zoa'); ?></p>
                                        <p>本日から3営業日以内にインストラクションとフィットサンプルブラを含むT.B.Y.Bキットがアトリエより発送されます。</p>
                                    </div>
                                    <div class="grid">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-07.png" alt="">
                                        <p class="p_en upper"><?php esc_html_e('Step 02', 'zoa'); ?></p>
                                        <p>お手元に届いたT.B.Y.Bキットに同封されたインストラクションに沿ってご自身でフィッティングしていただきます。</p>
                                    </div>
                                    <div class="grid">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tbyb/thanksSteps-06.png?20210729" alt="">
                                        <p class="p_en upper"><?php esc_html_e('Step 03', 'zoa'); ?></p>
                                        <p>T.B.Y.Bキットは<strong>お手元に届いた日から10日間以内に返却</strong>手続きをお願いいたします。（T.B.Y.Bキットに<strong>同封された着払いの伝票を必ずご利用</strong>ください）</p>
                                    </div>
                                </div>
                            </div>
                            <div class="inst_foot">
                                <div class="inner">
                                    <!-- T.B.Y.Bサービスに関するご注意点 -->
                                    <p class="p_ttl"><?php echo notice_tbyb_common_p_ttl(); ?></p>
                                    <?php echo $notice_tbyb_common; ?>
                                </div>
                                <!--Common for both-->
                                <div class="inner">
                                    <!-- ※1 サンプル保証金の返金条件 -->
                                    <p class="p_ttl"><?php echo notice_tbyb_refund_p_ttl(); ?></p>
                                    <?php echo $notice_tbyb_refund; ?>
                                </div>
                                <div class="inner">
                                    <!-- ※2 T.B.Y.B.キット損害金・返却遅延金について-->
                                    <p class="p_ttl"><?php echo notice_tbyb_kome01_p_ttl(); ?></p>
                                    <?php echo $notice_tbyb_kome01; ?>
                                </div>
                                <div class="inner">
                                    <p class="p_more"><?php echo $notice_tbyb_more; ?></p>
                                </div>
                                <!--/Common for both-->
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!--/if try fit your size service-->
                <?php
            }
            ?>
            <?php if ($thanks_message) : ?>
                <div class="thanks_message">
                    <?php echo nl2br($thanks_message); ?>
                </div>
            <?php endif; ?>

            <div class="order--checkout__review__section order-payment-instruments">
                <h3 class="order--checkout__form__title heading heading--small"><?php _e('Payment method', 'zoa'); ?></h3>
                <div class="readonly-address serif">
                    <div class="readonly-address__contents">
                        <?php if ($order->get_payment_method_title()) : ?>
                            <div class="payment-type"><?php echo wp_kses_post($order->get_payment_method_title()); ?></div>
                        <?php endif; ?>
                        <div class="payment-amount">
                            <span class="label"><?php esc_html_e('Amount', 'zoa'); ?></span>
                            <span class="value"><?php echo $order->get_formatted_order_total(); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>


        <?php do_action('woocommerce_thankyou', $order->get_id()); ?>

    <?php else : ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', __('Thank you. Your order has been received.', 'woocommerce'), null); ?></p>

    <?php endif; ?>
</div><!--/woocommerce-order-->
