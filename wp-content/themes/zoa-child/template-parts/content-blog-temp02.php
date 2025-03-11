<?php
$c_header = zoa_page_header_slug();
$categories = get_the_category();
$fvimg = get_field('post_fv');
?>
<div class="blog-article blog_newstyle01 styled_post" <?php zoa_schema_markup( 'blog' ); ?>>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup( 'blog_list' ); ?>>
		<div itemprop="mainEntityOfPage">
			<div class="blog-article-sum">
				<!--<div class="sf-content">-->
				<div class="blog_header_cover fw_cover">
					<div class="article_cover">
				
					
					<div class="blog-article-header <?php if (!empty( $fvimg )) { ?>align_center<?php } ?>">
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
						
						<?php if (!empty( $fvimg )) {
$thumb = $fvimg['url'];
$title = $fvimg['title'];
$alt = $fvimg['alt'] ? $fvimg['alt'] : $title;
	                 echo '<div class="fixed_cover"><div class="cover_inner">';
	                 echo '<div class="media-content-fullr-wide">';
	                 ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_attr_e($alt); ?>">
                                            <?php
	                 echo '</div>';
	                 //echo '<div class="header-image-overlay"></div>';
	                 echo '</div></div>';
                } ?>
					
						
					</div>
					<div class="first_summary" data-scroll="toggle(animateIn, animateOut)">
						<div class="entry-summary">
							<?php
					if ( is_single() ) {
						the_content();
						zoa_wp_link_pages();
					} else {
						the_excerpt();
					}
					?>
							<div class="end_header">
								<div class="end_credit">
									<span class="credit">Photography Paki Chong</span><span class="credit">Styling Chiyono Anne</span>
								</div>
							</div>
							<?php zoa_seo_data(); ?>
						</div>
						<div class="post-date-header"><div class="post-date-header_inner"><div class="header-date"><?php echo '<span class="day">'.get_post_time('d').'</span><span class="month">'.get_post_time('M').'</span><span class="year">&apos;'.get_post_time('Y').'</span>'; ?></div></div></div>
					</div>
				</div><!--/blog_header_cover-->
			<!--</div>/sf-content-->
				
			<div class="blog__content container">

				<div class="entry-content <?php if (!has_post_thumbnail() ) { ?>align_center<?php } ?>" <?php zoa_schema_markup( 'post_content' ); ?>>
					
					<!--layout01-->
					<div class="layout__01 bsec bsec__wrap">
						<div class="col__img">
							<!--<img src="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418378198.jpeg/w1900_q65.jpeg" alt="">-->
							<?php echo do_shortcode('[shop_the_look img="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2020/09/e472a0e8-cae-01-xmas20_07.jpg" product_skus = "CAE-01-XMAS20,CAE-10"][/shop_the_look]'); ?>
							
						</div>
						<div class="col__txt">
							<div class="text_inner">
								<h3 class="section-title-bar title_style03 num_ttl"><span class="num">1</span><span>ダミーダミーダミー</span></h3>
								<div class="paragraph"><p>ここ数年、ランジェリーブランドの持続可能性への取り組みは目まぐるしく、海にやさしいオーガニック素材の使用はスタンダードになりつつある。とはいえ、繊維製品が環境に与える悪影響の2/3が、日々の洗濯と乾燥によるものであることを考えると、まだまだ改善への道のりは長いと言えるだろう。</p></div>
								<h5 class="section-subttl o upper">Styling Ideas</h5>
								<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" skus="CAE-13-XMAS20,CAI-Y-02,CAE-15,CAE-10" ]'); ?>
								
							</div>
						</div>
					</div>
					<!--/layout01-->
					
					<!--layout01-->
					<div class="para__center bsec">
						<div class="col__img">
							<!--<img src="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418378198.jpeg/w1900_q65.jpeg" alt="">-->
							<?php echo do_shortcode('[shop_the_look img="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2019/03/e296a081-blog_xmasrobe.jpg" product_skus = "CAE-13-XMAS20,CAE-01,CAE-10"][/shop_the_look]'); ?>
							
						</div>
						<div class="col__txt">
							<div class="text_inner">
								<h3 class="section-title-bar title_style03 num_ttl"><span class="num">2</span><span>ダミーダミーダミー</span></h3>
								<div class="paragraph"><p>ここ数年、ランジェリーブランドの持続可能性への取り組みは目まぐるしく、海にやさしいオーガニック素材の使用はスタンダードになりつつある。とはいえ、繊維製品が環境に与える悪影響の2/3が、日々の洗濯と乾燥によるものであることを考えると、まだまだ改善への道のりは長いと言えるだろう。この現状に一石を投じるのが、2014年にデンマークでスタートしたオーガニック ベーシックス（ORGANIC BASICS）だ。創設者はファッション業界でのキャリアがない。だからこそ、既存のプロセスを疑う目を持ち、サステナブルな消費文化を広められるのかもしれない。</p></div>
							</div>
							
						</div>
					</div>
					<div class="bsec bsec__wrap">
						<h5 class="section-subttl o upper">Styling Ideas</h5>
						<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" skus="CAE-01,CAE-02,CAE-09,CAE-10" ]'); ?>
					</div>
					<!--/layout01-->
					
					<!--layout01-->
					<div class="layout__01 bsec_even bsec bsec__wrap">
						<div class="col__img">
							<!--<img src="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418378198.jpeg/w1900_q65.jpeg" alt="">-->
							<?php echo do_shortcode('[shop_the_look img="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2019/03/5df607c2-121614362_368929070827190_5395605975670674905_n.jpg" product_skus = "CAB-L-02-XMAS20,CAB-L-05-XMAS20"][/shop_the_look]'); ?>
							
						</div>
						<div class="col__txt">
							<div class="text_inner">
								<h3 class="section-title-bar title_style03 num_ttl"><span class="num">3</span><span>ダミーダミーダミー</span></h3>
								<div class="paragraph"><p>ここ数年、ランジェリーブランドの持続可能性への取り組みは目まぐるしく、海にやさしいオーガニック素材の使用はスタンダードになりつつある。とはいえ、繊維製品が環境に与える悪影響の2/3が、日々の洗濯と乾燥によるものであることを考えると、まだまだ改善への道のりは長いと言えるだろう。この現状に一石を投じるのが、2014年にデンマークでスタートしたオーガニック ベーシックス（ORGANIC BASICS）だ。創設者はファッション業界でのキャリアがない。だからこそ、既存のプロセスを疑う目を持ち、サステナブルな消費文化を広められるのかもしれない。</p></div>
								<h5 class="section-subttl o upper">You may like it</h5>
								<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" skus="CAB-L-06-XMAS20,CAB-L-02-G-XMAS20" ]'); ?>
							</div>
						</div>
					</div>
					<!--/layout01-->
					
					<!--layout01-->
					<div class="layout__01 bsec bsec__wrap">
						<div class="col__img">
							<!--<img src="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418378198.jpeg/w1900_q65.jpeg" alt="">-->
							<?php echo do_shortcode('[shop_the_look img="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2020/10/fab53cae-120499198_370455390746927_1576223863434040864_n.jpg" product_skus = "CAB-L-06-XMAS20,CAB-L-02-G-XMAS20"][/shop_the_look]'); ?>
							
							
						</div>
						<div class="col__txt">
							<div class="text_inner">
								<h3 class="section-title-bar title_style03 num_ttl"><span class="num">4</span><span>ダミーダミーダミー</span></h3>
								<div class="paragraph"><p>ここ数年、ランジェリーブランドの持続可能性への取り組みは目まぐるしく、海にやさしいオーガニック素材の使用はスタンダードになりつつある。とはいえ、繊維製品が環境に与える悪影響の2/3が、日々の洗濯と乾燥によるものであることを考えると、まだまだ改善への道のりは長いと言えるだろう。この現状に一石を投じるのが、2014年にデンマークでスタートしたオーガニック ベーシックス（ORGANIC BASICS）だ。創設者はファッション業界でのキャリアがない。だからこそ、既存のプロセスを疑う目を持ち、サステナブルな消費文化を広められるのかもしれない。</p></div>
								<h5 class="section-subttl o upper">Xmas Limited Lingeries</h5>
								<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" skus="CAB-L-02-XMAS20,CAB-L-02-G-XMAS20,CAB-L-05-XMAS20,CAB-L-06-XMAS20" ]'); ?>
							</div>
						</div>
					</div>
					<!--/layout01-->
					
					
					
					
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar bigger o align_center">Looking for chirstmas gift?</h3>
							<div class="main-text-component paragraph">
								<p>2020年もクリスマスコフレのシーズン到来！人気ブランドから限定コスメ盛りだくさんのメイクアップコフレ、スキンケアコフレ、ボディケアコフレが発売される。人気アイテムは即完売・予約必須の場合もあるので、お気に入りのコフレを事前にチェックして、自分へのご褒美に購入してみてはいかが？</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--layout02 cardstyle-->
					<div class="layout__02 card__style bsec bsec__wrap">
						<div class="fx fw fas fdr masonry__grid grid">
							<div class="ho card__item grid-item">
								<div class="inner">
									<div class="col__thum img_cover portlait"><img src="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2020/10/5c896684-ca_gc_envelope_story_02.jpg" alt=""></div>
									<div class="col__desc">
										<h3 class="section-title-bar o align_center">Gift Card</h3>
										<div class="paragraph"><p>彼氏彼女、夫婦、友達へのクリスマスプレゼントを選んで、大切なひとを喜ばせる最高の1日を過ごしませんか？</p></div>
									</div>
								</div>
							</div>
							<div class="ho card__item grid-item">
								<div class="inner">
									<div class="col__thum img_cover landscape"><img src="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2020/10/72eda435-eyemasks.jpg" alt=""></div>
									<div class="col__desc">
										<h3 class="section-title-bar o align_center">Eye Mask</h3>
										<div class="paragraph"><p>彼氏彼女、夫婦、友達へのクリスマスプレゼントを選んで、大切なひとを喜ばせる最高の1日を過ごしませんか？</p></div>
									</div>
								</div>
							</div>
							<div class="ho card__item grid-item">
								<div class="inner">
									<div class="col__thum img_cover landscape"><img src="http://test.chiyono-anne.com/chiyono/wp-content/uploads/2020/10/0c55d37c-pillows.jpg" alt=""></div>
									<div class="col__desc">
										<h3 class="section-title-bar o align_center">Pillow case with eye mask</h3>
										<div class="paragraph"><p>彼氏彼女、夫婦、友達へのクリスマスプレゼントを選んで、大切なひとを喜ばせる最高の1日を過ごしませんか？</p></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--layout04-->
					
					
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar title_style04 t align_center"><span>年に一度のクリスマスにとっておきのランジェリーを</span></h3>
							<div class="main-text-component paragraph">
								<p class="iv_p">ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です。</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--blockquote style 02-->
					<div class="para__center bsec">
						<blockquote class="pull-quote">
						<div class="qupte_wrap o quote-before">“</div><div class="quote_inner">
						<h5 class="em_title c"><em>It is Christmas every time you let God love others through you...yes, it is Christmas every time you smile at your brother and offer him your hand. </em></h5>
						</div><div class="qupte_wrap o quote-after">”</div><div class="quote_author"><span>Mother Teresa</span></div>
						</blockquote>
					</div>
					<!--/blockquote style 02-->
					
					
					<div class="bsec bsec__wrap">
						<!--center title-->
						<h3 class="section-title-centre"><span class="tsm">SHOP</span><span>WINTER STYLE</span></h3>
						<!--/center title-->
						<!--product list-->
					<div class="custom-product-list">
						<div class="custom-product-list_inner">
							<div data-automation="product-list-desktop">
								<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" category="bras, panty" cat_operator="OR" ]'); ?>
							</div>
						</div>
					</div>
					<!--/product list-->
					</div>
					
					
					
					
				</div>
			

			

				<footer class="entry-footer">
                          <div class="bsec__wrap"><?php echo show_latest_blog_posts(2); ?></div> 
				</footer>
			</div><!--/container-->
				
			</div>
		</div>
	</article>
</div>