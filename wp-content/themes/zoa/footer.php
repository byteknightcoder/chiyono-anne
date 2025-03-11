<?php
if ( function_exists( 'hfe_render_header' ) && hfe_footer_enabled()  ) :
	hfe_render_footer();
else:
?>

<footer id="theme-footer">
	<?php zoa_footer(); ?>
</footer>

<?php
endif;
// close tag content container `</div>`
zoa_after_content();

// quick view markup
if ( class_exists( 'woocommerce' ) ) {
	zoa_product_action();
}

if ( get_theme_mod( 'ajax_search', false ) ) {
	zoa_ajax_search_form();
} else {
	zoa_dialog_search_form();
}

?>
<a href="#" class="scroll-to-top js-to-top">
	<i class="ion-chevron-up"></i>
</a>
</div><!-- #theme-container -->
<?php
if ( true === get_theme_mod( 'loading', false ) ) {
	echo '<span class="is-loading-effect"></span>';
}
wp_footer();
?>
</body>
</html>
