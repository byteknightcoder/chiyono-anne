<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Zoa_Countdown extends Widget_Base {

	public function get_name() {
		return 'zoa-countdown';
	}

	public function get_title() {
		return esc_html__( 'Countdown', 'zoa' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_categories() {
		return [ 'zoa-theme' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_countdown',
			[
				'label' => esc_html__( 'Countdown', 'zoa' ),
			]
		);

		$this->add_control(
			'due_date',
			[
				'label'          => esc_html__( 'Due Date', 'zoa' ),
				'type'           => Controls_Manager::DATE_TIME,
				'default'        => '10/20/2020',
				'picker_options' => array(
					'dateFormat' => 'm/d/Y',
					'enableTime' => false,
				),
			]
		);

		$this->add_control(
			'label_days',
			[
				'label'   => esc_html__( 'Days Label', 'zoa' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Days', 'zoa' ),
			]
		);

		$this->add_control(
			'label_hours',
			[
				'label'   => esc_html__( 'Hours Label', 'zoa' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Hours', 'zoa' ),
			]
		);

		$this->add_control(
			'label_minutes',
			[
				'label'   => esc_html__( 'Minutes Label', 'zoa' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Minutes', 'zoa' ),
			]
		);

		$this->add_control(
			'label_seconds',
			[
				'label'   => esc_html__( 'Seconds Label', 'zoa' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Seconds', 'zoa' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => esc_html__( 'Boxes', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'          => esc_html__( 'Alignment', 'zoa' ),
				'type'           => Controls_Manager::CHOOSE,
				'options'        => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'zoa' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'zoa' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'zoa' ),
						'icon'  => 'fa fa-align-left',
					),
				),
				'default'        => 'center',
				'tablet_default' => 'center',
				'mobile_default' => 'center',
				'selectors'      => array(
					'{{WRAPPER}} .zoa-countdown-wrapper' => 'text-align: {{VALUE}}',
				),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => esc_html__( 'Content', 'zoa' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_digits',
			[
				'label' => esc_html__( 'Digits', 'zoa' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'digits_color',
			[
				'label'     => esc_html__( 'Color', 'zoa' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zoa-countdown-digit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digits_typography',
				'selector' => '{{WRAPPER}} .zoa-countdown-digit',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'heading_labels',
			[
				'label'     => esc_html__( 'Labels', 'zoa' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'labels_color',
			[
				'label'     => esc_html__( 'Color', 'zoa' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .zoa-countdown-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'labels_typography',
				'selector' => '{{WRAPPER}} .zoa-countdown-label',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_countdown_script();
		?>
		<div class="zoa-countdown-wrapper" data-date="<?php echo esc_attr( $settings['due_date'] ); ?>">
			<div class="zoa-countdown-item">
				<span id="<?php echo esc_attr( uniqid( 'days-' ) ); ?>" class="zoa-countdown-digit zoa-count"></span>
				<span class="zoa-countdown-label"><?php echo esc_html( $settings['label_days'] ); ?></span>
			</div>

			<div class="zoa-countdown-item">
				<span id="<?php echo esc_attr( uniqid( 'hours-' ) ); ?>" class="zoa-countdown-digit"></span>
				<span class="zoa-countdown-label"><?php echo esc_html( $settings['label_hours'] ); ?></span>
			</div>

			<div class="zoa-countdown-item">
				<span id="<?php echo esc_attr( uniqid( 'minutes-' ) ); ?>" class="zoa-countdown-digit"></span>
				<span class="zoa-countdown-label"><?php echo esc_html( $settings['label_minutes'] ); ?></span>
			</div>

			<div class="zoa-countdown-item">
				<span id="<?php echo esc_attr( uniqid( 'seconds-' ) ); ?>" class="zoa-countdown-digit"></span>
				<span class="zoa-countdown-label"><?php echo esc_html( $settings['label_seconds'] ); ?></span>
			</div>
		</div>
		<?php
	}

	protected function add_countdown_script() {
		wp_enqueue_script( 'countdown' );
		wp_add_inline_script(
			'countdown',
			"document.addEventListener( 'DOMContentLoaded', function() {
				var countdowns = document.querySelectorAll('.zoa-countdown-wrapper');

				if ( ! countdowns ) return;

				countdowns.forEach(function(countdownContainer) {

				var	digits = Array.from(countdownContainer.children),
					targetDate = countdownContainer.getAttribute('data-date'),
					countdown = Doom({
						targetDate: targetDate,
						ids: {
							days: digits[0].querySelector('.zoa-countdown-digit').id,
							hours: digits[1].querySelector('.zoa-countdown-digit').id,
							mins: digits[2].querySelector('.zoa-countdown-digit').id,
							secs: digits[3].querySelector('.zoa-countdown-digit').id,
						}
					});

				countdown.doom();
				});
			});",
			'after'
		);
	}
}

Plugin::instance()->widgets_manager->register_widget_type( new Zoa_Countdown() );
