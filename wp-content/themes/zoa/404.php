<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package zoa
 */
get_header();
$img         = get_theme_mod( 'not_found_image', get_template_directory_uri() . '/images/404.png' );
$title       = get_theme_mod( 'not_found_title', 'Whoops!' );
$subtitle    = get_theme_mod( 'not_found_subtitle', 'Your style does not exist!' );
$description = get_theme_mod( 'not_found_desc', 'Any question? Please contact us, we’re usually pretty quick. Cowboys to urbanites, professional athletes to ski bums, business suit to fishing guides.' );
?>

	<main id="main" class="site-main">
		<div class="not-found">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<?php if ( $img ) : ?>
						<figure>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php esc_attr_e( '404 image', 'zoa' ); ?>">
						</figure>
						<?php endif; ?>
					</div>
					<div class="col-md-5">
						<?php if ( $title ) : ?>
						<h2 class="title not-found-title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>

						<?php if ( $subtitle ) : ?>
						<h3 class="sub-title not-found-subtitle"><?php echo esc_html( $subtitle ); ?></h3>
						<?php endif; ?>

						<?php if ( $description ) : ?>
						<div class="not-found-desc"><?php echo wp_kses_decode_entities( $description ); ?></div>
						<?php endif; ?>

						<a class="back-to-home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go back', 'zoa' ); ?></a>
					</div>
				</div><!-- .row -->
			</div><!-- .container -->
		</div><!-- .not-found -->
	</main>

<?php
get_footer();
