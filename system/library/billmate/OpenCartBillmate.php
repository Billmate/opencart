<?php

 class OpenCartBillmate{
    private $pclass = null;
    public $allPclass = array();
    
 	public static function ocGetModuleVersion(){
 	    return '1.0.0';
 	}
 	public function ocGetAllPClasses(){
        $pclasses = $this->getPCStorage();
 	    return array();
 	}
    public function ocClearPClasses() {
        $pclasses = $this->getPCStorage();
        $pclasses->clear($this->pcURI);
    }
    public function ocFetchPClasses($countryCode, $eid, $key, $server){
        $base = dirname(__FILE__);
		require_once $base.'/BillMate.php';
		include_once($base."/lib/xmlrpc.inc");
		include_once($base."/lib/xmlrpcs.inc");
		$ssl = true;
		$debug = false;
		$eid = (int)$eid;
		$key = (int)$key;

        $k = new BillMate( $eid ,$key,$ssl,$debug);
        
        $countryInfo = $this->country_iso_code_3( $countryCode);

        $additionalinfo = array(
	        "currency"=> $countryInfo['currency'],
	        "country"=>$countryInfo['country'],
	        "language"=>(int)$countryInfo['language'],
        );
        $this->pclass = $k->FetchCampaigns($additionalinfo);
        array_walk($this->pclass, 'correct_lang_billmate');
        
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
				$country = 209;
				$language = 138;
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
    $keys = array('pclassid', 'description','months', 'start_fee','invoice_fee','interest', 'mintotal', 'country', 'Type', 'expiry' );
    $item[1] = utf8_encode($item[1]);
    $item = array_combine( $keys, $item );
    $item['start_fee'] = $item['start_fee'] / 100;
    $item['invoice_fee'] = $item['invoice_fee'] / 100;
    $item['interest'] = $item['interest'] / 100;
    $item['mintotal'] = $item['mintotal'] / 100;
}
?>