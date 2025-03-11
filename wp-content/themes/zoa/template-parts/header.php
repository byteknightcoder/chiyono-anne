<?php
/**
 * Header
 *
 * @package zoa
 */

zoa_preloader();
?>

<div id="theme-container">
	<?php do_action( 'zoa_before_content' ); // Open content container. ?>

	<?php
	if ( function_exists( 'hfe_render_header' ) && hfe_header_enabled() ) :

		hfe_render_header();

	else :

		$page_menu_layout = zoa_menu_slug();

		if ( get_theme_mod( 'sticky_header' ) && 'layout-5' !== $page_menu_layout ) {
			zoa_sticky_header();
		}
		?>

		<div id="theme-menu-layout">
			<?php zoa_menu_layout(); ?>
		</div>

	<?php endif; ?>

	<div id="theme-page-header">
		<?php zoa_page_header(); ?>
	</div>
