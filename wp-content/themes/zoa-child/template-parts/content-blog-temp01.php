<?php
$c_header = zoa_page_header_slug();
$categories = get_the_category();
$fvimg = get_field('post_fv');
?>
<div class="blog-article blog_newstyle01 styled_post" <?php zoa_schema_markup( 'blog' ); ?>>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php zoa_schema_markup( 'blog_list' ); ?>>
		<div itemprop="mainEntityOfPage">
			<div class="blog-article-sum">
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
			</div><!--/sf-content-->
				
			<div class="blog__content container">
					
					
					

				<div class="entry-content <?php if (!has_post_thumbnail() ) { ?>align_center<?php } ?>" <?php zoa_schema_markup( 'post_content' ); ?>>
					
					
					<!--layout01-->
					<div class="layout__01 bsec bsec__wrap">
						<div class="col__img">
							<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418378198.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
						</div>
						<div class="col__txt">
							<div class="text_inner">
								<div class="paragraph"><p>ここ数年、ランジェリーブランドの持続可能性への取り組みは目まぐるしく、海にやさしいオーガニック素材の使用はスタンダードになりつつある。とはいえ、繊維製品が環境に与える悪影響の2/3が、日々の洗濯と乾燥によるものであることを考えると、まだまだ改善への道のりは長いと言えるだろう。</p>
								<p>この現状に一石を投じるのが、2014年にデンマークでスタートしたオーガニック ベーシックス（ORGANIC BASICS）だ。創設者はファッション業界でのキャリアがない。だからこそ、既存のプロセスを疑う目を持ち、サステナブルな消費文化を広められるのかもしれない。</p>
								<p>ブランドの特徴は、ポリジン社の抗菌加工を施した「SilverTech™」だ。これを用いることで洗濯による劣化を避けることができる。また、銀イオンを素材に含ませることで、臭いの原因となるバクテリアの約99.9%を殺菌することが可能。洗濯回数の減少、および洗濯機の水量を節約することにも繋がるというわけだ。</p></div>
								<!--quote block-->
					<div class="block__quote bsec">
					<blockquote>
					<h3 class="large-text o">There was a PART of me that felt I needed to catch up… I was still quite NAIVE and felt I had to sort of LEVEL up my Britishness</h3>
					</blockquote>
					</div>
					<!--/quote block-->
							</div>
						</div>
					</div>
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<div class="main-text-component paragraph">
								<p>この抗菌テクノロジーは、NASAが宇宙ステーションで水を浄化する際に銀を使用していることから着想を得た。日本人の清潔な国民性を考えると、洗わずに同じものを着続けることに抵抗があるかもしれないが、家庭における洗濯水使用量を考えてみると、今一度見つめ直す必要がありそうだ。</p>
								<p>ちなみに、抗菌防臭効果のあるポリジン加工の主成分となる塩化銀は海や土などにもともと存在するが、新たな銀を採取せずリサイクルによって得た銀を使用。衣類から洗い流されにくく、海中に放出されたとしても生物には無害とされるこの安全性は「bluesign®」認定によって裏付けされている。また、素材自体には「GOTS」認定のオーガニックコットン、持続可能なプランテーションから調達した樹木由来の「TENCEL™」など、すべて地球環境に優しいものを使用している。</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					<!--layout02-->
					<div class="layout__02 bsec bsec__wrap">
						<div class="fx fas fdr fw">
						<div class="ho">
							<div class="portrait_inner">
								<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
							</div>
						</div>
						<div class="ho">
							<div class="portrait_inner">
								<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
							</div>
						</div>
						</div>
					</div>
					<!--/layout02-->
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<div class="main-text-component paragraph">
								<p>この抗菌テクノロジーは、NASAが宇宙ステーションで水を浄化する際に銀を使用していることから着想を得た。日本人の清潔な国民性を考えると、洗わずに同じものを着続けることに抵抗があるかもしれないが、家庭における洗濯水使用量を考えてみると、今一度見つめ直す必要がありそうだ。</p>
								<p>ちなみに、抗菌防臭効果のあるポリジン加工の主成分となる塩化銀は海や土などにもともと存在するが、新たな銀を採取せずリサイクルによって得た銀を使用。衣類から洗い流されにくく、海中に放出されたとしても生物には無害とされるこの安全性は「bluesign®」認定によって裏付けされている。また、素材自体には「GOTS」認定のオーガニックコットン、持続可能なプランテーションから調達した樹木由来の「TENCEL™」など、すべて地球環境に優しいものを使用している。</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--layout02-->
					<div class="layout__02 column_01 fx fas fdr bsec bsec__wrap">
						<div class="ho">
							<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
						</div>
						
					</div>
					<!--/layout02-->
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar title_style01">ダミーダミーダミー</h3>
							<div class="main-text-component paragraph">
								<p>この抗菌テクノロジーは、NASAが宇宙ステーションで水を浄化する際に銀を使用していることから着想を得た。日本人の清潔な国民性を考えると、洗わずに同じものを着続けることに抵抗があるかもしれないが、家庭における洗濯水使用量を考えてみると、今一度見つめ直す必要がありそうだ。</p>
								<p>ちなみに、抗菌防臭効果のあるポリジン加工の主成分となる塩化銀は海や土などにもともと存在するが、新たな銀を採取せずリサイクルによって得た銀を使用。衣類から洗い流されにくく、海中に放出されたとしても生物には無害とされるこの安全性は「bluesign®」認定によって裏付けされている。また、素材自体には「GOTS」認定のオーガニックコットン、持続可能なプランテーションから調達した樹木由来の「TENCEL™」など、すべて地球環境に優しいものを使用している。</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--layout02-->
					<div class="layout__02 bsec bsec__wrap">
						<div class="column_03 fx fas fdr fw fjc">
						<div class="ho">
							<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
						</div>
						<div class="ho">
							<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
						</div>
						<div class="ho">
							<?php echo do_shortcode('[shop_the_look img="https://cache.net-a-porter.com/content/images/story-body-content-1-0-1601418407104.jpeg/w1900_q65.jpeg" product_skus = "CAE-13-XMAS20, CAE-01-XMAS20"][/shop_the_look]'); ?>
						</div>
						</div>
					</div>
					<!--/layout02-->
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar title_style02">ダミーダミーダミー</h3>
							<div class="main-text-component paragraph">
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
								<p class="iv_p"><strong>Interviewr:</strong> “精力的な活動を続けるオーガニック ベーシックスは、競合の多い市場でどんな存在を目指しているのか？”</p>
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar title_style02">ダミーダミーダミー</h3>
							<div class="main-text-component paragraph">
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
								<p class="iv_p"><strong>Interviewr:</strong> “精力的な活動を続けるオーガニック ベーシックスは、競合の多い市場でどんな存在を目指しているのか？”</p>
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					<!--blockquote style 02-->
					<div class="para__center bsec">
						<blockquote class="pull-quote">
						<div class="qupte_wrap o quote-before">“</div><div class="quote_inner">
						<h5 class="em_title e"><em>美しさの在り方は人それぞれであるということ、<br>ありのままの体が美しいということ</em></h5>
						</div><div class="qupte_wrap o quote-after">”</div>
						</blockquote>
					</div>
					<!--/blockquote style 02-->
					
					<!--paragraph center style-->
					<div class="para__center fx bsec">
						<div class="para_container">
							<h3 class="section-title-bar title_style02">ダミーダミーダミー</h3>
							<div class="main-text-component paragraph">
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
								<p class="iv_p"><strong>Interviewr:</strong> “精力的な活動を続けるオーガニック ベーシックスは、競合の多い市場でどんな存在を目指しているのか？”</p>
								<p class="iv_p"><strong>Chiyono:</strong> “ブランドの立ち上げ時から、正直で責任のある高品質のブランドとして、業界に変化をもたらすことを目標としています。今日の環境問題の原因は数多くの誤った決定によるもの。過剰消費と地球汚染の危機を回避するためには最良の判断が必要です”</p>
							</div>
						</div>
					</div>
					<!--/paragraph center style-->
					
					
					<div class="bsec bsec__wrap">
						<!--center title-->
						<h3 class="section-title-centre"><span class="tsm">SHOP</span><span>HOLIDAY STYLE</span></h3>
						<!--/center title-->
						<!--product list-->
					<div class="custom-product-list">
						<div class="custom-product-list_inner">
							<div data-automation="product-list-desktop">
								<?php echo do_shortcode('[products limit="4" columns="4" class="list-shops" category="bras, panty" cat_operator="OR" ]'); ?>
								<?php //echo do_shortcode('[product_data id=30712,30724]'); ?>
							</div>
						</div>
					</div>
					<!--/product list-->
					</div>
					
					
					
					
				</div>

			

				<footer class="entry-footer">
					<div class="bsec__wrap"><?php echo show_latest_blog_posts(1); ?></div>
				</footer>
			</div><!--/container-->
				
			</div>
		</div>
	</article>
</div>