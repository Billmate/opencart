<?php
class ModelPaymentBillmateInvoice extends Model {
    public function getMethod($address, $total) {        
        $this->language->load('payment/billmate_invoice');
		
		$status = true;
		$allowedCurrencies = array(
            'SEK',
            'NOK',
            'DKK',
            'EUR',
            'USD',
            'GBP'
        );
        if(!in_array($this->session->data['currency'],$allowedCurrencies))
            $status = false;
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
            'GBR' => 'GBP'
		);				
        if(isset($billmate_invoice['SWE'])){
            if(isset($billmate_invoice['SWE']['mintotal']) && ($billmate_invoice['SWE']['mintotal'] != '' || $billmate_invoice['SWE']['mintotal'] != 0)){
                if($total < $billmate_invoice['SWE']['mintotal']){
                    $status = false;
                }
            }
            if(isset($billmate_invoice['SWE']['maxtotal']) && ($billmate_invoice['SWE']['maxtotal'] != '' || $billmate_invoice['SWE']['maxtotal'] != 0)){
                if($total > $billmate_invoice['SWE']['maxtotal']){
                    $status = false;
                }
            }
        }


		if( $status){
            $available_countries = array_keys($this->config->get('billmate_invoice_country'));
            if(in_array($address['country_id'],$available_countries)){
                $status = true;
            } else {
                $status = false;
            }

		}
        $method = array();
        
        if ($status) {
            $billmate_fee = $this->config->get('billmate_fee');
			if(version_compare(VERSION,'2.0','<')) {
                $description = empty($billmate_invoice['SWE']['description']) ? $this->language->get('text_title_fee') : $billmate_invoice['SWE']['description'];

                if (isset($billmate_fee[$countryData['iso_code_3']]) && $billmate_fee[$countryData['iso_code_3']]['status'] && $billmate_fee[$countryData['iso_code_3']]['fee'] > 0) {
                    $title = sprintf($this->language->get('text_fee'), $description, $this->currency->format($this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']), $this->session->data['currency'], ''), $this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']));

                } else {
                    $title = sprintf($this->language->get('text_no_fee'), $description, $billmate_invoice['SWE']['merchant'], strtolower($countryData['iso_code_2']));
                }


                $method = array(
                    'code' => 'billmate_invoice',
                    'title' => $title,
                    'sort_order' => $billmate_invoice['SWE']['sort_order'],
                );
            } else {
                $description = empty($billmate_invoice['SWE']['description']) ? $this->language->get('text_title_fee2') : $billmate_invoice['SWE']['description'];

                if (isset($billmate_fee[$countryData['iso_code_3']]) && $billmate_fee[$countryData['iso_code_3']]['status'] && $billmate_fee[$countryData['iso_code_3']]['fee'] > 0)  {
                    $title = sprintf($this->language->get('text_fee2'), $description, $this->currency->format($this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']),$this->session->data['currency'], ''), $this->tax->calculate($billmate_fee[$countryData['iso_code_3']]['fee'], $billmate_fee[$countryData['iso_code_3']]['tax_class_id']));

                } else {
                    $title = sprintf($this->language->get('text_no_fee2'),$description, $billmate_invoice['SWE']['merchant'], strtolower($countryData['iso_code_2']));
                }

                $method = array(
                    'code' => 'billmate_invoice',
                    'title' => $title,//$this->language->get('text_title'),
                    'sort_order' => $billmate_invoice['SWE']['sort_order'],
                    'terms' => ''//$title
                );
            }
        }
        
        return $method;
    }
}
?>
