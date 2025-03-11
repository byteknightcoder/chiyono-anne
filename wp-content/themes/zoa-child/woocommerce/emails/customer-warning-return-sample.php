<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('ABSPATH')) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email);
?>
<p>返却期限日が近づいて参りましたが返送手続きはお済みでしょうか？</p><br>
<p>返却期限を過ぎますと1日あたり2,200円(税込)が遅延損害金として保証金より控除いたします。また遅延日数が5日以上経過した場合は、サンプル品の紛失とみなされ、以下の通りサンプル品の損害金(1枚あたり27,500円税込)は保証金額を差し引いた額面にてお客様負担となります。ご注意ください。</p><br>
<p><b>◆損害金額につきまして</b><br>サンプル品数x27,500円-11,000円(保証金)<br>1枚の場合：16,500円<br>2枚の場合：44,000円<br>3枚の場合：71500円<br>4枚の場合：99,000円<br><small>※すべて税込の損害金が発生いたします。</small></p><br>
<p><b>★返却するもの</b><br>&#9312; サンプルブラ<br>&#9313; メジャー<br>&#9314; パッド<br>&#9315; インストラクションシート</p><br>
<p>また、返却期限の自動延長などはございませんので、何か不具合やご不明点などございましたら<a href="mailto:hello@chiyono-anne.com">hello@chiyono-anne.com</a>までお問い合わせください。</p>
<p>本メールと行き違いで返却手続きを完了いただいてる場合何卒ご容赦ください。</p>
<p><?php //printf(__('The order #%d need return sample product. Order Details:', 'woocommerce'), $order->get_order_number()); ?></p>

<?php
$passcode = '';// get_post_meta($order->id, '_ch_passcode', true);
if (!empty($passcode)) {//comment temp
    ?>
    <a target="_blank" style="background: blue;color: white;" href="<?php echo admin_url('admin-ajax.php?action=try_fit_customer_notify_to_admin') . '&type=email&passcode=' . $passcode . '&id=' . base64_encode($order->id); ?>"><?php echo __('I returned', 'zoa'); ?></a>
    <?php
}
?>
<?php
/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);
