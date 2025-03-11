<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Zoa blog posts widget.
 *
 * Zoa widget that displays blog posts.
 *
 * @since 1.0.0
 */
class Zoa_Blog_Posts extends Widget_Base {
	/**
	 * Get widget name.
	 *
	 * Retrieve blog posts widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'zoa-blog-posts';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve blog posts widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Blog Posts', 'zoa' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve blog posts widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the icon widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'zoa-theme' ];
	}

	/**
	 * Register blog posts widget controls.
	 *
	 * Add different input fields to allow the user to change and customize the widget settings
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		parent::_register_controls();

		$this->register_layout_section_controls();
		$this->register_query_section_controls();
		$this->register_pagination_section_controls();
	}

	protected function register_layout_section_controls() {
		$this->start_controls_section(
			'section_layout',
			[
				'label' => esc_html__( 'Layout', 'zoa' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'zoa' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'classic' => esc_html__( 'Classic', 'zoa' ),
					'overlap' => esc_html__( 'Overlap', 'zoa' ),
				],
				'default' => 'classic',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'zoa' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_query_section_controls() {
		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'zoa' ),
			]
		);

		$this->add_control(
			'categories',
			[
				'label'    => esc_html__( 'Categories', 'zoa' ),
				'type'     => Controls_Manager::SELECT2,
				'options'  => zoa_get_narrow_data( 'term', 'category' ),
				'multiple' => true,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => esc_html__( 'Posts Per Page', 'zoa' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order By', 'zoa' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => [
					'post_date'  => esc_html__( 'Date', 'zoa' ),
					'post_title' => esc_html__( 'Title', 'zoa' ),
					'rand'       => esc_html__( 'Random', 'zoa' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'zoa' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__( 'ASC', 'zoa' ),
					'desc' => esc_html__( 'DESC', 'zoa' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_pagination_section_controls() {
		$this->start_controls_section(
			'section_pagination',
			[
				'label' => esc_html__( 'Pagination', 'zoa' ),
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'        => esc_html__( 'Show Pagination', 'zoa' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'zoa' ),
				'label_off'    => esc_html__( 'Hide', 'zoa' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'pagination_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'zoa' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'zoa' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'zoa' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'zoa' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .ht-pagination' => 'text-align:{{VALUE}}',
				],
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label'          => esc_html__( 'Spacing', 'zoa' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => [ 'px', '%', 'em' ],
				'default'        => [
					'top'      => '30',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '20',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '15',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'      => [
					'{{WRAPPER}} .ht-pagination' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'      => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$categories = $settings['categories'];
		$classes    = 'wd-blog ht-grid ht-grid-' . $settings['columns'] .
					  ' ht-grid-tablet-' . $settings['columns_tablet'] .
					  ' ht-grid-mobile-' . $settings['columns_mobile'];
		$paged      = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$args       = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $settings['posts_per_page'],
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'paged'          => $paged,
		);
		if ( ! empty( $categories ) ) {
			$args['cat'] = $categories;
		}

		$the_query = new \WP_Query( $args );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				?>
				<div
					class="ht-grid-item post-layout-<?php echo esc_attr( $settings['layout'] ); ?>"
					<?php zoa_schema_markup( 'article' ); ?>
				>
					<article itemprop="mainEntityOfPage">
						<div class="entry-thumbnail">
							<a href="<?php the_permalink(); ?>">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'full' );
								} else {
									?>
									<img src="https://imgplaceholder.com/855x525/cccccc/757575/fa-image" alt="<?php echo esc_attr( 'Placeholder Image', 'zoa' ); ?>">
									<?php
								}
								?>

							</a>
						</div>
						<div
							class="entry-wrapper
							<?php
							if ( 'overlap' === $settings['layout'] ) {
								echo esc_attr( 'entry-wrapper--overlap' );
							}
							?>
							"
						>
							<div class="entry-meta">
								<div class="entry-categories">
									<?php echo zoa_blog_categories(); ?>
								</div>
								<time itemprop="datePublished" datetime="<?php echo get_the_time( 'c' ); ?>">
									<?php echo zoa_date_format(); ?>
								</time>
							</div>

							<h3 class="entry-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<?php if ( 'overlap' === $settings['layout'] ) : ?>
								<div class="read-more">
									<span class="ion-ios-arrow-thin-right read-more-icon"></span>
									<a href="<?php the_permalink(); ?>" class="read-more-link">
										Read more
									</a>
								</div>
							<?php endif; ?>
						</div>
					</article>
				</div>
			<?php
			endwhile;

			if ( 'yes' === $settings['show_pagination'] ) {
				zoa_paging( $the_query );
			}

			wp_reset_postdata();
			?>
		</div>
		<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Zoa_Blog_Posts() );
