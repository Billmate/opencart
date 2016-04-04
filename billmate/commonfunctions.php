<?php
if(!function_exists('getCountryID')){
	define('PLUGIN_VERSION', '2.1.7');
	define('BILLMATE_VERSION',  'PHP:Opencart:'.PLUGIN_VERSION );
	define('BILLMATE_CLIENT','PHP:Opencart:'.PLUGIN_VERSION);
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
//error_reporting(E_ALL);
//ini_set('display_errors', 1	);