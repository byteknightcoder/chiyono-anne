<?php ?>
<style>
  .home .post-item__latest .slick-list {
    padding: 0 20% 0 0;
  }
</style>
<div class="container-md">
  <div class="content-bloghome">
    <h3 class="sec-title ff_chapa mgb_02 mid-title align--center fade-up fade-ani">CA Stories</h3>
    <?php
    echo do_shortcode('[show_latest_blog_posts_shortcode limit="3"]');
    ?>
  </div>
</div>