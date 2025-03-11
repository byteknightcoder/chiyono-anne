<?php
$fvimg = get_field('post_fv');
$categories = get_the_category();
$photo_meta=get_field('photo_meta');
?>
<div class="sf-content">
  <div class="blog_header_cover">
    <div class="article_cover fade__inout">


      <div class="blog-article-header para-item" data-reg="3">
        <div class="blog-article-header_inner">
          <span class="entry-meta meta-cat"><?php echo $categories[count($categories)-1]->name; ?></span>
          <header class="post-entry-header">
            <?php
							if ( is_single() ) :
								the_title( '<h1 class="entry-title blog-title">', '</h1>' );
								else :
									the_title( '<h2 class="entry-title blog-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
								endif;
							if(get_the_subtitle() != '') {
								echo '<div class="subttl"><h4 class="subtitle">' . get_the_subtitle() . '</h4></div>';
							}
							?>
          </header>
        </div>
      </div>

      <?php if ( !empty( $fvimg ) ) {
$thumb = $fvimg['url'];
$title = $fvimg['title'];
$alt = $fvimg['alt'] ? $fvimg['alt'] : $title;
	                 echo '<div class="full_cover"><div class="cover_inner para-item">';
	                 echo '<div class="media-content-fullr-wide">';
	                 ?>
      <img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_attr_e($alt); ?>">
      <?php
	                 echo '</div>';
	                 echo '<div class="header-image-overlay"></div>';
	                 echo '</div></div>';
                } ?>


    </div>
    <div class="first_summary" data-scroll="toggle(animateIn, animateOut)">
      <div class="entry-summary">
        <?php
					if( get_field('first_summary') ):
							the_field('first_summary');
					endif;
					?>
        <?php
						// if(isset($photo_meta)&&!empty($photo_meta['photography'])&&!empty($photo_meta['styling'])&&!empty($photo_meta['writer'])&&!empty($photo_meta['special_thanks'])){
						if(!empty($photo_meta['photography'])||!empty($photo_meta['styling'])||!empty($photo_meta['writer'])||!empty($photo_meta['special_thanks'])){
						?>
        <div class="end_header">
          <div class="end_credit">
              <?php
              if(!empty($photo_meta['photography'])){
              ?>
            <span class="credit">Photography <?php echo trim($photo_meta['photography']); ?></span>
              <?php } ?>
                          <?php
              if(!empty($photo_meta['styling'])){
              ?>
            <span class="credit">Styling <?php echo trim($photo_meta['styling']); ?></span>
            <?php } ?>
                          <?php
              if(!empty($photo_meta['writer'])){
              ?>
            <span class="credit">Writer <?php echo trim($photo_meta['writer']); ?></span>
            <?php } ?>
                          <?php
              if(!empty($photo_meta['special_thanks'])){
              ?>
            <span class="credit">Special Thanks <?php echo trim($photo_meta['special_thanks']); ?></span>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
        <?php zoa_seo_data(); ?>
      </div>
      <div class="post-date-header">
        <div class="post-date-header_inner">
          <div class="header-date"><?php echo '<span class="day">'.get_post_time('d').'</span><span class="month">'.get_post_time('M').'</span><span class="year">&apos;'.get_post_time('Y').'</span>'; ?></div>
        </div>
      </div>
    </div>
  </div>
  <!--/blog_header_cover-->
</div>
<!--/sf-content-->