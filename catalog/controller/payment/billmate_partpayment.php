<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';
class ControllerPaymentBillmatePartpayment extends Controller {
    public function index() {
		$this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		if ($order_info) {
			$store_currency = $this->config->get('config_currency');
			$store_country  = $this->config->get('config_country_id');
			$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
			$countryData    = $countryQuery->row;
			$this->language->load('payment/billmate_partpayment');
			//$this->db->query('update '.DB_PREFIX.'order set order_status_id = 1 where order_id='.$this->session->data['order_id']);		   
			
			$data['text_information'] = $this->language->get('text_information');
			$data['text_additional'] = $this->language->get('text_additional');
			$data['text_payment_option'] = $this->language->get('text_payment_option');	
			$data['text_wait'] = $this->language->get('text_wait');
			$data['text_day'] = $this->language->get('text_day');	
			$data['text_month'] = $this->language->get('text_month');	
			$data['text_year'] = $this->language->get('text_year');	
			$data['text_male'] = $this->language->get('text_male');	
			$data['text_female'] = $this->language->get('text_female');

			$data['help_pno'] = $this->language->get('help_pno');
			$data['entry_pno'] = $this->language->get('entry_pno');		
			$data['entry_dob'] = $this->language->get('entry_dob');	
			$data['entry_gender'] = $this->language->get('entry_gender');	
			$data['entry_street'] = $this->language->get('entry_street');	
			$data['entry_house_no'] = $this->language->get('entry_house_no');	
			$data['entry_house_ext'] = $this->language->get('entry_house_ext');	
			$data['entry_phone_no'] = sprintf($this->language->get('entry_phone_no'),$order_info['email'] );
			$data['entry_company'] = $this->language->get('entry_company');	
			
			$data['button_confirm'] = $this->language->get('button_confirm');
			$data['wrong_person_number'] = $this->language->get('your_billing_wrong');

			$data['days'] = array();
			
			for ($i = 1; $i <= 31; $i++) {
				$data['days'][] = array(
					'text'  => sprintf('%02d', $i), 
					'value' => $i
				);
			}
					
			$data['months'] = array();
			
			for ($i = 1; $i <= 12; $i++) {
				$data['months'][] = array(
					'text'  => sprintf('%02d', $i), 
					'value' => $i
				);
			}			
				
			$data['years'] = array();
	
			for ($i = date('Y'); $i >= 1900; $i--) {
				$data['years'][] = array(
					'text'  => $i,
					'value' => $i
				);
			}			

			// Order must have identical shipping and billing address or have no shipping address at all
			if ($this->cart->hasShipping() && !($order_info['payment_firstname'] == $order_info['shipping_firstname'] && $order_info['payment_lastname'] == $order_info['shipping_lastname'] && $order_info['payment_address_1'] == $order_info['shipping_address_1'] && $order_info['payment_address_2'] == $order_info['shipping_address_2'] && $order_info['payment_postcode'] == $order_info['shipping_postcode'] && $order_info['payment_city'] == $order_info['shipping_city'] && $order_info['payment_zone_id'] == $order_info['shipping_zone_id'] && $order_info['payment_zone_code'] == $order_info['shipping_zone_code'] && $order_info['payment_country_id'] == $order_info['shipping_country_id'] && $order_info['payment_country'] == $order_info['shipping_country'] && $order_info['payment_iso_code_3'] == $order_info['shipping_iso_code_3'])) {
				$data['error_warning'] = $this->language->get('error_address_match');
			} else {
				$data['error_warning'] = '';
			}
			
			// The title stored in the DB gets truncated which causes order_info.tpl to not be displayed properly
			$billmate_partpayment = $this->config->get('billmate_partpayment');
		    $countryRates = $this->config->get('billmate_partpayment_pclasses');
            $countryRates = $countryRates['SWE'][0];
			
			$data['merchant'] = $billmate_partpayment['SWE']['merchant'];
			$data['phone_number'] = $order_info['telephone'];
			
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
				
				$data['street'] = $address[0];
				$data['street_number'] = $address[1];
				$data['street_extension'] = $address[2];
				
				if ($order_info['payment_iso_code_3'] == 'DEU') {
					$data['street_number'] = trim($address[1] . ' ' . $address[2]);
				}
			} else {
				$data['street'] = '';
				$data['street_number'] = '';
				$data['street_extension'] = '';
			}
						
			$data['company'] = $order_info['payment_company'];
			$data['iso_code_2'] = $countryData['iso_code_2'];
			$data['iso_code_3'] = $countryData['iso_code_3'];
			
			$payment_option = array();
			$total = $this->currency->format($order_info['total'], $country_to_currency[$countryData['iso_code_3']], '', false);
			foreach ($countryRates as $pclass) {                
				// 0 - Campaign
				// 1 - Account
				// 2 - Special
				// 3 - Fixed
				if (!in_array($pclass['type'], array(0, 1, 3))) {
					continue;
				}
				if ($pclass['type'] == 2) {
					$monthly_cost = -1;
				} else {
					if ($total < $pclass['minamount'] || ($total > $pclass['maxamount'] && $pclass['maxamount'] > 0)) {
						continue;
					}
					if ($pclass['type'] == 3) {
						continue;
					} else {
						$sum = $total;
						$lowest_payment = $this->getLowestPaymentAccount($countryData['iso_code_3']);
						$monthly_cost = 0;
						$monthly_fee = $pclass['handlingfee'];
						$start_fee = $pclass['startfee'];
						$sum += $start_fee;
						$base = ($pclass['type'] == 1);
						$minimum_payment = ($pclass['type'] === 1) ? $this->getLowestPaymentAccount($countryData['iso_code_3']) : 0;
						if ($pclass['nbrofmonths'] == 0) {
							$payment = $sum;
						} elseif ($pclass['interestrate'] == 0) {
							$payment = $sum / $pclass['nbrofmonths'];
						} else {
							$interest = $pclass['interestrate'] / (100.0 * 12);
							$payment = $sum * $interest / (1 - pow((1 + $interest), -$pclass['nbrofmonths']));
						}
						$payment += $monthly_fee;
						$balance = $sum;
						$pay_data = array();
						$months = $pclass['nbrofmonths'];
						
						while (($months != 0) && ($balance > 0.01)) {
							$interest = $balance * $pclass['interestrate'] / (100.0 * 12);
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
				
				$payment_option[$pclass['paymentplanid']]['pclass_id'] = $pclass['paymentplanid'];
				$payment_option[$pclass['paymentplanid']]['title'] = $pclass['description'];
				$payment_option[$pclass['paymentplanid']]['months'] = $pclass['nbrofmonths'];
				$payment_option[$pclass['paymentplanid']]['monthly_cost'] = round($monthly_cost,0);
			}
			
			$sort_order = array(); 
			  
			foreach ($payment_option as $key => $value) {
				$sort_order[$key] = $value['pclass_id'];
			}
		
			$data['payment_options'] = array();
			
			foreach ($payment_option as $payment_option) {
				$data['payment_options'][] = array(
					'code'  => $payment_option['pclass_id'],
					'title' => sprintf($this->language->get('text_monthly_payment'), $payment_option['months'],
                        preg_replace('/[.,].0+/','',$this->currency->format($this->currency->convert($payment_option['monthly_cost'], $country_to_currency[$countryData['iso_code_3']], $this->currency->getCode()), 1, 1)))
				);
			}
			//$this->document->addStyle($style);
			//$this->document->addScript(HTTP_SERVER . 'catalog/view/javascript/module-tombola.js');
			//$data['description'] = $billmate_partpayment['SWE']['description'];

            if(version_compare(VERSION,'2.0.0','>=')){

                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oc2/billmate_partpayment.tpl')) {
                    return $this->load->view($this->config->get('config_template') . '/template/payment/oc2/billmate_partpayment.tpl',$data);
                } else {
                    return $this->load->view('default/template/payment/oc2/billmate_partpayment.tpl',$data);
                }
            } else {
                $this->data = $data;
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_partpayment.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/billmate_partpayment.tpl';
                } else {
                    $this->template = 'default/template/payment/billmate_partpayment.tpl';
                }

                $this->render();
            }
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
        $order_id = $this->session->data['order_id'];
		// Order must have identical shipping and billing address or have no shipping address at all
		if ($order_info) {
			if ($countryData['iso_code_3'] == 'DEU' && empty($this->request->post['deu_terms'])) {
				$json['error'] =  $this->language->get('error_deu_terms');
			}
			
			if (!$json) {
				$billmate_partpayment = $this->config->get('billmate_partpayment');
				
				require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
				
				$eid = (int)$billmate_partpayment['SWE']['merchant'];
				$key = (int)$billmate_partpayment['SWE']['secret'];
				$ssl = true;
				$debug = false;

                define('BILLMATE_SERVER','2.1.7');
                define('BILLMATE_CLIENT','Opencart:Billmate:2.0');
                $k = new BillMate($eid,$key,$ssl,$billmate_partpayment['SWE']['server'] == 'beta' ,$debug);


                $values['PaymentData'] = array(
                    'method' => 4,
                    'paymentplanid' => isset($this->request->post['code']) ? $this->request->post['code'] : '',
                    'currency' => $this->currency->getCode(),
                    'language' => $this->language->get('code'),
                    'country' => 'SE',
                    'autoactivate' => 0,
                    'orderid' => $order_id
                );

                $values['PaymentInfo'] = array(
                    'paymentdate' => date('Y-m-d')
                );


                $values['Customer']['nr'] = $this->customer->getId();
                $values['Customer']['pno'] = $this->request->post['pno'];
                $values['Customer']['Shipping'] = array(
                    'email'           => $order_info['email'],
                    'firstname'           => $order_info['shipping_firstname'],
                    'lastname'           => $order_info['shipping_lastname'],
                    'company'         => $order_info['shipping_company'],
                    'street'          => $order_info['shipping_address_1'],
                    'zip'             => $order_info['shipping_postcode'],
                    'city'            => $order_info['shipping_city'],
                    'country'         => $order_info['shipping_iso_code_2'],
                );

                $values['Customer']['Billing'] = array(
                    'email'           => $order_info['email'],
                    'firstname'           => $order_info['payment_firstname'],
                    'lastname'           => $order_info['payment_lastname'],
                    'company'         => $order_info['payment_company'],
                    'street'          => $order_info['payment_address_1'],
                    'zip'             => $order_info['payment_postcode'],
                    'city'            => $order_info['payment_city'],
                    'country'         => $order_info['payment_iso_code_2'],
                );

                $products = $this->cart->getProducts();
                $prepareDiscount = array();
                $subtotal = 0;
                $prepareProductDiscount = array();
                $productTotal = 0;
                $orderTotal = 0;
                $taxTotal = 0;

                foreach ($products as $product) {

                    $product_total_qty = $product['quantity'];

                    if ($product['minimum'] > $product_total_qty) {
                        $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
                    }
                    $rates=0;

                    $price = $product['price'];
                    $price = $this->currency->convert($price,$this->config->get('config_currency'),$this->session->data['currency']);
                    $tax_rates = $this->tax->getRates($price,$product['tax_class_id']);
                    foreach($tax_rates as $rate){
                        $rates+= $rate['rate'];
                    }
                    $title = $product['name'];
                    if(count($product['option']) > 0){
                        foreach($product['option'] as $option){
                            $title .= ' - '.$option['name'].': '.$option['option_value'];
                        }
                    }
                    $productValue = $this->currency->format($price *100, $this->currency->getCode(), '', false);
                    $values['Articles'][] = array(
                        'quantity'   => (int)$product_total_qty,
                        'artnr'    => $product['model'],
                        'title'    => $title,
                        'aprice'    => $price * 100,
                        'taxrate'      => (float)($rates),
                        'discount' => 0.0,
                        'withouttax'    => $product_total_qty * ($price *100),

                    );
                    $orderTotal += $product_total_qty * ($price *100);
                    $taxTotal += ($product_total_qty * ($price *100)) * ($rates/100);

                    $subtotal += ($price * 100) * $product_total_qty;
                    $productTotal += ($price * 100) * $product_total_qty;
                    if(isset($prepareDiscount[$rates])){
                        $prepareDiscount[$rates] += ($price * 100) * $product_total_qty;
                    } else {
                        $prepareDiscount[$rates] = ($price * 100) * $product_total_qty;
                    }
                    if(isset($prepareProductDiscount[$rates])){
                        $prepareProductDiscount[$rates] += ($price * 100) * $product_total_qty;
                    } else {
                        $prepareProductDiscount[$rates] = ($price * 100) * $product_total_qty;
                    }
                }


                $totals = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = ".$order_id);
                $billmate_tax = array();
                $total_data = array();
                $total = 0;
                $totals = $totals->rows;

                foreach ($totals as $result) {
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

                foreach ($totals as $key => $value) {
                    $sort_order[$key] = $value['sort_order'];

                    if (isset($billmate_tax[$value['code']])) {
                        if ($billmate_tax[$value['code']]) {
                            $totals[$key]['tax_rate'] = abs($billmate_tax[$value['code']] / $value['value'] * 100);
                        } else {
                            $totals[$key]['tax_rate'] = 0;
                        }
                    } else {
                        $totals[$key]['tax_rate'] = '0';
                    }
                }

                foreach ($totals as $total) {
                    if ($total['code'] != 'sub_total' && $total['code'] != 'tax' && $total['code'] != 'total' && $total['code'] != 'coupon') {

                        $total['value'] = round( $total['value'], 2 );
                        $totalTypeTotal = $this->currency->format($total['value']*100, $this->currency->getCode(), '', false);
                        $totalTypeTotal = $this->currency->convert($totalTypeTotal,$this->config->get('config_currency'),$this->session->data['currency']);
                        if($total['code'] != 'billmate_fee' && $total['code'] != 'shipping'){
                            $values['Articles'][] = array(
                                'quantity' => 1,
                                'artnr' => '',
                                'title' => $total['title'],
                                'aprice' => $totalTypeTotal,
                                'taxrate' => (float)$total['tax_rate'],
                                'discount' => 0.0,
                                'withouttax' => $totalTypeTotal,
                            );
                            $orderTotal += $totalTypeTotal;
                            $taxTotal += $totalTypeTotal * ($total['tax_rate'] / 100);
                        }
                        if($total['code'] == 'shipping'){
                            $values['Cart']['Shipping'] = array(
                                'withouttax' => $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100,
                                'taxrate' => $total['tax_rate']
                            );
                            $orderTotal += $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100;
                            $taxTotal += ($this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100) * ($total['tax_rate']/100);
                        }
                        if($total['code'] == 'billmate_fee'){
                            $values['Cart']['Handling'] = array(
                                'withouttax' => $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100,
                                'taxrate' => $total['tax_rate']
                            );
                            $orderTotal +=$this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100;
                            $taxTotal += ($this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100) * ($total['tax_rate']/100);
                        }


                        if($total['code'] != 'myoc_price_rounding' )
                        {
                            if (isset($prepareDiscount[$total['tax_rate']]))
                                $prepareDiscount[$total['tax_rate']] += $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100;
                            else
                                $prepareDiscount[$total['tax_rate']] = $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100;
                            $subtotal += $this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']) * 100;
                        }
                    }
                }

                if(isset($this->session->data['advanced_coupon'])){
                    $coupon = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'advanced_coupon' AND order_id = ".$this->session->data['order_id']);
                    $total = $coupon->row;

                    $this->load->model('checkout/advanced_coupon');
                    $codes = array_unique($this->session->data['advanced_coupon']);
                    foreach ($codes as $code) {
                        # code...
                        $coupons_info[] = $this->model_checkout_advanced_coupon->getAdvancedCoupon($code);
                    }

                    if(isset($coupons_info)){
                        foreach($coupons_info as $coupon_info){
                            if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F' || $coupon_info['type'] == 'FP') && $coupon_info['shipping'] == 1)
                            {
                                $shipping = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'shipping' AND order_id = ".$this->session->data['order_id']);
                                $shipping = $shipping->row;
                                $shiptax = array();
                                $shiptotal = 0;
                                $shiptotal_data = array();
                                $shippingtax = 0;
                                if ($this->config->get($shipping['code'].'_status'))
                                {
                                    $this->load->model('total/'.$shipping['code']);

                                    $this->{'model_total_'.$shipping['code']}->getTotal($shiptotal_data, $shiptotal, $shiptax);

                                    foreach ($shiptax as $key => $value)
                                    {
                                        $shippingtax += $value;
                                    }
                                    $shippingtax = $shippingtax / $shipping['value'];

                                }
                                if($total['value'] < $shipping['value'])
                                {

                                    foreach ($prepareProductDiscount as $tax => $value)
                                    {

                                        $discountValue = $total['value'] + $shipping['value'];
                                        $percent       = $value / $productTotal;

                                        $discountIncl = $percent * ($discountValue * 100);

                                        $discountExcl = $discountIncl / (1 + $tax / 100);
                                        $discountToArticle = $this->currency->format($discountIncl, $this->currency->getCode(), '', false);
                                        $discountToArticle = $this->currency->convert($discountToArticle,$this->config->get('config_currency'),$this->session->data['currency']);

                                        $values['Articles'][] = array(
                                            'quantity'   => 1,
                                            'artnr'    => '',
                                            'title'    => $total['title'].' '.$tax.'% tax',
                                            'aprice'    => $discountToArticle,
                                            'taxrate'      => $tax,
                                            'discount' => 0.0,
                                            'withouttax'    => $discountToArticle

                                        );


                                    }
                                }
                                $freeshipTotal = $this->currency->format(-$shipping['value'] * 100, $this->currency->getCode(), '', false);
                                $freeshipTotal = $this->currency->convert($freeshipTotal,$this->config->get('config_currency'),$this->session->data['currency']);

                                $values['Articles'][] = array(
                                    'quantity'   => 1,
                                    'artnr'    => '',
                                    'title'    => $total['title'].' Free Shipping',
                                    'aprice'    => $freeshipTotal,
                                    'taxrate'      => $shippingtax * 100,
                                    'discount' => 0.0,
                                    'withouttax'    => $freeshipTotal
                                );
                                $orderTotal += $freeshipTotal;
                                $taxTotal += $freeshipTotal * $shippingtax;

                            } else if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F' || $coupon_info['type'] == 'FP') && $coupon_info['shipping'] == 0){


                                foreach ($prepareProductDiscount as $tax => $value)
                                {

                                    $percent      = $value / $productTotal;
                                    $discount     = $percent * ($total['value'] * 100);
                                    $discountToArticle = $this->currency->format($discount, $this->currency->getCode(), '', false);
                                    $discountToArticle = $this->currency->convert($discountToArticle,$this->config->get('config_currency'),$this->session->data['currency']);

                                    $values['Articles'][] = array(
                                        'quantity'   => 1,
                                        'artnr'    => '',
                                        'title'    => $total['title'].' '.$tax.'% tax',
                                        'aprice'    => (int)$discountToArticle,
                                        'taxrate'      => $tax,
                                        'discount' => 0.0,
                                        'withouttax'    => $discountToArticle
                                    );
                                    $orderTotal += $discountToArticle;
                                    $taxTotal += $discountToArticle * ($tax/100);
                                }
                            }
                        }
                    }

                }

                if(isset($this->session->data['coupon'])){
                    $coupon = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'coupon' AND order_id = ".$this->session->data['order_id']);
                    $total = $coupon->row;
                    $this->load->model('checkout/coupon');
                    $coupon_info = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
                    if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F') && $coupon_info['shipping'] == 1)
                    {
                        $shipping = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'shipping' AND order_id = ".$this->session->data['order_id']);
                        $shipping = $shipping->row;
                        $shiptax = array();
                        $shiptotal = 0;
                        $shiptotal_data = array();
                        $shippingtax = 0;
                        if ($this->config->get($shipping['code'].'_status'))
                        {
                            $this->load->model('total/'.$shipping['code']);

                            $this->{'model_total_'.$shipping['code']}->getTotal($shiptotal_data, $shiptotal, $shiptax);

                            foreach ($shiptax as $key => $value)
                            {
                                $shippingtax += $value;
                            }
                            $shippingtax = $shippingtax / $shipping['value'];

                        }
                        if($total['value'] < $shipping['value'])
                        {

                            foreach ($prepareProductDiscount as $tax => $value)
                            {

                                $discountValue = $total['value'] + $shipping['value'];
                                $percent       = $value / $productTotal;

                                $discountIncl = $percent * ($discountValue * 100);

                                $discountExcl = $discountIncl / (1 + $tax / 100);
                                $discountToArticle = $this->currency->format($discountIncl, $this->currency->getCode(), '', false);
                                $discountToArticle = $this->currency->convert($discountToArticle,$this->config->get('config_currency'),$this->session->data['currency']);

                                $values['Articles'][] = array(
                                    'quantity'   => 1,
                                    'artnr'    => '',
                                    'title'    => $total['title'].' '.$tax.'% tax',
                                    'aprice'    => $discountToArticle,
                                    'taxrate'      => $tax,
                                    'discount' => 0.0,
                                    'withouttax'    => $discountToArticle

                                );
                                $orderTotal += $discountToArticle;
                                $taxTotal += $discountToArticle * ($tax/100);

                            }
                        }
                        $freeshipTotal =  $this->currency->format(-$shipping['value'] * 100, $this->currency->getCode(), '', false);
                        $freeshipTotal = $this->currency->convert($freeshipTotal,$this->config->get('config_currency'),$this->session->data['currency']);

                        $values['Articles'][] = array(
                            'quantity'   => 1,
                            'artnr'    => '',
                            'title'    => $total['title'].' Free Shipping',
                            'aprice'    => $freeshipTotal,
                            'taxrate'      => $shippingtax * 100,
                            'discount' => 0.0,
                            'withouttax'    => $freeshipTotal

                        );
                        $orderTotal += $freeshipTotal;
                        $taxTotal += $freeshipTotal * $shippingtax;

                    } else if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F') && $coupon_info['shipping'] == 0){


                        foreach ($prepareProductDiscount as $tax => $value)
                        {

                            $percent      = $value / $productTotal;
                            $discount     = $percent * ($total['value'] * 100);
                            $discountToArticle = $this->currency->format($discount, $this->currency->getCode(), '', false);

                            $discountToArticle = $this->currency->convert($discountToArticle,$this->config->get('config_currency'),$this->session->data['currency']);
                            $values['Articles'][] = array(
                                'quantity'   => 1,
                                'artnr'    => '',
                                'title'    => $total['title'].' '.$tax.'% tax',
                                'aprice'    => (int)$discountToArticle,
                                'taxrate'      => $tax,
                                'discount' => 0.0,
                                'withouttax'    => $discountToArticle

                            );
                            $orderTotal += $discountToArticle;
                            $taxTotal += $discountToArticle * ($tax/100);

                        }
                    }

                } // End discount isset
                $total = $this->currency->convert($order_info['total'],$this->config->get('config_currency'),$this->session->data['currency']);
                $round = ($total*100) - ($orderTotal + $taxTotal);
                $values['Cart']['Total'] = array(
                    'withouttax' => $orderTotal,
                    'tax' => $taxTotal,
                    'rounding' => $round,
                    'withtax' => $orderTotal + $taxTotal + $round
                ); // End discount isset

				$pno = trim($this->request->post['pno']);
				

                if( !function_exists('match_usernamevp')){
                    function match_usernamevp( $str1, $str2 ){
                        $name1 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str1 ) ) );
                        $name2 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str2 ) ) );
                        $foundName = array_intersect($name1, $name2);
                        return count($foundName ) > 0;                        
                    }
                }
				try {
                    $addr = $k->GetAddress(array('pno' => $pno));

                    if( !is_array( $addr ) ){
                        $json['error'] = utf8_encode( $addr );//.'<br/><br/>'.$this->language->get('close_other_payment').'<br/><input type="button" onclick="modalWin.HideModalPopUp();jQuery(\'#payment-method a\').first().trigger(\'click\');" class="button" value="'.$this->language->get('Close').'" />'
                        $this->response->setOutput(my_json_encode($json ));
                        return;
                    } else if(isset($addr['code'])){
                        $json['error'] = utf8_encode($addr['message']);
                        $this->response->setOutput(my_json_encode($json ));
                        return;
                    }
                    foreach( $addr as $key => $col ){
                        $addr[$key] = mb_convert_encoding($col,'UTF-8','auto');
                    }

                    if(isset($addr['error']))
                        $json['address'] = $this->language->get('wrong_person_number').'<br/><br/>'.$this->language->get('close_other_payment').'<br/><input type="button" onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() ==0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="billmate_button" value="'.$this->language->get('Close').'" />';
                } catch(Exception $e) {
                    //Something went wrong
                    // echo "{$e->getMessage()} (#{$e->getCode()})\n";
                }

//				if(empty($json['error'])) //$order_info['shipping_firstname'] == $addr[0][0] and


                $db = $this->registry->get('db');
                if( $db == NULL ) $db = $this->db;

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $addr['country']. "' AND status = '1'");
                $countryinfo = $query->row;

                $countryname = $countryinfo['name'];

                $fullname = $order_info['payment_firstname']. ' '.$order_info['payment_lastname'];
                if( empty( $addr['firstname'])){
                    $apiName = $fullname;
                } else {
                    $apiName  = $addr['firstname'].' '.$addr['lastname'];
                }
                if( !function_exists('match_usernamevp')){
                    function match_usernamevp( $str1, $str2 ){
                        $name1 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str1 ) ) );
                        $name2 = explode(' ', utf8_strtolower( Encoding::fixUTF8( $str2 ) ) );
                        $foundName = array_intersect($name1, $name2);
                        return count($foundName ) > 0;
                    }
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

                if( empty( $addr['firstname'] ) ){
                    $apifirst = $firstArr;
                    $apilast  = $lastArr ;
                }else {
                    $apifirst = explode(' ', $addr['firstname'] );
                    $apilast  = explode(' ', $addr['lastname'] );
                }
                $matchedFirst = array_intersect($apifirst, $firstArr );
                $matchedLast  = array_intersect($apilast, $lastArr );
                $apiMatchedName   = !empty($matchedFirst) && !empty($matchedLast);


                $this->session->data['mismatch'] = false;
                if( !(
                        $this->matchString( $order_info['payment_city'], $addr['city']) &&
                        $this->matchString( $order_info['payment_postcode'], $addr['zip']) &&
                        $this->matchString( $order_info['payment_address_1'],$addr['street']) &&
                        $apiMatchedName
                    ) || !$address_same ){

                    $this->session->data['mismatch'] = true;
                    if(!(isset($this->request->get['geturl']) and $this->request->get['geturl']=="yes")){

                        if(isset($addr['company'])) {

                            $json['address'] = $addr['company'] . '<br>' . $addr['street'] . '<br>' . $addr['zip'] . '<br>' . $addr['city'] . '<br/>' . $countryname . '<div style="padding: 17px 0px;"></div><div><input type="button" value="' . $this->language->get('bill_yes') . '" onclick="modalWin.HideModalPopUp();ajax_load(\'&geturl=yes\');" class="billmate_button"/></div><div><a onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() ==0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="linktag" >' . $this->language->get('bill_no') . '</a></div>';
                        } else {
                            $json['address'] = $addr['firstname'] . ' ' . $addr['lastname'] . '<br>' . $addr['street'] . '<br>' . $addr['zip'] . '<br>' . $addr['city'] . '<br/>' . $countryname . '<div style="padding: 17px 0px;"></div><div><input type="button" value="' . $this->language->get('bill_yes') . '" onclick="modalWin.HideModalPopUp();ajax_load(\'&geturl=yes\');" class="billmate_button"/></div><div><a onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() ==0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="linktag" >' . $this->language->get('bill_no') . '</a></div>';

                        }$json['error'] = "";
                    }

                }
                if(!isset($json['error'])){
                    $ship_api_address = array();
                    try {
                        $data = array(
                            'fname'      => Encoding::fixUTF8($addr['firstname']),
                            'lname'       => Encoding::fixUTF8($addr['lastname']),
                            'address_1'      => Encoding::fixUTF8($addr['street']),
                            'company'      => '',
                            'address_2'      => '',
                            'postcode'       => Encoding::fixUTF8($addr['zip']),
                            'city'           => Encoding::fixUTF8($addr['city']),
                            'country_id'     => (int)$countryinfo['country_id'],
                            'zone_id'        => 0
                        );
                        if( empty($addr['firstname'])){
                            $ship_api_address = array(
                                'company'         => Encoding::fixUTF8($addr['company']),
                                'street'          => Encoding::fixUTF8($addr['street']),
                                'zip'             => Encoding::fixUTF8($addr['zip']),
                                'city'            => Encoding::fixUTF8($addr['city']),
                            );
                            $data['company']   = $addr['company'];
                            $data['fname'] = $order_info['payment_firstname'];
                            $data['lname']  = $order_info['payment_lastname'];
                        } else{
                            $ship_api_address = array(
                                'firstname'           => Encoding::fixUTF8($addr['firstname']),
                                'lastname'           => Encoding::fixUTF8($addr['lastname']),
                                'company'		  => '',
                                'street'          => Encoding::fixUTF8($addr['street']),
                                'zip'             => Encoding::fixUTF8($addr['zip']),
                                'city'            => Encoding::fixUTF8($addr['city']),
                            );
                        }

                        $zonename = '';


                        $this->load->model('account/address');
                        $data['firstname'] = $data['fname'];
                        $data['lastname'] = $data['lname'];

                        if( $this->session->data['mismatch'] ){

                            $this->session->data['shipping_address_id'] = 0;
                            if( $this->customer->getId() > 0 ){
                                $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($data);
                            }
                            $this->session->data['shipping_postcode']   = $data['postcode'];

                            $this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
                            $this->session->data['payment_postcode']   = $data['postcode'];
                        }


                        $sql = "UPDATE `" . DB_PREFIX . "order` SET shipping_company='".$data['company']."', payment_company='".$data['company']."', shipping_firstname = '" . $db->escape($data['fname']) . "',firstname = '" . $db->escape($data['fname']) . "', lastname = '" . $db->escape($data['lname']) . "', payment_firstname = '" . $db->escape($data['fname']) . "', payment_lastname = '" . $db->escape($data['lname']) . "', payment_address_1 = '" . $db->escape($data['address_1']) . "', payment_address_2 = '', payment_city = '" . $db->escape($data['city']) . "', payment_postcode = '" . $db->escape($data['postcode']) . "', payment_zone = '" . $db->escape($zonename) . "', payment_zone_id = '" . (int)$data['zone_id'] . "', shipping_lastname = '" . $db->escape($data['lname']) . "', shipping_address_1 = '" . $db->escape($data['address_1']) . "', shipping_address_2 = '', shipping_city = '" . $db->escape($data['city']) . "', shipping_postcode = '" . $db->escape($data['postcode']) . "', shipping_zone = '', shipping_zone_id = '0', date_modified = NOW(), shipping_country = '" . $this->db->escape($countryinfo['name']) . "', shipping_country_id = '" . (int)$countryinfo['country_id'] . "' where order_id = ". $this->session->data['order_id'];
                        $db->query($sql);

                        $order_info_updated = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                        $values['Customer']['Shipping'] = array(
                            'email'           => $order_info_updated['email'],
                            'firstname'           => $order_info_updated['shipping_firstname'],
                            'lastname'           => $order_info_updated['shipping_lastname'],
                            'company'         => $order_info_updated['shipping_company'],
                            'street'          => $order_info_updated['shipping_address_1'],
                            'zip'             => $order_info_updated['shipping_postcode'],
                            'city'            => $order_info_updated['shipping_city'],
                            'country'         => $order_info_updated['shipping_iso_code_2'],
                        );
                        $values['Customer']['Billing'] = array(
                            'email'           => $order_info_updated['email'],
                            'firstname'           => $order_info_updated['shipping_firstname'],
                            'lastname'           => $order_info_updated['shipping_lastname'],
                            'company'         => $order_info_updated['shipping_company'],
                            'street'          => $order_info_updated['shipping_address_1'],
                            'zip'             => $order_info_updated['shipping_postcode'],
                            'city'            => $order_info_updated['shipping_city'],
                            'country'         => $order_info_updated['shipping_iso_code_2'],
                        );

                        $func = create_function('','');
                        $oldhandler = set_error_handler($func);

                        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_code` = 'billmate_partpayment', `payment_method` = '" . $this->db->escape($this->language->get('text_title')) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);

                        $result1 = $k->AddPayment($values);


                        if(isset($result1['code']))
                        {
                            $json['address'] = '<p>'.utf8_encode($result1['message']).'</p><input type="button" style="float:right" value="'.$this->language->get('close').'" onclick="modalWin.HideModalPopUp();if(jQuery(\'#supercheckout-fieldset\').size() ==0){jQuery(\'#payment-method a\').first().trigger(\'click\');}" class="button" />';
                            $json['title'] = $this->language->get('payment_error');
                            $json['height'] = 150;
                        }
                        else
                        {
                            $billmate_order_status = $result1['status'];
                            if ($billmate_order_status == 'Created') {
                                $order_status = $billmate_partpayment['SWE']['accepted_status_id'];
                            } elseif ($billmate_order_status == 'Pending') {
                                $order_status = $billmate_partpayment['SWE']['pending_status_id'];
                            } else {
                                $order_status = $this->config->get('config_order_status_id');
                            }

                            $comment = sprintf($this->language->get('text_comment'), $result1['number']);

                            if(version_compare(VERSION,'2.0.0','>='))
                                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status,$comment,false);
                            else
                                $this->model_checkout_order->confirm($this->session->data['order_id'], $order_status, $comment, 1);

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
        if(isset($json['error'] ) ) $json['error'] = utf8_encode(($json['error']));
//        if(isset($json['address'] ) ) $json['address'] = utf8_encode(Encoding::fixUTF8($json['address']));

        $data = @my_json_encode($json);
        $this->response->setOutput($data);
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
