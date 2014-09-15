<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';
class ControllerPaymentBillmatePartpayment extends Controller {
    protected function index() {
		$this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if ($order_info) {
			$store_currency = $this->config->get('config_currency');
			$store_country  = $this->config->get('config_country_id');
			$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
			$countryData    = $countryQuery->row;
			$this->language->load('payment/billmate_partpayment');
			$this->db->query('update '.DB_PREFIX.'order set order_status_id = 1 where order_id='.$this->session->data['order_id']);		   
			
			$this->data['text_information'] = $this->language->get('text_information');
			$this->data['text_additional'] = $this->language->get('text_additional');
			$this->data['text_payment_option'] = $this->language->get('text_payment_option');	
			$this->data['text_wait'] = $this->language->get('text_wait');
			$this->data['text_day'] = $this->language->get('text_day');	
			$this->data['text_month'] = $this->language->get('text_month');	
			$this->data['text_year'] = $this->language->get('text_year');	
			$this->data['text_male'] = $this->language->get('text_male');	
			$this->data['text_female'] = $this->language->get('text_female');		
			
			$this->data['entry_pno'] = $this->language->get('entry_pno');		
			$this->data['entry_dob'] = $this->language->get('entry_dob');	
			$this->data['entry_gender'] = $this->language->get('entry_gender');	
			$this->data['entry_street'] = $this->language->get('entry_street');	
			$this->data['entry_house_no'] = $this->language->get('entry_house_no');	
			$this->data['entry_house_ext'] = $this->language->get('entry_house_ext');	
			$this->data['entry_phone_no'] = sprintf($this->language->get('entry_phone_no'),$order_info['email'] );
			$this->data['entry_company'] = $this->language->get('entry_company');	
			
			$this->data['button_confirm'] = $this->language->get('button_confirm');
			$this->data['wrong_person_number'] = $this->language->get('your_billing_wrong');

			$this->data['days'] = array();
			
			for ($i = 1; $i <= 31; $i++) {
				$this->data['days'][] = array(
					'text'  => sprintf('%02d', $i), 
					'value' => $i
				);
			}
					
			$this->data['months'] = array();
			
			for ($i = 1; $i <= 12; $i++) {
				$this->data['months'][] = array(
					'text'  => sprintf('%02d', $i), 
					'value' => $i
				);
			}			
				
			$this->data['years'] = array();
	
			for ($i = date('Y'); $i >= 1900; $i--) {
				$this->data['years'][] = array(
					'text'  => $i,
					'value' => $i
				);
			}			
			// Store Taxes to send to Billmate
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();
			 
			$this->load->model('setting/extension');
			
			$sort_order = array(); 
			
			$results = $this->model_setting_extension->getExtensions('total');
			
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}
			
			array_multisort($sort_order, SORT_ASC, $results);
						
			$billmate_tax = array();
            
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);
		                    
                    $taxes = array();
                    
					$func = create_function('','');
					$oldhandler = set_error_handler($func);
					@$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
					set_error_handler($oldhandler);
                    
                    $amount = 0;
                    
                    foreach ($taxes as $tax_id => $value) {
                        $amount += $value;
                    }
                    
                    $billmate_tax[$result['code']] = $amount;
				}
			}
            
			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
                
                if (isset($billmate_tax[$value['code']])) {
                    if ($billmate_tax[$value['code']]) {
                        $total_data[$key]['tax_rate'] = abs($billmate_tax[$value['code']] / $value['value'] * 100);
                    } else {
                        $total_data[$key]['tax_rate'] = 0;
                    }
                } else {
                    $total_data[$key]['tax_rate'] = '0';
                }
			}
            
            $this->session->data['billmate'][$this->session->data['order_id']] = $total_data;
			
			// Order must have identical shipping and billing address or have no shipping address at all
			if ($this->cart->hasShipping() && !($order_info['payment_firstname'] == $order_info['shipping_firstname'] && $order_info['payment_lastname'] == $order_info['shipping_lastname'] && $order_info['payment_address_1'] == $order_info['shipping_address_1'] && $order_info['payment_address_2'] == $order_info['shipping_address_2'] && $order_info['payment_postcode'] == $order_info['shipping_postcode'] && $order_info['payment_city'] == $order_info['shipping_city'] && $order_info['payment_zone_id'] == $order_info['shipping_zone_id'] && $order_info['payment_zone_code'] == $order_info['shipping_zone_code'] && $order_info['payment_country_id'] == $order_info['shipping_country_id'] && $order_info['payment_country'] == $order_info['shipping_country'] && $order_info['payment_iso_code_3'] == $order_info['shipping_iso_code_3'])) {
				$this->data['error_warning'] = $this->language->get('error_address_match');
			} else {
				$this->data['error_warning'] = '';
			}
			
			// The title stored in the DB gets truncated which causes order_info.tpl to not be displayed properly
			$billmate_partpayment = $this->config->get('billmate_partpayment');
		    $this->db->query("SET NAMES 'utf8'");
		    $query = $this->db->query('SELECT value FROM '.DB_PREFIX.'setting where serialized=1 and `key`="'.$countryData['iso_code_3'].'"');
            $countryRates = unserialize( $query->row['value']);
            $countryRates = $countryRates[0];
			
			$this->data['merchant'] = $billmate_partpayment['SWE']['merchant'];
			$this->data['phone_number'] = $order_info['telephone'];
			
			$country_to_currency = array(
				'NOR' => 'NOK',
				'SWE' => 'SEK',
				'FIN' => 'EUR',
				'DNK' => 'DKK',
				'DEU' => 'EUR',
				'NLD' => 'EUR',
			);
						
			if ($order_info['payment_iso_code_3'] == 'DEU' || $order_info['payment_iso_code_3'] == 'NLD') {
				$address = $this->splitAddress($order_info['payment_address_1']);
				
				$this->data['street'] = $address[0];
				$this->data['street_number'] = $address[1];
				$this->data['street_extension'] = $address[2];
				
				if ($order_info['payment_iso_code_3'] == 'DEU') {
					$this->data['street_number'] = trim($address[1] . ' ' . $address[2]);
				}
			} else {
				$this->data['street'] = '';
				$this->data['street_number'] = '';
				$this->data['street_extension'] = '';
			}
						
			$this->data['company'] = $order_info['payment_company'];
			$this->data['iso_code_2'] = $countryData['iso_code_2'];
			$this->data['iso_code_3'] = $countryData['iso_code_3'];
			
			$payment_option = array();
			$total = $this->currency->format($order_info['total'], $country_to_currency[$countryData['iso_code_3']], '', false);
			foreach ($countryRates as $pclass) {                
				// 0 - Campaign
				// 1 - Account
				// 2 - Special
				// 3 - Fixed
				if (!in_array($pclass['Type'], array(0, 1, 3))) {
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
							$interest = $pclass['interest'] / (100.0 * 12);
							$payment = $sum * $interest / (1 - pow((1 + $interest), -$pclass['months']));
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
				
				$payment_option[$pclass['pclassid']]['pclass_id'] = $pclass['pclassid'];
				$payment_option[$pclass['pclassid']]['title'] = $pclass['description'];
				$payment_option[$pclass['pclassid']]['months'] = $pclass['months'];
				$payment_option[$pclass['pclassid']]['monthly_cost'] = $monthly_cost;
			}
			
			$sort_order = array(); 
			  
			foreach ($payment_option as $key => $value) {
				$sort_order[$key] = $value['pclass_id'];
			}
		
			$this->data['payment_options'] = array();
			
			foreach ($payment_option as $payment_option) {
				$this->data['payment_options'][] = array(
					'code'  => $payment_option['pclass_id'],
					'title' => sprintf($this->language->get('text_monthly_payment'), $payment_option['title'], $this->currency->format($this->currency->convert($payment_option['monthly_cost'], $country_to_currency[$countryData['iso_code_3']], $this->currency->getCode()), 1, 1))
				);
			}
			//$this->document->addStyle($style);
			//$this->document->addScript(HTTP_SERVER . 'catalog/view/javascript/module-tombola.js');
			//$this->data['description'] = $billmate_partpayment['SWE']['description'];
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_partpayment.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/billmate_partpayment.tpl';
			} else {
				$this->template = 'default/template/payment/billmate_partpayment.tpl';
			}
	
			$this->render();
		}
    }
    public function send() {
		$this->language->load('payment/billmate_partpayment');
		
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;
		
		if(isset($_POST['pno'])) $_POST['pno'] = trim($_POST['pno']);
		$json = array();
        if( empty( $_POST['pno'] ) ){
            $json['error'] = $this->language->get('requried_pno');
        }
        if( empty( $json['error']) ){
            if( empty( $_POST['confirm_verify_email'] ) ){
                $json['error'] = $this->language->get('requried_confirm_verify_email');
            }
        }
        if(isset($json['error'] ) ){
			$this->response->setOutput(my_json_encode($json));
			return;
		}
		$this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        		
		// Order must have identical shipping and billing address or have no shipping address at all
		if ($order_info) {
			if ($countryData['iso_code_3'] == 'DEU' && empty($this->request->post['deu_terms'])) {
				$json['error'] =  $this->language->get('error_deu_terms');
			}
			
			if (!$json) {
				$billmate_partpayment = $this->config->get('billmate_partpayment');
				
				require_once dirname(DIR_APPLICATION).'/billmate/BillMate.php';
				include_once(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpc.inc');
				include_once(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpcs.inc');
				
				$eid = (int)$billmate_partpayment['SWE']['merchant'];
				$key = (int)$billmate_partpayment['SWE']['secret'];
				$ssl = true;
				$debug = false;
				$k = new BillMate($eid,$key,$ssl,$debug, $billmate_partpayment['SWE']['server'] == 'beta');
				$country_to_currency = array(
					'NOR' => 'NOK',
					'SWE' => 'SEK',
					'FIN' => 'EUR',
					'DNK' => 'DKK',
					'DEU' => 'EUR',
					'NLD' => 'EUR',
				);
				
				$shippiISO = 'SWE';//!empty( $order_info['shipping_iso_code_3'] ) ? $order_info['shipping_iso_code_3'] : $order_info['payment_iso_code_3'];
				switch ($shippiISO) {
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
				
				$ship_address = $bill_address = array();
				if( !empty( $order_info['shipping_firstname'] ) ) {
				    $ship_address = array(
					    'email'           => $order_info['email'],
					    'telno'           => $order_info['telephone'],
					    'cellno'          => '',
					    'fname'           => $order_info['shipping_firstname'],
					    'lname'           => $order_info['shipping_lastname'],
					    'company'         => $order_info['shipping_company'],
					    'careof'          => '',
					    'street'          => $order_info['shipping_address_1'],
					    'house_number'    => isset($house_no)? $house_no: '',
					    'house_extension' => isset($house_ext)?$house_ext:'',
					    'zip'             => $order_info['shipping_postcode'],
					    'city'            => $order_info['shipping_city'],
					    'country'         => $country,
				    );
				}
				
				if( !empty( $order_info['payment_firstname'] ) ) {
				    $bill_address = array(
					    'email'           => $order_info['email'],
					    'telno'           => $order_info['telephone'],
					    'cellno'          => '',
					    'fname'           => $order_info['payment_firstname'],
					    'lname'           => $order_info['payment_lastname'],
					    'company'         => $order_info['payment_company'],
					    'careof'          => '',
					    'street'          => $order_info['payment_address_1'],
					    'house_number'    => '',
					    'house_extension' => '',
					    'zip'             => $order_info['payment_postcode'],
					    'city'            => $order_info['payment_city'],
					    'country'         => $country,
				    );
				}
				/*$product_query = $this->db->query("SELECT `name`, `model`, `price`, `quantity`, `tax` / `price` * 100 AS 'tax_rate' FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = " . (int) $order_info['order_id'] . " UNION ALL SELECT '', `code`, `amount`, '1', 0.00 FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = " . (int) $order_info['order_id'])->rows;	
				foreach ($product_query as $product) {
					$goods_list[] = array(
						'qty'   => (int)$product['quantity'],
						'goods' => array(
							'artno'    => $product['model'],
							'title'    => $product['name'],
							'price'    => (int)$this->currency->format($product['price']*100, $country_to_currency[$countryData['iso_code_3']], '', false),
							'vat'      => (float)($product['tax_rate']), ///$product['quantity']
							'discount' => 0.0,
							'flags'    => 0,
						)
					);
				}*/
				$products = $this->cart->getProducts();
				$goods_list = array();
				
				foreach ($products as $product) {
					$product_total_qty = $product['quantity'];
					
					if ($product['minimum'] > $product_total_qty) {
						$this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
					}
					$rates=0;
					$tax_rates = $this->tax->getRates($product['price'],$product['tax_class_id']);
					foreach($tax_rates as $rate){
						$rates+= $rate['rate'];
					}
					
					$goods_list[] = array(
						'qty'   => (int)$product_total_qty,
						'goods' => array(
							'artno'    => $product['model'],
							'title'    => $product['name'],
							'price'    => (int)$this->currency->format($product['price']*100, $this->currency->getCode(), '', false),
							'vat'      => (float)($rates),
							'discount' => 0.0,
							'flags'    => 0,
						)
					);
				}
				
				if (isset($this->session->data['billmate'][$this->session->data['order_id']])) {
					$totals = $this->session->data['billmate'][$this->session->data['order_id']];
				} else {
					$totals = array();
				}
                
				foreach ($totals as $total) {
					if ($total['code'] != 'sub_total' && $total['code'] != 'tax' && $total['code'] != 'total') {
						$flag = $total['code'] == 'handling' ? 16 : ( $total['code'] == 'shipping' ? 8 : 0);
						$goods_list[] = array(
							'qty'   => 1,
							'goods' => array(
								'artno'    => '',
								'title'    => $total['title'],
								'price'    => (int)$this->currency->format($total['value']*100, $this->currency->getCode(), '', false),
								'vat'      => (float)$total['tax_rate'],
								'discount' => 0.0,
								'flags'    => $flag,
							)
						);
					}
				}
                if (isset($this->request->post['code'])) {
                    $pclass = (int) $this->request->post['code'];
                } else {
                    $pclass = -1;
                }
				$pno = trim($this->request->post['pno']);
				
				$transaction = array(
					"order1"=>(string)$this->session->data['order_id'],
					"comment"=>'',
					"flags"=>0,
					"gender"=>0,
					"reference"=>"",
					"reference_code"=>"",
					"currency"=>$this->currency->getCode(),//$currency,
					"country"=>$country,
					"language"=>$this->language->get('code'),//$language,
					"pclass"=>$pclass,
					"shipInfo"=>array("delay_adjust"=>"1"),
					"travelInfo"=>array(),
					"incomeInfo"=>array(),
					"bankInfo"=>array(),
					"sid"=>array("time"=>microtime(true)),
					"extraInfo"=>array(array("cust_no"=>(string)$order_info['customer_id']))
				);
                if( !function_exists('match_usernamevp')){
                    function match_usernamevp( $str1, $str2 ){
                        $name1 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str1 ) ) );
                        $name2 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str2 ) ) );
                        $foundName = array_intersect($name1, $name2);
                        return count($foundName ) > 0;                        
                    }
                }
				try {
					$addr = $k->GetAddress($pno);
					if( !is_array( $addr ) ){
				        $json['error'] = utf8_encode( $addr );//.'<br/><br/><input type="button" onclick="modalWin.HideModalPopUp();" class="button" value="'.$this->language->get('Close').'" />';
				        $this->response->setOutput(my_json_encode($json ));
				        return;
					}
					foreach( $addr[0] as $key => $col ){
						$addr[0][$key] = utf8_encode($col);
					}
					
				} catch(Exception $e) {
						$json['error'] = $addr['error'];
					//Something went wrong
				   // echo "{$e->getMessage()} (#{$e->getCode()})\n";
				}
//				if(!$json['error']) //$order_info['shipping_firstname'] == $addr[0][0] and 
				
                $db = $this->registry->get('db');
                if( $db == NULL ) $db = $this->db;
                $countriesdata = array(209 =>'sweden', 73=> 'finland',59=> 'denmark', 164 => 'norway', 81 => 'germany', 15 => 'austria', 154 => 'netherlands' );
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE LOWER(name) = '" . $countriesdata[$addr[0][5]]. "' AND status = '1'");
                $countryinfo = $query->row;
				if( $addr[0][5] != 209 ){
					$countryname = $countryinfo['name'];
				} else {
					$countryname = '';
				}
                $fullname = $order_info['payment_firstname']. ' '.$order_info['payment_lastname'];
				if( empty( $addr[0][0])){
					$apiName = $fullname;
				} else {
					$apiName  = $addr[0][0].' '.$addr[0][1];
                }
                if( !empty( $order_info['shipping_firstname'] ) ){
                    $usership = $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'].' '.$order_info['shipping_company'];
                }
				$userbill = $order_info['payment_firstname'].' '.$order_info['payment_lastname'].' '.$order_info['payment_company'];
                if( !empty( $order_info['shipping_firstname'] ) ){
                    $address_same = match_usernamevp( $usership, $userbill ) && 
                        $this->matchString( $order_info['shipping_city'], $order_info['payment_city'] ) && 
		                $this->matchString( $order_info['shipping_postcode'], $order_info['payment_postcode'] ) &&
		                $this->matchString( $order_info['shipping_address_1'], $order_info['payment_address_1'] ) ;
                } else {
                    $address_same = true;
                }
				$firstArr = explode(' ', $order_info['shipping_firstname'] );
				$lastArr  = explode(' ', $order_info['shipping_lastname'] );
				
				if( empty( $addr[0][0] ) ){
					$apifirst = $firstArr;
					$apilast  = $lastArr ;
				}else {
					$apifirst = explode(' ', $addr[0][0] );
					$apilast  = explode(' ', $addr[0][1] );
				}
				$matchedFirst = array_intersect($apifirst, $firstArr );
				$matchedLast  = array_intersect($apilast, $lastArr );
				$apiMatchedName   = !empty($matchedFirst) && !empty($matchedLast);
				
				
                $this->session->data['mismatch'] = false;
				if( !( 
				        $this->matchString( $order_info['payment_city'], $addr[0][4] ) && 
				        $this->matchString( $order_info['payment_postcode'], $addr[0][3] ) &&
				        $this->matchString( $order_info['payment_address_1'], $addr[0][2]) &&
						$apiMatchedName
				) || !$address_same ){
				    $this->session->data['mismatch'] = true;
                    if(!(isset($this->request->get['geturl']) and $this->request->get['geturl']=="yes")){
                    $json['address'] = $addr[0][0].' '.$addr[0][1].'<br>'.$addr[0][2].'<br>'.$addr[0][3].'<br>'.$addr[0][4].'<br/>'.$countryname.'<div style="padding: 17px 0px;"></div><div><input type="button" value="'.$this->language->get('bill_yes').'" onclick="modalWin.HideModalPopUp();ajax_load(\'&geturl=yes\');" class="billmate_button"/></div><div><a onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() == 0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="linktag" >'.$this->language->get('bill_no').'</a></div>';
                    $json['error'] = "";
                    }
				}
			if(!isset($json['error'])){
				try {
                    $data = array(
                        'fname'      => Encoding::fixUTF8($addr[0][0]),
                        'lname'       => Encoding::fixUTF8($addr[0][1]),
                        'address_1'      => Encoding::fixUTF8($addr[0][2]),
                        'company'      => '',
                        'address_2'      => '',
                        'postcode'       => Encoding::fixUTF8($addr[0][3]),
                        'city'           => Encoding::fixUTF8($addr[0][4]),
                        'country_id'     => (int)$countryinfo['country_id'],
                        'zone_id'        => 0
                    );
				    if( empty($addr[0][0])){
                        $ship_api_address = array(
                            'company'         => Encoding::fixUTF8($addr[0][1]),
                            'street'          => Encoding::fixUTF8($addr[0][2]),
                            'zip'             => Encoding::fixUTF8($addr[0][3]),
                            'city'            => Encoding::fixUTF8($addr[0][4]),
                        );
                        $data['company']   = $addr[0][1];
                        $data['fname'] = $order_info['payment_firstname'];
                        $data['lname']  = $order_info['payment_lastname'];
				    } else{
                        $ship_api_address = array(
                            'fname'           => Encoding::fixUTF8($addr[0][0]),
                            'lname'           => Encoding::fixUTF8($addr[0][1]),
							'company'		  => '',
                            'street'          => Encoding::fixUTF8($addr[0][2]),
                            'zip'             => Encoding::fixUTF8($addr[0][3]),
                            'city'            => Encoding::fixUTF8($addr[0][4]),
                        );
                    }
                    $zonename = '';
                    $ship_address = array_merge($ship_address, $ship_api_address );
                    $bill_address = array_merge($bill_address, $ship_api_address );
					
                    $this->load->model('account/address');
                    if( $this->session->data['mismatch'] ){
						$data['firstname'] = $data['fname'];
						$data['lastname'] = $data['lname'];
						$this->session->data['shipping_address_id'] = 0;
						if( $this->customer->getId() > 0 ){
							$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($data);
						}
                        $this->session->data['shipping_postcode']   = $data['postcode'];
                        $this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
                        $this->session->data['payment_postcode']   = $data['postcode'];
						
$sql = "UPDATE `" . DB_PREFIX . "order` SET shipping_company='".$data['company']."', payment_company='".$data['company']."', shipping_firstname = '" . $db->escape($data['fname']) . "',firstname = '" . $db->escape($data['fname']) . "', lastname = '" . $db->escape($data['lastname']) . "', payment_firstname = '" . $db->escape($data['fname']) . "', payment_lastname = '" . $db->escape($data['lastname']) . "', payment_address_1 = '" . $db->escape($data['address_1']) . "', payment_address_2 = '', payment_city = '" . $db->escape($data['city']) . "', payment_postcode = '" . $db->escape($data['postcode']) . "', payment_zone = '" . $db->escape($zonename) . "', payment_zone_id = '" . (int)$data['zone_id'] . "', shipping_lastname = '" . $db->escape($data['lastname']) . "', shipping_address_1 = '" . $db->escape($data['address_1']) . "', shipping_address_2 = '', shipping_city = '" . $db->escape($data['city']) . "', shipping_postcode = '" . $db->escape($data['postcode']) . "', shipping_zone = '', shipping_zone_id = '0', date_modified = NOW(), shipping_country = '" . $this->db->escape($countryinfo['name']) . "', shipping_country_id = '" . (int)$countryinfo['country_id'] . "' where order_id = ". $this->session->data['order_id'];
$db->query($sql);
                     }
					 foreach($bill_address as $key => $col ){
						$bill_address[$key] = utf8_decode($col);
					 }
					 foreach($ship_address as $key => $col ){
						$ship_address[$key] = utf8_decode($col);
					 }
					$func = create_function('','');
					$oldhandler = set_error_handler($func);
					
					if( empty( $goods_list ) ){
						$result1 = 'Unable to find product in cart';
					} else {
						$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $this->db->escape($this->language->get('text_title')) . "' WHERE `order_id` = " . (int) $this->session->data['order_id']);						
						$result1 = $k->AddInvoice($pno,$bill_address,$ship_address,$goods_list,$transaction);
					}
                    
					if(!is_array($result1))
					{ 
						$json['address'] = '<p>'.utf8_encode($result1).'</p><input type="button" style="float:right" value="'.$this->language->get('close').'" onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() ==0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="button" />';
						$json['title'] = 'Betalning med Billmate misslyckades.';
						$json['height'] = 150;
					}
				    else
				    {
					    $billmate_order_status = $result1['2'];
					    if ($billmate_order_status == '1') {
                            $order_status = $billmate_partpayment['SWE']['accepted_status_id'];
					    } elseif ($billmate_order_status == '2') {
                            $order_status = $billmate_partpayment['SWE']['pending_status_id'];
					    } else {
						    $order_status = $this->config->get('config_order_status_id');
					    }
					
					    $comment = sprintf($this->language->get('text_comment'), $result1[0]);
					
						if( !$order_status ) {
							$this->model_checkout_order->confirm($this->session->data['order_id'], $order_status, $comment, 1);
						} else {
							$this->model_checkout_order->update($this->session->data['order_id'], $order_status, $comment, 1);
						}

					    $json['redirect'] = $this->url->link('checkout/success'); 
				    }
					set_error_handler($oldhandler);
				 } catch(Exception $e) {
					//Something went wrong
					//$json['error'] = "{$e->getMessage()} (#{$e->getCode()})\n";
				 }
			   }
			}
		}
        if(isset($json['error'] ) ) $json['error'] = utf8_encode($json['error']);		
		$this->response->setOutput(my_json_encode($json));
    }
    
    private function constructXmlrpc($data) {
        $type = gettype($data);
        switch ($type) {
            case 'boolean':
                if ($data == true) {
                    $value = 1;
                } else {
                    $value = false;
                }
                
                $xml = '<boolean>' . $value . '</boolean>';
                break;
            case 'integer':
                $xml = '<int>' . (int)$data . '</int>';
                break;
            case 'double':
                $xml = '<double>' . (float)$data . '</double>';
                break;
            case 'string':
                $xml = '<string>' . htmlspecialchars($data) . '</string>';
                break;
            case 'array':
                if ($data === array_values($data)) {
                    $xml = '<array><data>';
                    
                    foreach ($data as $value) {
                        $xml .= '<value>' . $this->constructXmlrpc($value) . '</value>';
                    }
                    
                    $xml .= '</data></array>';
                } else {
                    $xml = '<struct>';
                    
                    foreach ($data as $key => $value) {
                        $xml .= '<member>';
                        $xml .= '  <name>' . htmlspecialchars($key) . '</name>';
                        $xml .= '  <value>' . $this->constructXmlrpc($value) . '</value>';
                        $xml .= '</member>';
                    }
                    
                    $xml .= '</struct>';
                }
                break;
            default:
                $xml = '<nil/>';
                break;
        }
        
        return $xml;
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
                $log = new Log('billmate.log');
			    $log->write('Unknown country ' . $country);
                
				$amount = NULL;
                break;
		}
        
        return $amount;
    }
	
    private function splitAddress($address) {
        $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        
        $characters = array('-', '/', ' ', '#', '.', 'a', 'b', 'c', 'd', 'e',
                        'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
                        'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A',
                        'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
                        'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
                        'X', 'Y', 'Z');
        
        $specialchars = array('-', '/', ' ', '#', '.');
        $num_pos = $this->strposArr($address, $numbers, 2);
        $street_name = substr($address, 0, $num_pos);
        $street_name = trim($street_name);
        $number_part = substr($address, $num_pos);
        
        $number_part = trim($number_part);
        $ext_pos = $this->strposArr($number_part, $characters, 0);
        if ($ext_pos != '') {
            $house_number = substr($number_part, 0, $ext_pos);
            $house_extension = substr($number_part, $ext_pos);
            $house_extension = str_replace($specialchars, '', $house_extension);
        } else {
            $house_number = $number_part;
            $house_extension = '';
        }
        return array($street_name, $house_number, $house_extension);
    }
    
    private function strposArr($haystack, $needle, $where) {
        $defpos = 10000;
        
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $what) {
            if (($pos = strpos($haystack, $what, $where)) !== false) {
                if ($pos < $defpos) {
                    $defpos = $pos;
                }
            }
        }
        
        return $defpos;
    }	
    private function matchString( $string1, $string2 ){
        $string1 = explode(" ", strtolower( $string1 ) );
        $string2 = explode(" ", strtolower( $string2 ) );
		
		$filterStr1 = array();
		foreach( $string1 as $str1 ){
			if( trim($str1,'.') == $str1 ){
				$filterStr1[] = $str1;
			}
		}
		$filterStr2 = array();
		foreach( $string2 as $str2 ){
			if( trim($str2,'.') == $str2 ){
				$filterStr2[] = $str2;
			}
		}
		$foundName = array_intersect( $filterStr1, $filterStr2 );
        return count($foundName)>0;
    }
}
if(!class_exists('Encoding')){
class Encoding {
    
  protected static $win1252ToUtf8 = array(
        128 => "\xe2\x82\xac",
        130 => "\xe2\x80\x9a",
        131 => "\xc6\x92",
        132 => "\xe2\x80\x9e",
        133 => "\xe2\x80\xa6",
        134 => "\xe2\x80\xa0",
        135 => "\xe2\x80\xa1",
        136 => "\xcb\x86",
        137 => "\xe2\x80\xb0",
        138 => "\xc5\xa0",
        139 => "\xe2\x80\xb9",
        140 => "\xc5\x92",
        142 => "\xc5\xbd",
        145 => "\xe2\x80\x98",
        146 => "\xe2\x80\x99",
        147 => "\xe2\x80\x9c",
        148 => "\xe2\x80\x9d",
        149 => "\xe2\x80\xa2",
        150 => "\xe2\x80\x93",
        151 => "\xe2\x80\x94",
        152 => "\xcb\x9c",
        153 => "\xe2\x84\xa2",
        154 => "\xc5\xa1",
        155 => "\xe2\x80\xba",
        156 => "\xc5\x93",
        158 => "\xc5\xbe",
        159 => "\xc5\xb8"
  );
  
    protected static $brokenUtf8ToUtf8 = array(
        "\xc2\x80" => "\xe2\x82\xac",
        
        "\xc2\x82" => "\xe2\x80\x9a",
        "\xc2\x83" => "\xc6\x92",
        "\xc2\x84" => "\xe2\x80\x9e",
        "\xc2\x85" => "\xe2\x80\xa6",
        "\xc2\x86" => "\xe2\x80\xa0",
        "\xc2\x87" => "\xe2\x80\xa1",
        "\xc2\x88" => "\xcb\x86",
        "\xc2\x89" => "\xe2\x80\xb0",
        "\xc2\x8a" => "\xc5\xa0",
        "\xc2\x8b" => "\xe2\x80\xb9",
        "\xc2\x8c" => "\xc5\x92",
        
        "\xc2\x8e" => "\xc5\xbd",
        
        
        "\xc2\x91" => "\xe2\x80\x98",
        "\xc2\x92" => "\xe2\x80\x99",
        "\xc2\x93" => "\xe2\x80\x9c",
        "\xc2\x94" => "\xe2\x80\x9d",
        "\xc2\x95" => "\xe2\x80\xa2",
        "\xc2\x96" => "\xe2\x80\x93",
        "\xc2\x97" => "\xe2\x80\x94",
        "\xc2\x98" => "\xcb\x9c",
        "\xc2\x99" => "\xe2\x84\xa2",
        "\xc2\x9a" => "\xc5\xa1",
        "\xc2\x9b" => "\xe2\x80\xba",
        "\xc2\x9c" => "\xc5\x93",
        
        "\xc2\x9e" => "\xc5\xbe",
        "\xc2\x9f" => "\xc5\xb8"
  );
    
  protected static $utf8ToWin1252 = array(
       "\xe2\x82\xac" => "\x80",
       
       "\xe2\x80\x9a" => "\x82",
       "\xc6\x92"     => "\x83",
       "\xe2\x80\x9e" => "\x84",
       "\xe2\x80\xa6" => "\x85",
       "\xe2\x80\xa0" => "\x86",
       "\xe2\x80\xa1" => "\x87",
       "\xcb\x86"     => "\x88",
       "\xe2\x80\xb0" => "\x89",
       "\xc5\xa0"     => "\x8a",
       "\xe2\x80\xb9" => "\x8b",
       "\xc5\x92"     => "\x8c",
       
       "\xc5\xbd"     => "\x8e",
       
       
       "\xe2\x80\x98" => "\x91",
       "\xe2\x80\x99" => "\x92",
       "\xe2\x80\x9c" => "\x93",
       "\xe2\x80\x9d" => "\x94",
       "\xe2\x80\xa2" => "\x95",
       "\xe2\x80\x93" => "\x96",
       "\xe2\x80\x94" => "\x97",
       "\xcb\x9c"     => "\x98",
       "\xe2\x84\xa2" => "\x99",
       "\xc5\xa1"     => "\x9a",
       "\xe2\x80\xba" => "\x9b",
       "\xc5\x93"     => "\x9c",
       
       "\xc5\xbe"     => "\x9e",
       "\xc5\xb8"     => "\x9f"
    );
  static function toUTF8($text){
  /**
   * Function Encoding::toUTF8
   *
   * This function leaves UTF8 characters alone, while converting almost all non-UTF8 to UTF8.
   * 
   * It assumes that the encoding of the original string is either Windows-1252 or ISO 8859-1.
   *
   * It may fail to convert characters to UTF-8 if they fall into one of these scenarios:
   *
   * 1) when any of these characters:   ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞß
   *    are followed by any of these:  ("group B")
   *                                    ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶•¸¹º»¼½¾¿
   * For example:   %ABREPRESENT%C9%BB. «REPRESENTÉ»
   * The "«" (%AB) character will be converted, but the "É" followed by "»" (%C9%BB) 
   * is also a valid unicode character, and will be left unchanged.
   *
   * 2) when any of these: àáâãäåæçèéêëìíîï  are followed by TWO chars from group B,
   * 3) when any of these: ðñòó  are followed by THREE chars from group B.
   *
   * @name toUTF8
   * @param string $text  Any string.
   * @return string  The same string, UTF8 encoded
   *
   */
    if(is_array($text))
    {
      foreach($text as $k => $v)
      {
        $text[$k] = self::toUTF8($v);
      }
      return $text;
    } elseif(is_string($text)) {
    
      $max = strlen($text);
      $buf = "";
      for($i = 0; $i < $max; $i++){
          $c1 = $text{$i};
          if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
            $c2 = $i+1 >= $max? "\x00" : $text{$i+1};
            $c3 = $i+2 >= $max? "\x00" : $text{$i+2};
            $c4 = $i+3 >= $max? "\x00" : $text{$i+3};
              if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2;
                      $i++;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2 . $c3;
                      $i = $i + 2;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
                  if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                      $buf .= $c1 . $c2 . $c3;
                      $i = $i + 2;
                  } else { //not valid UTF8.  Convert it.
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = ($c1 & "\x3f") | "\x80";
                      $buf .= $cc1 . $cc2;
                  }
              } else { //doesn't look like UTF8, but should be converted
                      $cc1 = (chr(ord($c1) / 64) | "\xc0");
                      $cc2 = (($c1 & "\x3f") | "\x80");
                      $buf .= $cc1 . $cc2;
              }
          } elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
                if(isset(self::$win1252ToUtf8[ord($c1)])) { //found in Windows-1252 special cases
                    $buf .= self::$win1252ToUtf8[ord($c1)];
                } else {
                  $cc1 = (chr(ord($c1) / 64) | "\xc0");
                  $cc2 = (($c1 & "\x3f") | "\x80");
                  $buf .= $cc1 . $cc2;
                }
          } else { // it doesn't need convesion
              $buf .= $c1;
          }
      }
      return $buf;
    } else {
      return $text;
    }
  }
  static function toWin1252($text) {
    if(is_array($text)) {
      foreach($text as $k => $v) {
        $text[$k] = self::toWin1252($v);
      }
      return $text;
    } elseif(is_string($text)) {
      return utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), self::toUTF8($text)));
    } else {
      return $text;
    }
  }
  static function toISO8859($text) {
    return self::toWin1252($text);
  }
  static function toLatin1($text) {
    return self::toWin1252($text);
  }
  static function fixUTF8($text){
    if(is_array($text)) {
      foreach($text as $k => $v) {
        $text[$k] = self::fixUTF8($v);
      }
      return $text;
    }
    $last = "";
    while($last <> $text){
      $last = $text;
      $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
    }
    $text = self::toUTF8(utf8_decode(str_replace(array_keys(self::$utf8ToWin1252), array_values(self::$utf8ToWin1252), $text)));
    return $text;
  }
  
  static function UTF8FixWin1252Chars($text){
    // If you received an UTF-8 string that was converted from Windows-1252 as it was ISO8859-1 
    // (ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
    // See: http://en.wikipedia.org/wiki/Windows-1252
    
    return str_replace(array_keys(self::$brokenUtf8ToUtf8), array_values(self::$brokenUtf8ToUtf8), $text);
  }
  
  static function removeBOM($str=""){
    if(substr($str, 0,3) == pack("CCC",0xef,0xbb,0xbf)) {
      $str=substr($str, 3);
    }
    return $str;
  }
  
  public static function normalizeEncoding($encodingLabel)
  {
    $encoding = strtoupper($encodingLabel);
    $enc = preg_replace('/[^a-zA-Z0-9\s]/', '', $encoding);
    $equivalences = array(
        'ISO88591' => 'ISO-8859-1',
        'ISO8859'  => 'ISO-8859-1',
        'ISO'      => 'ISO-8859-1',
        'LATIN1'   => 'ISO-8859-1',
        'LATIN'    => 'ISO-8859-1',
        'UTF8'     => 'UTF-8',
        'UTF'      => 'UTF-8',
        'WIN1252'  => 'ISO-8859-1',
        'WINDOWS1252' => 'ISO-8859-1'
    );
    
    if(empty($equivalences[$encoding])){
      return 'UTF-8';
    }
   
    return $equivalences[$encoding];
  }
  public static function encode($encodingLabel, $text)
  {
    $encodingLabel = self::normalizeEncoding($encodingLabel);
    if($encodingLabel == 'UTF-8') return Encoding::toUTF8($text);
    if($encodingLabel == 'ISO-8859-1') return Encoding::toLatin1($text);
  }
}
}
