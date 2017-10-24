<?php
if(!function_exists('getCountryID')){
	define('PLUGIN_VERSION', '2.2.6');
	define('BILLMATE_VERSION',  'PHP:Opencart:'.PLUGIN_VERSION );
	$ocVersion = defined('VERSION') ? VERSION : 'No-Value';
	define('BILLMATE_CLIENT','PHP:Opencart:'. $ocVersion.':PluginVersion:'.PLUGIN_VERSION);
	function getCountryID(){
		return 209;
		$country = strtoupper(shopp_setting('base_operations'));
		switch($country){
			case 'SE': return 209;
			case 'FI': return 73;
			case 'DK': return 59;
			case 'NO': return 164;
			default :
				return 209;
		}
	}
}

if(!function_exists('billmateCleanTotal')) {
    function billmateCleanTotal($total = 0) {
        $totalClean = $total;

        /* Adjustment for Ajax Quick Checkout by Dreamvention where $total is not a float as expected, instead array */
        if(is_array($total) AND count($total) > 0) {
            foreach($total AS $row) {
                if(isset($row['code']) AND strtolower(trim($row['code'])) == 'total') {
                    $totalClean = (isset($row['value'])) ? $row['value'] : 0;
                }
            }
        }

        return  $totalClean;
    }
}
//error_reporting(E_ALL);
//ini_set('display_errors', 1	);
