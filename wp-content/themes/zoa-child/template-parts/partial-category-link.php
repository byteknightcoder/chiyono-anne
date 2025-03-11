<?php
$include = array(72, 73, 530, 531, 532);
$args = array(
    'hide_empty' => false, // also retrieve terms which are not used yet
    'limit' => -1,
    'include' => $include,
    'meta_key' => 'order',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
);
$cats = get_terms('product_cat', $args);
if (!empty($cats)) {
?>
<style>
   .cl-items .slick-list{padding:0 20% 0 0;}
</style>
    <div class="container-fluid">
        <div class="content-fit cats-link">
            <h3 class="sec-title ff_chapa mgb_02 mid-title align--center fade-up fade-ani"><?php echo __('Search by Item', 'zoa'); ?></h3>
            <div class="cl-items cats-items">
                <?php
                foreach ($cats as $cat) {
                    // get the thumbnail id using the queried category term_id
                    $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);

                    // get the image URL
                    $image = wp_get_attachment_image_url($thumbnail_id, 'large');
                ?>
                    <div class="cl-item">
                        <a class="cl-thum over_t over_linear" href="<?php echo esc_url(get_term_link($cat)); ?>">
                            <div class="cl-name over_elem over_elem__center-bottom ff_chapa"><?php echo $cat->name; ?></div>
                            <img class="cl-img" src="<?php echo $image; ?>" />
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

<?php
}
