<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by James Kemp on 19-June-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Iconic_PC_NS\StellarWP\Uplink\Messages;

class Unlicensed extends Message_Abstract {
	/**
	 * @inheritDoc
	 */
	public function get(): string {
        $message  = '<div class="notice notice-warning"><p>';
        $message .= esc_html__( 'No license entered.', 'jckpc' );
        $message .= '</p></div>';

		return $message;
	}
}
