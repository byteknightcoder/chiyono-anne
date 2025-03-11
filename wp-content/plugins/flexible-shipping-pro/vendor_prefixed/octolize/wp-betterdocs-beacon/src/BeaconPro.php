<?php

namespace FSProVendor\Octolize\BetterDocs\Beacon;

use FSProVendor\WPDesk\Beacon\BeaconShouldShowStrategy;
/**
 * Can display BetterDocs Beacon without confirmation.
 */
class BeaconPro extends \FSProVendor\Octolize\BetterDocs\Beacon\Beacon
{
    public function __construct(\FSProVendor\Octolize\BetterDocs\Beacon\BeaconOptions $beacon_options, \FSProVendor\WPDesk\Beacon\BeaconShouldShowStrategy $strategy, $assets_url, $beacon_search_elements_class = 'search-input')
    {
        parent::__construct($beacon_options, $strategy, $assets_url, $beacon_search_elements_class);
        $this->confirmation_message = '';
    }
}
