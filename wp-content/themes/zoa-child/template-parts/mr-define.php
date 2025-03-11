<?php
$page_title = $wp_query->post->post_title;
$base_page = get_page_by_path('about-repair');
$base_id = $base_page->ID;
$group_name = 'ms_definition';
$group = get_field($group_name, $base_id);
$def_repeater = 'offer';
$offers = $group[$def_repeater];
$def_rank = 'rank';
$def_benefit = 'list';
$def_amt01 = 'amount_first';
$def_amt02 = 'amount_keep';
?>
<?php
// function offerArray($mr_rank) {
//   $base_page = get_page_by_path( 'about-repair' );
//   $base_id = $base_page->ID;
//   $group = get_field('ms_definition', $base_id);
//   $offers = $group['offer'];
//   $offers_rank = 'rank';
//   $offers_list = 'list';
//   $offers_item = 'item';
//   $mr_rank = 'bronze';
//   // echo '<ul class="list">';
//   foreach($offers as $offer => $rank_name) {
//     $offer[$offers_rank] = '<p>'.$rank_name[$mr_rank].'</p>';
//   }
//   return $offers;
//   // echo '</ul>';
// }
?>
<picture>
  <source media="(max-width:749px)" width="750" height="807" srcset="<?php echo get_stylesheet_directory_uri(); ?>/images/members/membership_rule_sp.svg">
  <img width="820" height="507" sizes="100vw,820px" alt="会員ステータス特典と条件" src="<?php echo get_stylesheet_directory_uri(); ?>/images/members/membership_rule.svg">
</picture>