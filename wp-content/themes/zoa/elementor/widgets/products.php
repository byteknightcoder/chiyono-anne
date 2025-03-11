<?php

namespace Elementor;

if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

class zoa_widget_products extends Widget_Base {

	public function get_categories() {
		return array( 'zoa-theme' );
	}

	public function get_name() {
		return 'products';
	}

	public function get_title() {
		return esc_html__( 'Woo - Products', 'zoa' );
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	protected function _register_controls() {
		$this->sectionGeneral();
		$this->sectionQuery();
		$this->sectionPagination();
	}

	private function sectionGeneral() {
		$this->start_controls_section(
			'product_content',
			array(
				'label' => esc_html__( 'General', 'zoa' ),
			)
		);

		$this->add_control(
			'col',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Columns', 'zoa' ),
				'default' => 4,
				'options' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				),
			)
		);

		$this->add_control(
			'pro_pagi',
			array(
				'type'         => Controls_Manager::SWITCHER,
				'label'        => esc_html__( 'Pagination', 'zoa' ),
				'default'      => '',
				'label_on'     => esc_html__( 'Yes', 'zoa' ),
				'label_off'    => esc_html__( 'No', 'zoa' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	private function sectionQuery() {
		$this->start_controls_section(
			'product_query',
			array(
				'label' => esc_html__( 'Query', 'zoa' ),
			)
		);

		$this->add_control(
			'product_cat',
			array(
				'label'     => esc_html__( 'Categories', 'zoa' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => zoa_get_narrow_data( 'term', 'product_cat' ),
				'multiple'  => true,
			)
		);

		$this->add_control(
			'pro_exclude',
			array(
				'label'     => esc_html__( 'Exclude product', 'zoa' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => zoa_get_narrow_data( 'post', 'product' ),
				'multiple'  => true,
			)
		);

		$this->add_control(
			'count',
			array(
				'label'     => esc_html__( 'Posts Per Page', 'zoa' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'     => esc_html__( 'Order By', 'zoa' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'id',
				'options'   => array(
					'id'   => esc_html__( 'ID', 'zoa' ),
                                        'menu_order+title' => esc_html__( 'menu_order title', 'zoa' ),
					'name' => esc_html__( 'Name', 'zoa' ),
					'date' => esc_html__( 'Date', 'zoa' ),
					'rand' => esc_html__( 'Random', 'zoa' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => esc_html__( 'Order', 'zoa' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ASC',
				'options'   => array(
					'ASC'   => esc_html__( 'ASC', 'zoa' ),
					'DESC'  => esc_html__( 'DESC', 'zoa' ),
				),
			)
		);

		$this->end_controls_section();
	}

	private function sectionPagination() {
		$this->start_controls_section(
			'pro_pagi_section',
			array(
				'label'     => esc_html__( 'Pagination', 'zoa' ),
				'condition' => array(
					'pro_pagi' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'pagi_position',
			array(
				'type'    => Controls_Manager::CHOOSE,
				'label'   => esc_html__( 'Alignment', 'zoa' ),
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'zoa' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'zoa' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'zoa' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'        => 'center',
				'tablet_default' => 'center',
				'mobile_default' => 'center',
				'selectors'      => array(
					'{{WRAPPER}} .ht-pagination' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagi_space',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => esc_html__( 'Space', 'zoa' ),
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'top'      => '30',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'tablet_default' => array(
					'top'      => '20',
					'right'    => '0',
					'bottom'   => '20',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'mobile_default' => array(
					'top'      => '15',
					'right'    => '0',
					'bottom'   => '15',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} .ht-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$cat_id   = $settings['product_cat'];
		$paged    = get_query_var( 'page' ) ? intval( get_query_var( 'page' ) ) : 1;
		$args     = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $settings['count'],
			'orderby'        => $settings['order_by'],
			'order'          => $settings['order'],
			'paged'          => $paged,
		);

		if ( ! empty( $settings['pro_exclude'] ) ) {
			$args['post__not_in'] = $settings['pro_exclude'];
		}

		if ( ! empty( $cat_id ) ) :
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $cat_id,
				),
			);
		endif;
                
                 $args['meta_query'] = array( array(
        'key'     => '_stock_status',
        'value'   => 'outofstock',
        'compare' => '!=',
    ) );

		$products_query = new \WP_Query( $args );
		if ( ! $products_query->have_posts() ) {
			return;
		}

		?>
		<div class="zoa-widget-products">
			
		<?php

		global $woocommerce_loop;

		$woocommerce_loop['columns'] = (int) $settings['col'];

		woocommerce_product_loop_start();

		while ( $products_query->have_posts() ) :
			$products_query->the_post();
			wc_get_template_part( 'content', 'product' );
		endwhile;

		woocommerce_product_loop_end();

		woocommerce_reset_loop();

		if ( 'yes' == $settings['pro_pagi'] ) {
			zoa_paging( $products_query, $paged );
		}
				wp_reset_postdata();
		?>
		</div>
		
		<?php
	}
}
Plugin::instance()->widgets_manager->register_widget_type( new zoa_widget_products() );
