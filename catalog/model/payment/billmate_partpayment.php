<?php
require_once(dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php');
class ModelPaymentBillmatePartpayment extends Model {
    public function getMethod($address, $total) {

		$this->language->load('payment/billmate_partpayment');
		$status = true;
		$total = billmateCleanTotal($total);
		$billmate_partpayment = $this->config->get('billmate_partpayment');
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;
		$allowedCurrencies = array(
			'SEK'
		);
		if(!in_array($this->session->data['currency'],$allowedCurrencies))
			return false;
		if (!isset($billmate_partpayment['SWE'])) {
			$status = false;
			return false;
		} elseif (!$billmate_partpayment['SWE']['status']) {
			$status = false;
			return false;
		}

		if(isset($billmate_partpayment['SWE'])){
			if(isset($billmate_partpayment['SWE']['mintotal']) && ($billmate_partpayment['SWE']['mintotal'] != '' || $billmate_partpayment['SWE']['mintotal'] != 0)){
				if($total < $billmate_partpayment['SWE']['mintotal']){
					$status = false;
				}
			}
			if(isset($billmate_partpayment['SWE']['maxtotal'])  && ($billmate_partpayment['SWE']['maxtotal'] != '' || $billmate_partpayment['SWE']['maxtotal'] != 0)){
				if($total > $billmate_partpayment['SWE']['maxtotal']){
					$status = false;
				}
			}
		}
		if( $status){
            $available_countries = array_keys($this->config->get('billmate_partpayment_country'));

            if(in_array($address['country_id'],$available_countries)){
                $status = true;
            } else {
                $status = false;
            }

		}
        if(!$status) return false;


		$countryRates = $this->config->get('billmate_partpayment_pclasses');
		$countryRates = $countryRates['SWE'][0];
		$lang = strtolower($this->language->get('code'));
        if($lang == 'se')
            $lang = 'sv';
		if($lang == 'sv' || $lang == 'en'){
			$selectedLanguage = $lang;
		} else {
			$selectedLanguage = 'sv';
		}
	    if(isset($countryRates[$selectedLanguage]))
			$countryRates = $countryRates[$selectedLanguage];
	    else if(isset($countryRates['en']))
		    $countryRates = $countryRates['en'];
	    else
		    return false;



	    $method = array();

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
		$payment_option = array();
		if ($status) {
                
			$total = $this->currency->format($total, $store_currency, '', false);
		
			$i = 0;
			foreach ($countryRates as $pclass) {

				// 0 - Campaign
				// 1 - Account
				// 2 - Special
				// 3 - Fixed
				if (!isset($pclass['type']) || !in_array($pclass['type'], array(0, 2, 1, 3, 4))) {
					continue;
				}

				if ($pclass['type'] == 2) {
					$monthly_cost = -1;
				} else {
					if ($total < ($pclass['minamount']/100) || ($total > ($pclass['maxamount']/100) && ($pclass['maxamount']/100) > 0)) {
						continue;
					}

					if ($pclass['type'] == 3) {
						continue;
					} else {
						$sum = $total;

						$lowest_payment = $this->getLowestPaymentAccount($countryData['iso_code_3']);
						$monthly_cost = 0;

						$monthly_fee = $pclass['handlingfee']/100;
						$start_fee = $pclass['startfee']/100;

						$sum += $start_fee;

						$base = ($pclass['type'] == 1);

						$minimum_payment = ($pclass['type'] === 1) ? $this->getLowestPaymentAccount($countryData['iso_code_3']) : 0;

						if ($pclass['nbrofmonths'] == 0) {
							$payment = $sum;
						} elseif ($pclass['interestrate'] == 0) {
							$payment = $sum / $pclass['nbrofmonths'];
						} else {
							$interest_rate = $pclass['interestrate'] /(100 * 12);
							
							$payment = $sum * $interest_rate / (1 - pow((1 + $interest_rate), -$pclass['nbrofmonths']));
						}

						$payment += $monthly_fee;

						$balance = $sum;
						$pay_data = array();

						$months = $pclass['nbrofmonths'];
						
						while (($months != 0) && ($balance > 0.01)) {
							$interest = $balance * $pclass['interestrate'] / (100 * 12);
							$new_balance = $balance + $interest + $monthly_fee;

							if ($minimum_payment >= $new_balance || $payment >= $new_balance) {
								$pay_data[] = $new_balance;
								break;
							}

							$new_payment = max($payment, $minimum_payment);
							
							if ($base) {
								$new_payment = max($new_payment, $balance / 24.0 + $monthly_fee + $interest);
							}

							$balance = $new_balance - $new_payment;
							
							$pay_data[] = $new_payment;
							
							$months -= 1;
						}

						$monthly_cost = round(isset($pay_data[0]) ? ($pay_data[0]) : 0, 2);

						if ($monthly_cost < 0.01) {
							continue;
						}

						if ($pclass['type'] == 1 && $monthly_cost < $lowest_payment) {
							$monthly_cost = $lowest_payment;
						}

						if ($pclass['type'] == 0 && $monthly_cost < $lowest_payment) {
							continue;
						}
					}
				}

				$payment_option[$i]['monthly_cost'] = round($monthly_cost,0);
				$payment_option[$i]['pclass_id'] = $pclass['paymentplanid'];
				$payment_option[$i]['nbrofmonths'] = $pclass['nbrofmonths'];
                $i++;
			}
		}
		if (!$payment_option) {
			$status = false;
		}
		
		$sort_order = array(); 
		  
		foreach ($payment_option as $key => $value) {
			$sort_order[$key] = $value['monthly_cost'];
		}
	
		array_multisort($sort_order, SORT_ASC, $payment_option);	
					
		$method = array();
	
		if ($status) {
			$description = empty($billmate_partpayment['SWE']['description']) ? '': $billmate_partpayment['SWE']['description'];
			$convertedCurrency = $this->currency->convert($payment_option[0]['monthly_cost'], $store_currency, $this->session->data['currency']);
			$formattedCurrency = $this->currency->format($convertedCurrency, $this->session->data['currency'], 1);
			if(version_compare(VERSION,'2.0','<')){
				$method = array(
					'code'       => 'billmate_partpayment',
					'title'      => sprintf($this->language->get('text_no_fee'),$description, preg_replace('/[.,]0+/','', $formattedCurrency), $billmate_partpayment['SWE']['merchant'], strtolower($countryData['iso_code_2'])),
					'sort_order' => $billmate_partpayment['SWE']['sort_order']
				);
			} else {
				$method = array(
					'code'       => 'billmate_partpayment',
					'title'      => sprintf($this->language->get('text_no_fee2'),$description, preg_replace('/[.,]0+/','', $formattedCurrency), $billmate_partpayment['SWE']['merchant'], strtolower($countryData['iso_code_2'])),
					'sort_order' => $billmate_partpayment['SWE']['sort_order'],
					'terms' => ''
				);
			}

		}
		
        return $method;
    }

	public function getLowestPaymentFromTotal($total)
	{
		
		$billmate_partpayment = $this->config->get('billmate_partpayment');
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;

		$countryRates = $this->config->get('billmate_partpayment_pclasses');
		$countryRates = $countryRates['SWE'][0];
		$lang = $this->language->get('code');
        if($lang == 'se')
            $lang = 'sv';
		if($lang == 'sv' || $lang == 'en'){
			$selectedLanguage = $lang;
		} else {
			$selectedLanguage = 'en';
		}
		$countryRates = $countryRates[$selectedLanguage];


		$method = array();
		$status = true;
		if (!isset($billmate_partpayment['SWE'])) {
			$status = false;
		} elseif (!$billmate_partpayment['SWE']['status']) {
			$status = false;
		}

		// Maps countries to currencies
		$country_to_currency = array(
			'NOR' => 'NOK',
			'SWE' => 'SEK',
			'FIN' => 'EUR',
			'DNK' => 'DKK',
			'DEU' => 'EUR',
			'NLD' => 'EUR'
		);
		$payment_option = array();
		if ($status) {

			$total = $this->currency->format($total, $store_currency, '', false);

			$i = 0;
			foreach ($countryRates as $pclass) {

				// 0 - Campaign
				// 1 - Account
				// 2 - Special
				// 3 - Fixed
				if (!in_array($pclass['type'], array(0, 2, 1, 3, 4))) {
					continue;
				}

				if ($pclass['type'] == 2) {
					$monthly_cost = -1;
				} else {
					if ($total < ($pclass['minamount']/100) || ($total > ($pclass['maxamount']/100) && ($pclass['maxamount']/100) > 0)) {
						continue;
					}

					if ($pclass['type'] == 3) {
						continue;
					} else {
						$sum = $total;

						$lowest_payment = $this->getLowestPaymentAccount($countryData['iso_code_3']);
						$monthly_cost = 0;

						$monthly_fee = $pclass['handlingfee']/100;
						$start_fee = $pclass['startfee']/100;

						$sum += $start_fee;

						$base = ($pclass['type'] == 1);

						$minimum_payment = ($pclass['type'] === 1) ? $this->getLowestPaymentAccount($countryData['iso_code_3']) : 0;

						if ($pclass['nbrofmonths'] == 0) {
							$payment = $sum;
						} elseif ($pclass['interestrate'] == 0) {
							$payment = $sum / $pclass['nbrofmonths'];
						} else {
							$interest_rate = $pclass['interestrate'] / (100 * 12);

							$payment = $sum * $interest_rate / (1 - pow((1 + $interest_rate), -$pclass['nbrofmonths']));
						}

						$payment += $monthly_fee;

						$balance = $sum;
						$pay_data = array();

						$months = $pclass['nbrofmonths'];

						while (($months != 0) && ($balance > 0.01)) {
							$interest = $balance * $pclass['interestrate'] /  (100 * 12);
							$new_balance = $balance + $interest + $monthly_fee;

							if ($minimum_payment >= $new_balance || $payment >= $new_balance) {
								$pay_data[] = $new_balance;
								break;
							}

							$new_payment = max($payment, $minimum_payment);

							if ($base) {
								$new_payment = max($new_payment, $balance / 24.0 + $monthly_fee + $interest);
							}

							$balance = $new_balance - $new_payment;

							$pay_data[] = $new_payment;

							$months -= 1;
						}

						$monthly_cost = round(isset($pay_data[0]) ? ($pay_data[0]) : 0, 2);

						if ($monthly_cost < 0.01) {
							continue;
						}

						if ($pclass['type'] == 1 && $monthly_cost < $lowest_payment) {
							$monthly_cost = $lowest_payment;
						}

						if ($pclass['type'] == 0 && $monthly_cost < $lowest_payment) {
							continue;
						}
					}
				}

				$payment_option[$i]['monthly_cost'] = round($monthly_cost,0);
				$payment_option[$i]['pclass_id'] = $pclass['paymentplanid'];
				$payment_option[$i]['nbrofmonths'] = $pclass['nbrofmonths'];
				$i++;
			}
		}
		if (!$payment_option) {
			$status = false;
			return false;
		}

		$sort_order = array();

		foreach ($payment_option as $key => $value) {
			$sort_order[$key] = $value['monthly_cost'];
		}

		array_multisort($sort_order, SORT_ASC, $payment_option);

		return $payment_option[0];

	}
    private function getLowestPaymentAccount($country) {
        switch ($country) {
            case 'SWE':
                $amount = 50.0;
                break;
            case 'NOR':
                $amount = 95.0;
                break;
            case 'FIN':
                $amount = 8.95;
                break;
            case 'DNK':
                $amount = 89.0;
                break;
            case 'DEU':
            case 'NLD':
                $amount = 6.95;
                break;
            default:
                $log = new Log('billmate_account.log');
                $log->write('Unknown country ' . $country);
                
				$amount = NULL;
                break;
        }

        return $amount;
    }
}
?>
