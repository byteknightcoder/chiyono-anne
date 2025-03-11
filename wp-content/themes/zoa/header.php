<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head itemscope="itemscope" itemtype="https://schema.org/WebSite">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php zoa_preloader(); ?>

<div id="theme-container">
	<?php zoa_before_content(); ?>

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
