<?php
/**
 * Elementor Library
 *
 * @package zoa
 */

?>
<!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head itemscope="itemscope" itemtype="https://schema.org/WebSite">
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
		<?php
			the_content();
			wp_footer();
		?>
	</body>
</html>
