<?php

/**
 * Add Gift product to cart if subtotal is above a certain amount
 */

// Exit if accessed directly 
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

if ( ! class_exists( 'Chiyono_Free_Gift_Box' ) ) :

	class Chiyono_Free_Gift_Box {

		private $gift_product_id = 2025643; // Define the targeted product ID for free offer
		private $threshold = 88000; // Threshold in JPY
		private $end_date = '2025-01-06 00:00:00'; // End date for the offer
		private $is_offer_active;

		/**
		 * Constructor.
		 */
		public function __construct() {

			// For Staging change the product ID , Temporary
			if ( isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'staging') !== false ) {
				$this->gift_product_id = 2046800;
			}

			$current_date = current_time('Y-m-d H:i:s');
			$this->is_offer_active = $current_date <= $this->end_date;
			
			add_action('woocommerce_after_calculate_totals', [ $this, 'add_gift_product_if_eligible' ], 10, 1);
			add_action('woocommerce_before_calculate_totals', [ $this, 'set_gift_product_price_to_zero' ], 10, 1);
		}

		/**
		 * Hook the function to 'woocommerce_after_calculate_totals'
		 * 
		 * @param object $cart
		 */
		function add_gift_product_if_eligible( $cart ) {
			if (is_admin() && !defined('DOING_AJAX')) {
				return;
			}

			if (!$this->is_offer_active) {
				return; // Exit if the offer has expired
			}
			
			$found = false;
			$cart_item_key_to_update = null;

			// Check if the gift product is already in the cart
			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( isset($cart_item['product_id']) && $cart_item['product_id'] == $this->gift_product_id && isset($cart_item['free_gift_offer'])) {
					$found = true;
					$cart_item_key_to_update = $cart_item_key;

					// Ensure the gift product quantity is set to 1
					if ($cart_item['quantity'] > 1) {
						$cart->set_quantity($cart_item['key'], 1);
					}

					break;
				}
			}
			
			// Calculate the subtotal including tax and subtract the discount
			$subtotal_incl_tax = $cart->get_subtotal() + $cart->get_subtotal_tax() - $cart->get_discount_total();
		
			// Add or remove the gift product based on eligibility
			if ($subtotal_incl_tax >= $this->threshold) {
				if (!$found) {
					$cart->add_to_cart( $this->gift_product_id, 1, 0, array(), array('free_gift_offer' => true) );
				} else {
					// If found and quantity is greater than 1, set it to 1
					if ( $cart->get_cart_item($cart_item_key_to_update)['quantity'] > 1 ) {
						$cart->set_quantity($cart_item_key_to_update, 1);
					}
				}
			} else {
				// Remove the gift product if subtotal is below threshold
				if ($found) {
					foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( $cart_item['product_id'] == $this->gift_product_id && $cart_item['data']->get_price() == 0 ) {
							$cart->remove_cart_item($cart_item_key_to_update);
						}
					}
				}
			}
			
		}

		/**
		 * Set the gift product price to 0
		 * 
		 * @param object $cart
		 */
		function set_gift_product_price_to_zero( $cart ) {

			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}

			// Iterate through cart items
			foreach ($cart->get_cart() as $cart_item) {
				if ( isset($cart_item['free_gift_offer']) && true === $cart_item['free_gift_offer'] && $cart_item['product_id'] == $this->gift_product_id ) {
					$cart_item['data']->set_price(0); // Set price to zero
				}
			}
		}

	}

endif;

return new Chiyono_Free_Gift_Box();
