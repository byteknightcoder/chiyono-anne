<?php
/**
 * Footer
 *
 * @package zoa
 */

if ( function_exists( 'hfe_render_header' ) && hfe_footer_enabled() ) {
	hfe_render_footer();
} else {
	?>
	<footer id="theme-footer">
		<?php zoa_footer(); ?>
	</footer>
	<?php
}

// close tag content container `</div>`.
zoa_after_content();

?>
<a href="#" class="scroll-to-top js-to-top">
	<i class="ion-chevron-up"></i>
</a>

<?php
if ( true === get_theme_mod( 'loading', false ) ) {
	echo '<span class="is-loading-effect"></span>';
}
