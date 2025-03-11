<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by James Kemp on 19-June-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Iconic_PC_NS\StellarWP\Uplink\Contracts;

interface Provider_Interface {
	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register();
}
