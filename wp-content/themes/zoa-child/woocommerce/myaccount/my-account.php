<?php
global $post, $wp;
$url = home_url($wp->request);
$url_arr = explode('/', $url);
array_pop($url_arr);
$request = explode('/', $wp->request);
$prev_url = implode('/', $url_arr);
$prev_slug = $request[1];
$lastslug = end($request);
$order_history_url = get_permalink(get_option('woocommerce_myaccount_page_id')) . 'orders';
if (count($request) > 2) {
  if ($prev_slug == 'view-order') {
    $back_prevpage = '<a class="cta cta--secondary" href="' . $order_history_url . '">' . __('Return to Order History', 'zoa') . '</a>';
  } else {
    $back_prevpage = '<a class="cta cta--secondary" href="' . $prev_url . '">' . __('Back', 'zoa') . '</a>';
  }
}
$my_acount_title = wpb_woo_my_account_order();
$current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$dashboard_url = get_permalink(get_option('woocommerce_myaccount_page_id'));

$base_page = get_page_by_path('about-repair');
$base_id = $base_page->ID;
$group_name = 'ms_definition';
$group = get_field($group_name, $base_id);
$def_repeater = 'offer';
$offers = $group[$def_repeater];
$def_rank = 'rank';
$def_benefit = 'list';
$def_repair = 'repair';
$def_repairship = 'repair_shipfee';
$def_amt01 = 'amount_first';
$def_amt02 = 'amount_keep';
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
if (!defined('ABSPATH')) {
  exit;
}
wc_print_notices();
$title_parts = zoa_wp_title(array());
?>
<div class="account-row row <?php if ($dashboard_url == $current_url) { ?>flex-justify-between<?php } else { ?>gutter-padding flex-justify-center<?php } ?>">
  <?php
  /**
   * My Account navigation.
   * @since 2.6.0
   */
  do_action('woocommerce_account_navigation');
  ?>

  <div class="account__content <?php if ($dashboard_url == $current_url) { ?>col-12 col-md-7<?php } else { ?>col-lg-7 col-md-8 col-12 offset-md-1<?php } ?>">
    <?php
    if ($dashboard_url == $current_url) {
      $current_user = wp_get_current_user();

      if (isset($current_user) && isset($current_user->ID) && $current_user->ID > 0) { //only for customer logged
        $rank_and_amount = mr_get_member_rank($current_user->ID);

        $user_id = $current_user->ID;
        $rank = get_user_meta($user_id, 'rank', true);
        $spent_total = get_user_meta($user_id, 'spent_total', true);
        $expired_date = get_user_meta($user_id, 'expired_date', true);
        $spent_retain = (int) get_user_meta($user_id, 'spent_retain', true);
        $rank_limit = (int) get_user_meta($user_id, 'rank_limit', true);
        $retain_amount = $rank_limit - $spent_retain;
        $next_update = get_user_meta($user_id, 'next_update', true);
        $settings = mr_get_settings();

        if ($rank == 'gold') {
          $coupon = new WC_Coupon($settings['ch_mr_coupon_2times']);
          $usage_limit_per_user = mr_coupon_limit();
          $used = mr_get_coupon_used_by_user($current_user->ID, $coupon->id);
          $remain = $usage_limit_per_user - $used;
    ?>
          <?php if ($remain > 0) { ?>
            <div class="my_account_coupon">
              <?php printf(__("You still have %s OFF coupon x %s", 'zoa'), $coupon->coupon_amount . '%', $remain); ?>
            </div>
          <?php } ?>
          <?php } elseif ($rank == 'silver') {
          $coupon = new WC_Coupon($settings['ch_mr_coupon_except_bra_2times']);
          $usage_limit_per_user = mr_coupon_limit();
          $used = mr_get_coupon_used_by_user($current_user->ID, $coupon->id);
          $remain = $usage_limit_per_user - $used;
          if ($remain > 0) { ?>
            <div class="my_account_coupon">
              <?php printf(__("You still have %s OFF coupon x %s", 'zoa'), $coupon->coupon_amount . '%', $remain); ?>
            </div>
          <?php } ?>
        <?php  } ?>
        <?php echo '<p class="youare"><span class="rank_you">あなたは現在</span><span class="rank_name serif rank_' . $rank . '">' . $rank . '</span>ランクです。</p>'; ?>
        <?php
        $user_id = $_REQUEST['user_id'] ? $_REQUEST['user_id'] : get_current_user_id();
        $user = get_userdata($user_id);
        if (isset($rank_and_amount['next_update'])) {
          $rest_amount = '<span class="restamount_label">' . __('spend', 'zoa') . ' &yen;<span class="restamount_value font-serif">' . number_format($next_update) . '</span> ' . __('to rank up', 'zoa') . '</span>';

          echo '<table class="table_responsive_vertical ja table_responsive table_font_sm"><tbody><tr><th class="th_md">' . __('Total Purchases Amount', 'zoa') . '<br/><small>' . __('(in the Past Year)', 'zoa') . '</small></th><td class="text-center bigger" data-label="< ' . __('Total Purchases Amount', 'zoa') . __('(in the Past Year)', 'zoa') . ' >"><span class="font-serif">&yen;' . number_format($spent_total) . '</span><span class="block restamount_rankup">' . $rest_amount . '</span></td></tr>';

          if ($retain_amount > 0) {
            echo '<tr><th class="th_md">' . __('To Retain', 'zoa') . '</th><td class="text-center bigger" data-label="< ' . __('To Retain', 'zoa') . ' >"><span class="font-serif">&yen;' . number_format($retain_amount) . '</span><span class="block restamount_rankup"><small>' . __('To Retain Your Rank on Next', 'zoa') . '</small></span></td></tr>';
          }

          if ($expired_date) {
            echo '<tr><th class="th_md">' . __('Next update date of your rank', 'zoa') . '</th><td class="text-center bigger" data-label="< ' . __('Next update date of your rank', 'zoa') . ' >"><span class="font-serif">' . date_i18n('Y/m/d (H:i)', strtotime($expired_date)) . '</span></td></tr>';
          }

          echo '</tbody></table>';
        }
        ?>
        <?php

        $default = array('day' => '', 'month' => '', 'year' => '',);
        $birth_date = wp_parse_args(get_the_author_meta('account_birth', $user->ID), $default);
        if ($birth_date['year'] == '0' || $birth_date['year'] == '' || $birth_date['month'] == '0' || $birth_date['month'] == '' || $birth_date['day'] == '0' || $birth_date['day'] == '') {
          echo '<div class="notice_small p_xs"> お客様の誕生日が未登録のため、誕生日クーポンが配信されてません。<a href="' . wc_customer_edit_account_url() . '">誕生日のご登録はこちら</a></div>';
        }
        ?>
        <?php if (have_rows($group_name, $base_id)) : ?>
          <table class="rank_table ja table_responsive">
            <thead>
              <tr>
                <th rowspan="2" class="rank_name">会員ステータス</th>
                <th rowspan="2" class="rank_benefit">特典内容</th>
                <th class="rank_logic">ご購入金額(税抜・送料抜)</th>
              </tr>

            </thead>
            <?php while (have_rows($group_name, $base_id)) : the_row();
              if (have_rows('offer')) : ?>
                <tbody>
                  <?php while (have_rows('offer')) : the_row(); ?>
                    <?php
                    $subs = array();
                    $subs = get_sub_field_object($def_rank);
                    $ranks_value = $subs['value'];
                    $ranks = $subs['choices'][$ranks_value];
                    $benefits = get_sub_field($def_benefit);
                    $amounts_first = get_sub_field($def_amt01);
                    $amounts_keep = get_sub_field($def_amt02);
                    ?>
                    <?php if ($rank == $ranks_value) { ?>
                      <tr class="rank_tr rank_tr_<?php echo $ranks_value; ?>">
                        <td class="rank_name" data-label="会員ステータス">
                          <span class="rank_icon">
                            <?php if ($ranks_value == 'royal') { ?>
                              <svg id="gradient">
                                <defs>
                                  <linearGradient id="linearGradient">
                                    <stop offset="0%" stop-color="#74ebd5"></stop>
                                    <stop offset="50%" stop-color="#ffdde1"></stop>
                                    <stop offset="100%" stop-color="#ACB6E5"></stop>
                                  </linearGradient>
                                </defs>
                              </svg>
                              <span class="svg-wrap eq_icon">
                                <svg class="icoca icoca-RankIcon">
                                  <use xlink:href="#icoca-RankIcon"></use>
                                </svg>
                              </span>
                            <?php } else { ?>
                              <span class="rank_icon"><span class="svg-wrap eq_icon">
                                  <svg class="icoca icoca-RankIcon">
                                    <use xlink:href="#icoca-RankIcon"></use>
                                  </svg>
                                </span></span>
                            <?php } ?>
                          </span>
                          <span class="name"><?php echo $ranks; ?></span>
                        </td>
                        <?php if ($benefits) { ?>
                          <td class="rank_benefit" data-label="&lt;&nbsp;特典&nbsp;&gt;">
                            <ul class="list">
                              <?php
                              foreach ($benefits as $benefit) {
                                echo '<li>' . $benefit['item'] . '</li>';
                              }
                              ?>
                            </ul>
                          </td>
                        <?php } ?>
                        <?php if ($amounts_first) { ?>
                          <td class="rank_logic" data-label="&lt;&nbsp;初年度ご購入金額&nbsp;&gt;">
                            <?php echo $amounts_first; ?>
                          </td>
                        <?php } ?>
                      </tr>
                    <?php } ?>
                  <?php endwhile; ?>
                </tbody>
              <?php endif; ?>
            <?php endwhile; ?>
          </table>
          <p class="notion_p"><a href="#remodal_rank_info" class="link_modal pop-up-button-remodal">会員ランクについて</a></p>
        <?php endif; ?>
        <?php if (have_rows($group_name, $base_id)) : ?>
          <div data-remodal-id="remodal_rank_info" id="remodal_rank_info" class="remodal_basic rank_summary">
            <div class="remodal_head">
              <h3 class="remodal_ttl ja">会員ランクについて</h3>
              <button data-remodal-action="close" class="remodal-close"></button>
            </div>
            <div class="remodal_body">
              <?php get_template_part('./template-parts/mr-define'); ?>
              <div class="p_xxs p_notice">
                <p>※ゲストとしてご購入されたご注文は換算されませんので、あらかじめご了承ください。</p>
                <p>※キャンセル・返品をされた場合はご購入金額には加算されません。</p>
                <p>※ディスカウントクーポンをご利用の場合は、ディスカウントされた後の支払い金額が累計対象となります。</p>
              </div>
            </div>
          </div>
        <?php endif; ?>

    <?php }
    } ?>
    <?php if ($dashboard_url != $current_url) { ?>
      <?php if (!$lastslug == 'my-wishlist') { ?>
        <div class="account__heading">
          <h1 class="heading heading--xlarge serif">
            <?php if (empty($my_acount_title[trim(end($request))])) { ?>
              <?php echo $title_parts['title']; ?>
            <?php } else { ?>
              <?php echo $my_acount_title[trim(end($request))]; ?>
            <?php } ?>
          </h1>
          <?php echo $back_prevpage; ?>
        </div>
      <?php } ?>
    <?php } ?>
    <?php
    /**
     * My Account content.
     * @since 2.6.0
     */
    do_action('woocommerce_account_content');
    ?>
  </div>
</div>