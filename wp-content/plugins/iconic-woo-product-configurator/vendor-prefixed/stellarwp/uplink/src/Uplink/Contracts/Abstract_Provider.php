<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by James Kemp on 19-June-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Iconic_PC_NS\StellarWP\Uplink\Contracts;

use Iconic_PC_NS\StellarWP\ContainerContract\ContainerInterface;
use Iconic_PC_NS\StellarWP\Uplink\Config;

abstract class Abstract_Provider implements Provider_Interface {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor for the class.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct( $container = null ) {
		$this->container = $container ?: Config::get_container();
	}

}
