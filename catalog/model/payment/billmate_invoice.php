<?php
class ModelPaymentBillmateInvoice extends Model {
    public function getMethod($address, $total) {        
        $this->language->load('payment/billmate_invoice');
		
		$status = true;
		
		$billmate_invoice = $this->config->get('billmate_invoice');
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;
		
		if (!isset($billmate_invoice['SWE'])) {
			$status = false;
		} elseif (!$billmate_invoice['SWE']['status']) {
			$status = false;
		}

		// Maps countries to currencies
		$country_to_currency = array(
			'NOR' => 'NOK',
			'SWE' => 'SEK',
			'FIN' => 'EUR',
			'DNK' => 'DKK',
			'DEU' => 'EUR',
			'NLD' => 'EUR',
		);				
        
		if ($status) {  
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$billmate_invoice['SWE']['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			$billmate_fee = $this->config->get('billmate_fee');
		//echo	$total = $total - $billmate_fee['SWE']['fee'];
			
			if ($billmate_invoice['SWE']['mintotal'] > 0 && $billmate_invoice['SWE']['mintotal'] > $total) {

				$status = false;
			} elseif (  
			            !empty($billmate_invoice['SWE']['maxtotal']) && 
			            $billmate_invoice['SWE']['maxtotal'] > 0 && $billmate_invoice['SWE']['maxtotal'] < $total
			       ) {
				$status = false;

			} elseif (!$billmate_invoice['SWE']['geo_zone_id']) {
				$status = true;

			}
			if (!isset($country_to_currency[$countryData['iso_code_3']]) || !$this->currency->has($country_to_currency[$countryData['iso_code_3']])) {
				$status = false;
			} 
		}

		if( $status){
			//$sql = 'select * from '. DB_PREFIX . 'geo_zone where geo_zone_id = '.(int)$billmate_invoice['SWE']['geo_zone_id'];
			$sql = 'select * from ' . DB_PREFIX . 'zone_to_geo_zone where country_id='.(int)$address['country_id'].' and geo_zone_id = '.(int)$billmate_invoice['SWE']['geo_zone_id'];
			$query2 = $this->db->query($sql);
				
			if( $billmate_invoice['SWE']['geo_zone_id'] == 0  ){
				$status = true;
			}elseif($query2->num_rows){ ///* && $address['iso_code_2'] == 'SE' && $query2->row['name'] == 'Inom Sverige' 
				$status = true;
			}else{
				$status = false;
			}
		}
        $method = array();
        
        if ($status) {
            $billmate_fee = $this->config->get('billmate_fee');
			$description = empty($billmate_invoice['SWE']['description']) ?'Billmate Faktura - Betala inom 14-dagar' : $billmate_invoice['SWE']['description'];
			
            if ($billmate_fee[$countryData['iso_code_3']]['status']) {
                $title = sprintf($this->language->get('text_fee'),$description, $this->currency->format($this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']), '', ''), $this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']));
                
            } else {
                $title = sprintf($this->language->get('text_no_fee'),$description, $billmate_invoice['SWE']['merchant'], strtolower($countryData['iso_code_2']));
            }

           
            $method = array(
                'code'       => 'billmate_invoice',
                'title'      => $title,
                'sort_order' => $billmate_invoice['SWE']['sort_order']
            );
        }
        
        return $method;
    }
}
?>
