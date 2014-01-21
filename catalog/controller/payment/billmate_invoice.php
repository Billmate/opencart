<?php

require_once dirname(DIR_APPLICATION).'/billmate/JSON.php';
class ControllerPaymentBillmateInvoice extends Controller {
	public function getDebugReport(){
		$billmate_invoice = $this->config->get('billmate_invoice');
		
		require_once dirname(DIR_APPLICATION).'/billmate/billingapi/BillMate API/BillMate.php';
		include(dirname(DIR_APPLICATION)."/billmate/billingapi/BillMate API/xmlrpc-2.2.2/lib/xmlrpc.inc");
		include(dirname(DIR_APPLICATION)."/billmate/billingapi/BillMate API/xmlrpc-2.2.2/lib/xmlrpcs.inc");
			
		$eid = 7270;//7320;
		$key = 606250886062;//511461125114;
		$ssl = true;
		$debug = true;


		$k = new BillMate($eid,$key,$ssl,$debug);


		$additionalinfo = array(
			"currency"=>0,//SEK
			"country"=>209,//Sweden
			"language"=>125,//Swedish
		);

		try {

			$result = $k->FetchCampaigns($additionalinfo);
			
			//Result:
		//    print_r($addr);
			
		} catch(Exception $e) {
			//Something went wrong
		//    echo "{$e->getMessage()} (#{$e->getCode()})\n";
		}
	}
    public function terms(){
        $this->language->load('payment/billmate_invoice');
        
        $this->data['page_title'] = $this->language->get('page_title');
        $this->data['body_title'] = $this->language->get('body_title');
        $this->data['subtitle'] = $this->language->get('subtitle');
        $this->data['short_description'] = $this->language->get('short_description');
        $this->data['subline'] = $this->language->get('subline');
        $this->data['li1'] = $this->language->get('li1');
        $this->data['li2'] = $this->language->get('li2');
        $this->data['li3'] = $this->language->get('li3');
        $this->data['li4'] = $this->language->get('li4');
        $this->data['li5'] = $this->language->get('li5');
        $this->data['li6'] = $this->language->get('li6');
        $this->data['long_description'] = $this->language->get('long_description');
        $this->data['footer_one'] = $this->language->get('footer_one');
        $this->data['footer_two'] = $this->language->get('footer_two');
	
	    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/villkor.tpl')) {
		    $this->template = $this->config->get('config_template') . '/template/payment/villkor.tpl';
	    } else {
		    $this->template = 'default/template/payment/villkor.tpl';
	    }
        $json['output'] = $this->render();
        $this->response->setOutput($json['output']);
    }
	public function getInfo(){
		echo 'Billmate Plugin Version: 1.28'; 
		phpinfo();
	}
    protected function index() {
	    $this->load->model('checkout/order');

	    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

	    if ($order_info) {      

			$store_currency = $this->config->get('config_currency');
			$store_country  = $this->config->get('config_country_id');
			$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
			$countryData    = $countryQuery->row;

			$this->language->load('payment/billmate_invoice');

            $this->data['text_additional'] = $this->language->get('text_additional');
            $this->data['text_payment_option'] = $this->language->get('text_payment_option');	
		    $this->data['text_wait'] = $this->language->get('text_wait');		
			
		    $this->data['entry_pno'] = $this->language->get('entry_pno');
		    $this->data['entry_phone_no'] = sprintf($this->language->get('entry_phone_no'),$order_info['email'] );
		    $this->data['button_confirm'] = $this->language->get('button_confirm');
			$this->data['wrong_person_number'] = $this->language->get('your_billing_wrong');
			
		    // Store Taxes to send to Billmate
		    $total_data = array();
		    $total = 0;
		     
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

//                    @$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
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
				
	
		    // The title stored in the DB gets truncated which causes order_info.tpl to not be displayed properly
		    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $this->db->escape($this->language->get('text_title')) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);
        	$this->document->addScript('catalog/view/javascript/jquery/social/init_socialbutton.js');

		    $billmate_invoice = $this->config->get('billmate_invoice');
	
		    $this->data['merchant'] = (int)$billmate_invoice['SWE']['merchant'];
		    $this->data['phone_number'] = $order_info['telephone'];
				
		    if ($countryData['iso_code_3'] == 'DEU' || $countryData['iso_code_3'] == 'NLD') {
			    $address = $this->splitAddress($order_info['payment_address_1']);
		
			    $this->data['street'] = $address[0];
			    $this->data['street_number'] = $address[1];
			    $this->data['street_extension'] = $address[2];
		
			    if ($countryData['iso_code_3'] == 'DEU') {
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
	
		    // Get the invoice fee
		    $query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = " . (int) $order_info['order_id'] . " AND `code` = 'billmate_fee'");
	        
		    if ($query->num_rows && !$query->row['value']) {
			    $this->data['billmate_fee'] = $query->row['value'];
		    } else {
			    $this->data['billmate_fee'] = '';
		    }
			$this->data['description'] = $billmate_invoice['SWE']['description'];
			 
		    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_invoice.tpl')) {
			    $this->template = $this->config->get('config_template') . '/template/payment/billmate_invoice.tpl';
		    } else {
			    $this->template = 'default/template/payment/billmate_invoice.tpl';
		    }

		    $this->render();
	    }
    }

    public function send() {
        $this->language->load('payment/billmate_invoice');
		
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;

		$json = array();

		if(isset($_POST['pno'])) $_POST['pno'] = trim($_POST['pno']);
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
				$billmate_invoice = $this->config->get('billmate_invoice');
				
				require_once dirname(DIR_APPLICATION).'/billmate/BillMate.php';
				include(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpc.inc');
				include(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpcs.inc');
    				
				$eid = (int)$billmate_invoice['SWE']['merchant'];
				$key = (float)$billmate_invoice['SWE']['secret'];
				$ssl = true;
				$debug = false;
				$k = new BillMate($eid,$key,$ssl,$debug, $billmate_invoice['SWE']['server'] == 'beta' );

				$country_to_currency = array(
					'NOR' => 'NOK',
					'SWE' => 'SEK',
					'FIN' => 'EUR',
					'DNK' => 'DKK',
					'DEU' => 'EUR',
					'NLD' => 'EUR',
				);

				$shippiISO = 'SWE'; // !empty( $order_info['shipping_iso_code_3'] ) ? $order_info['shipping_iso_code_3'] : $order_info['payment_iso_code_3'];
				
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
					    'telno'           => '',
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
					    'telno'           => '',
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
				$product_query = $this->db->query("SELECT `name`, `model`, `price`, `quantity`, `tax` / `price` * 100 AS 'tax_rate' FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = " . (int) $order_info['order_id'] . " UNION ALL SELECT '', `code`, `amount`, '1', 0.00 FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = " . (int) $order_info['order_id'])->rows;	
				foreach ($product_query as $product) {

					$goods_list[] = array(
						'qty'   => (int)$product['quantity'],
						'goods' => array(
							'artno'    => $product['model'],
							'title'    => $product['name'],
							'price'    => (int)$this->currency->format($product['price']*100, $country_to_currency[$countryData['iso_code_3']], '', false),
							'vat'      => (float)($product['tax_rate']/$product['quantity']),
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
								'price'    => (int)$this->currency->format($total['value']*100, $country_to_currency[$countryData['iso_code_3']], '', false),
								'vat'      => (float)$total['tax_rate'],
								'discount' => 0.0,
								'flags'    => $flag,
							)
						);
					}
				}
              
				$pno = trim($this->request->post['pno']);
				$pclass = -1;
				
				$transaction = array(
					"order1"=>(string)$this->session->data['order_id'],
					"comment"=>'',
					"gender"=>0,
					"flags"=>0,
					"reference"=>"",
					"reference_code"=>"",
					"currency"=>$currency,
					"country"=>$country,
					"language"=>$language,
					"pclass"=>$pclass,
					"shipInfo"=>array("delay_adjust"=>"1"),
					"travelInfo"=>array(),
					"incomeInfo"=>array(),
					"bankInfo"=>array(),
					"sid"=>array("time"=>microtime(true)),
					"extraInfo"=>array(array("cust_no"=>(string)$order_info['customer_id']))
				);

				try {
					$addr = $k->GetAddress($pno);
					
					if( !is_array( $addr ) ){
				        $json['error'] = utf8_encode( $addr );//.'<br/><br/>'.$this->language->get('close_other_payment').'<br/><input type="button" onclick="modalWin.HideModalPopUp();jQuery(\'#payment-method a\').first().trigger(\'click\');" class="button" value="'.$this->language->get('Close').'" />'
				        $this->response->setOutput(my_json_encode($json ));
				        return;
					}
					foreach( $addr[0] as $key => $col ){
						$addr[0][$key] = utf8_encode($col);
					}

					if(isset($addr['error']))
						$json['address'] = $this->language->get('wrong_person_number').'<br/><br/>'.$this->language->get('close_other_payment').'<br/><input type="button" onclick="modalWin.HideModalPopUp();jQuery(\'#payment-method a\').first().trigger(\'click\');" class="billmate_button" value="'.$this->language->get('Close').'" />';
				} catch(Exception $e) {
					//Something went wrong
				   // echo "{$e->getMessage()} (#{$e->getCode()})\n";
				}

//				if(empty($json['error'])) //$order_info['shipping_firstname'] == $addr[0][0] and 
				

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
			            $this->matchString( $order_info['payment_city'], $addr[0][4]) && 
			            $this->matchString( $order_info['payment_postcode'], $addr[0][3]) &&
			            $this->matchString( $order_info['payment_address_1'],$addr[0][2]) && 
			            $apiMatchedName
				) || !$address_same ){
				    
				    $this->session->data['mismatch'] = true;
                    if(!(isset($this->request->get['geturl']) and $this->request->get['geturl']=="yes")){


                    $json['address'] = $addr[0][0].' '.$addr[0][1].'<br>'.$addr[0][2].'<br>'.$addr[0][3].'<br>'.$addr[0][4].'<br/>'.$countryname.'<div style="padding: 17px 0px;"></div><div><input type="button" value="'.$this->language->get('bill_yes').'" onclick="modalWin.HideModalPopUp();ajax_load(\'&geturl=yes\');" class="billmate_button"/></div><div><a onclick="modalWin.HideModalPopUp();jQuery(\'#payment-method a\').first().trigger(\'click\');" class="linktag" >'.$this->language->get('bill_no').'</a></div>';
                    $json['error'] = "";
                    }

				}
			if(!isset($json['error'])){
				$ship_api_address = array();
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
					$data['firstname'] = $data['fname'];
					$data['lastname'] = $data['lname'];
					
                    if( $this->session->data['mismatch'] ){
                        $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($data);
                        $this->session->data['shipping_postcode']   = $data['postcode'];

                        $this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
                        $this->session->data['payment_postcode']   = $data['postcode'];
                    }


$sql = "UPDATE `" . DB_PREFIX . "order` SET shipping_company='".$data['company']."', payment_company='".$data['company']."', shipping_firstname = '" . $db->escape($data['fname']) . "',firstname = '" . $db->escape($data['fname']) . "', lastname = '" . $db->escape($data['lname']) . "', payment_firstname = '" . $db->escape($data['fname']) . "', payment_lastname = '" . $db->escape($data['lname']) . "', payment_address_1 = '" . $db->escape($data['address_1']) . "', payment_address_2 = '', payment_city = '" . $db->escape($data['city']) . "', payment_postcode = '" . $db->escape($data['postcode']) . "', payment_zone = '" . $db->escape($zonename) . "', payment_zone_id = '" . (int)$data['zone_id'] . "', shipping_lastname = '" . $db->escape($data['lname']) . "', shipping_address_1 = '" . $db->escape($data['address_1']) . "', shipping_address_2 = '', shipping_city = '" . $db->escape($data['city']) . "', shipping_postcode = '" . $db->escape($data['postcode']) . "', shipping_zone = '', shipping_zone_id = '0', date_modified = NOW(), shipping_country = '" . $this->db->escape($countryinfo['name']) . "', shipping_country_id = '" . (int)$countryinfo['country_id'] . "' where order_id = ". $this->session->data['order_id'];
$db->query($sql);

//header('Content-Type:text/html');
                    foreach( $ship_address as $key => $col ){
                        if( !is_array( $col )) {
                            $ship_address[$key] = utf8_decode( Encoding::fixUTF8($col));
                        }
                    }
                    foreach( $bill_address as $key => $col ){
                        if( !is_array( $col )) {
                            $bill_address[$key] = utf8_decode( Encoding::fixUTF8($col));
                        }
                    }
					$func = create_function('','');
					$oldhandler = set_error_handler($func);
					
					$result1 = $k->AddInvoice($pno,$bill_address,$ship_address,$goods_list,$transaction);
 
					if(!is_array($result1))
					{ 
						$json['address'] = '<p>'.utf8_encode($result1).'</p><input type="button" style="float:right" value="'.$this->language->get('close').'" onclick="modalWin.HideModalPopUp();jQuery(\'#payment-method a\').first().trigger(\'click\')" class="button" />';
						$json['title'] = 'Betalning med Billmate misslyckades.';
						$json['height'] = 150;
					}
					else
					{
						$billmate_order_status = $result1['2'];
						if ($billmate_order_status == '1') {
                            $order_status = $billmate_invoice['SWE']['accepted_status_id'];
						} elseif ($billmate_order_status == '2') {
                            $order_status = $billmate_invoice['SWE']['pending_status_id'];
						} else {
							$order_status = $this->config->get('config_order_status_id');
						}
						
						$comment = sprintf($this->language->get('text_comment'), $result1[0]);
						
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
                // is numeric ?
                if ($data === array_values($data)) {
                    $xml = '<array><data>';
                    
                    foreach ($data as $value) {
                        $xml .= '<value>' . $this->constructXmlrpc($value) . '</value>';
                    }
                    
                    $xml .= '</data></array>';
                    
                } else {
                    // array is associative
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
    /*private function matchString( $string1, $string2 ){
        $string1 = utf8_strtolower( preg_replace('/([\s+])/', '', Encoding::fixUTF8($string1) ));
        $string2 = utf8_strtolower( preg_replace('/([\s+])/', '', Encoding::fixUTF8($string2 )));
        return $string1 == $string2;
    }*/
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
