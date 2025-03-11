<div class="sidebar portfolio-sidebar">
	<ul class="cat-portfolio">
		<li data-id="all" ><span class="filter_link cta"><?php esc_html_e('All', 'zoa'); ?></span></li>
		<?php renderPortfolioCategories(); ?>
	</ul>
	<div class="cat-portfolio-mobile-wraper" >
		<ul class="cat-portfolio-mobile" >
			<li data-id="all" data-value="All" class="init"><span class="filter_link cta"><?php esc_html_e('All', 'zoa'); ?></span></li>
			<li data-id="all" data-value="All" ><span class="filter_link cta"><?php esc_html_e('All', 'zoa'); ?></span></li>
			<?php renderPortfolioCategories(true); ?>
		</ul>
	</div>
</div>