<?php

 class OpenCartBillmate{
    private $pclass = null;
    public $allPclass = array();
    
 	public static function ocGetModuleVersion(){
 	    return '2.0';
 	}
 	public function ocGetAllPClasses(){
        $pclasses = $this->getPCStorage();
 	    return array();
 	}

    public function ocFetchPClasses($countryCode, $eid, $key, $server){
        $base = dirname(__FILE__);
		require_once $base.'/Billmate.php';
		$ssl = true;
		$debug = false;
		$eid = (int)$eid;
		$key = (int)$key;

        $k = new BillMate( $eid ,$key,$ssl,false,$debug);
        
        $countryInfo = $this->country_iso_code_3( $countryCode);

        $additionalinfo['PaymentData'] = array(
	        "currency"=> 'SEK',
	        "country"=> 'se',
	        "language"=>'sv',
        );
        $this->pclass = $k->getPaymentplans($additionalinfo);
		if( is_array( $this->pclass) ){
			array_walk($this->pclass, 'correct_lang_billmate');
		}
        
        return $this;
    }
    public function getPClasses(){
        return $this->pclass;
    }
    public function country_iso_code_2($countrycode)
    {
        $countrycode = strtoupper($countrycode);
		switch ($countrycode) {
			// Sweden
			case 'SE':
				$country = 209;
				$language = 138;
				$encoding = 2;
				$currency = 0;
				break;
			// Finland
			case 'FI':
				$country = 73;
				$language = 37;
				$encoding = 4;
				$currency = 2;
				break;
			// Denmark
			case 'DN':
				$country = 59;
				$language = 27;
				$encoding = 5;
				$currency = 3;
				break;
			// Norway	
			case 'NO':
				$country = 164;
				$language = 97;
				$encoding = 3;
				$currency = 1;
				break;
			// Germany	
			case 'DE':
				$country = 81;
				$language = 28;
				$encoding = 6;
				$currency = 2;
				break;
			// Netherlands															
			case 'NL':
				$country = 154;
				$language = 101;
				$encoding = 7;
				$currency = 2;
				break;
		}
		return array('country' => $country, 'language' => $language, 'encoding' => $encoding, 'currency' => $currency);
    }
    
    public function country_iso_code_3($countrycode)
    {
        $countrycode = strtoupper($countrycode);
		switch ($countrycode) {
			// Sweden
			case 'SWE':
				$country = 'SE';
				$language = 'sv';
				$encoding = 2;
				$currency = 0;
				break;
			// Finland
			case 'FIN':
				$country = 73;
				$language = 37;
				$encoding = 4;
				$currency = 2;
				break;
			// Denmark
			case 'DNK':
				$country = 59;
				$language = 27;
				$encoding = 5;
				$currency = 3;
				break;
			// Norway	
			case 'NOR':
				$country = 164;
				$language = 97;
				$encoding = 3;
				$currency = 1;
				break;
			// Germany	
			case 'DEU':
				$country = 81;
				$language = 28;
				$encoding = 6;
				$currency = 2;
				break;
			// Netherlands															
			case 'NLD':
				$country = 154;
				$language = 101;
				$encoding = 7;
				$currency = 2;
				break;
		}
		return array('country' => $country, 'language' => $language, 'encoding' => $encoding, 'currency' => $currency);
    }
 }
 
function correct_lang_billmate(&$item, $index){

    $item['startfee'] = $item['startfee'] / 100;
    $item['handlingfee'] = $item['handlingfee'] / 100;
    $item['interestrate'] = $item['interestrate'] / 100;
    $item['minamount'] = $item['minamount'] / 100;
    $item['maxamount'] = $item['maxamount'] / 100;
}
?>