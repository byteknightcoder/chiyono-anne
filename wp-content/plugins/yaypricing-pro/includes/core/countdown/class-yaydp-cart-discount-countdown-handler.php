<?php

namespace YAYDP\Core\Countdown;

class YAYDP_Cart_Discount_Countdown_Handler extends \YAYDP\Abstracts\YAYDP_Countdown_Handler {

	use \YAYDP\Traits\YAYDP_Singleton;

	protected function __construct() {

		parent::__construct();

		add_shortcode( 'yaydp-cart-discount-countdowns', function() {
			$html = '';
			ob_start();
			$this->render_shortcode();
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} );
		
	}

	public function is_enabled() {
		$countdown_settings = $this->get_countdown_settings();
		return empty( $countdown_settings['enable'] ) ? false : $countdown_settings['enable'];
	}

	protected function get_rules() {
		return \yaydp_get_cart_discount_rules();
	}

	public function get_countdown_settings() {
		return \YAYDP\Settings\YAYDP_Cart_Discount_Settings::get_instance()->get_countdown_settings();
	}

}
