<?php
class ModelPaymentBillmatePartpayment extends Model {
    public function getMethod($address, $total) {        
        $this->language->load('payment/billmate_partpayment');
		
		$status = true;
		
		$billmate_partpayment = $this->config->get('billmate_partpayment');
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;

		if (!isset($billmate_partpayment['SWE'])) {
			$status = false;
		} elseif (!$billmate_partpayment['SWE']['status']) {
			$status = false;
		}
        
		if( $status){
            $available_countries = array_keys($this->config->get('billmatepart-country'));
            if(in_array($address['country_id'],$available_countries)){
                $status = true;
            } else {
                $status = false;
            }

		}

        if(!$status) return false;
        		
		$this->db->query("SET NAMES 'utf8'");
		$query = $this->db->query('SELECT value FROM '.DB_PREFIX.'setting where serialized=1 and `key`="'.$countryData['iso_code_3'].'"');
		if( empty( $query->row['value'] ) ) return false;
		
        $countryRates = unserialize( $query->row['value']);
        $countryRates = $countryRates[0];

       $method = array();

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
                
			$total = $this->currency->format($total, $country_to_currency[$countryData['iso_code_3']], '', false);
		
					
			foreach ($countryRates as $pclass) {

				// 0 - Campaign
				// 1 - Account
				// 2 - Special
				// 3 - Fixed
				if (!in_array($pclass['Type'], array(0, 2, 1, 3, 4))) {
					continue;
				}

				if ($pclass['Type'] == 2) {
					$monthly_cost = -1;
				} else {
					if ($total < $pclass['mintotal'] || ($total > $pclass['maxtotal'] && $pclass['maxtotal'] > 0)) {
						continue;
					}

					if ($pclass['Type'] == 3) {
						continue;
					} else {
						$sum = $total;

						$lowest_payment = $this->getLowestPaymentAccount($countryData['iso_code_3']);
						$monthly_cost = 0;

						$monthly_fee = $pclass['invoice_fee'];
						$start_fee = $pclass['start_fee'];

						$sum += $start_fee;

						$base = ($pclass['Type'] == 1);

						$minimum_payment = ($pclass['Type'] === 1) ? $this->getLowestPaymentAccount($countryData['iso_code_3']) : 0;

						if ($pclass['months'] == 0) {
							$payment = $sum;
						} elseif ($pclass['interest'] == 0) {
							$payment = $sum / $pclass['months'];
						} else {
							$interest_rate = $pclass['interest'] / (100.0 * 12);
							
							$payment = $sum * $interest_rate / (1 - pow((1 + $interest_rate), -$pclass['months']));
						}

						$payment += $monthly_fee;

						$balance = $sum;
						$pay_data = array();

						$months = $pclass['months'];
						
						while (($months != 0) && ($balance > 0.01)) {
							$interest = $balance * $pclass['interest'] / (100.0 * 12);
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

						if ($pclass['Type'] == 1 && $monthly_cost < $lowest_payment) {
							$monthly_cost = $lowest_payment;
						}

						if ($pclass['Type'] == 0 && $monthly_cost < $lowest_payment) {
							continue;
						}
					}
				}

				$payment_option[$pclass['pclassid']]['monthly_cost'] = round($monthly_cost,0);
				$payment_option[$pclass['pclassid']]['pclass_id'] = $pclass['pclassid'];
				$payment_option[$pclass['pclassid']]['months'] = $pclass['months'];
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
			$method = array(
				'code'       => 'billmate_partpayment',
				'title'      => sprintf($this->language->get('text_no_fee'), preg_replace('/[.,]0+/','',$this->currency->format($this->currency->convert($payment_option[0]['monthly_cost'], $country_to_currency[$countryData['iso_code_3']], $this->currency->getCode()), 1, 1)), $billmate_partpayment['SWE']['merchant'], strtolower($countryData['iso_code_2'])),
				'sort_order' => $billmate_partpayment['SWE']['sort_order']
			);
		}
		
        return $method;
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
