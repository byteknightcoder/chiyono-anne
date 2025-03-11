<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by James Kemp on 19-June-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Iconic_PC_NS\StellarWP\Uplink\Messages;

class Expired_Key extends Message_Abstract {
	/**
	 * @inheritDoc
	 */
	public function get(): string {
        $message  = '<div class="notice notice-warning"><p>';
        $message  .= __( 'Your license is expired', 'jckpc' );
		$message .= '<a href="https://evnt.is/195y" target="_blank" class="button button-primary">' .
			__( 'Renew Your License Now', 'jckpc' ) .
			'<span class="screen-reader-text">' .
			__( ' (opens in a new window)', 'jckpc' ) .
			'</span></a>';
        $message .= '</p>    </div>';

		return $message;
	}
}
