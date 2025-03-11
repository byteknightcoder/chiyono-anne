<?php 
function wccm_url_exists($url) 
{
    $headers = @get_headers($url);
	if(strpos($headers[0],'200')===false) return false;
	
	return true;
}
$wccm_result = get_option("_".$wccm_id);
$wccm_notice = !$wccm_result || $wccm_result != md5($_SERVER['SERVER_NAME']);
/* if($wccm_notice)
	remove_action( 'plugins_loaded', 'wccm_setup'); */
if(!$wccm_notice)
	wccm_setup();
function wccm_apache_request_headers() 
{
	$arh = array();
	$rx_http = '/\AHTTP_/';

	foreach($_SERVER as $key => $val) {
		if( preg_match($rx_http, $key) ) {
			$arh_key = preg_replace($rx_http, '', $key);
			$rx_matches = array();
	   // do some nasty string manipulations to restore the original letter case
	   // this should work in most cases
			$rx_matches = explode('_', $arh_key);

			if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
				foreach($rx_matches as $ak_key => $ak_val) {
					$rx_matches[$ak_key] = ucfirst($ak_val);
				}

				$arh_key = implode('-', $rx_matches);
			}

			$arh[$arh_key] = $val;
		}
	}

	return( $arh );
}
?>