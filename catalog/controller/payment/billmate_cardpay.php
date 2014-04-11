<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';

class ControllerPaymentBillmateCardpay extends Controller {
	protected function index() {
		if( !empty($this->session->data['order_created']) ) $this->session->data['order_created'] = '';
				
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->load->model('checkout/order');
                
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$order_id = $this->session->data['order_id'];
		$amount = round( $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) * 100,0 );

        $merchant_id = $this->config->get('billmate_cardpay_merchant_id');
        $currency = $this->currency->getCode();
        $accept_url = $this->url->link('payment/billmate_cardpay/accept');
        $cancel_url = $this->url->link('checkout/checkout');
       // $callback_url = $this->url->link('payment/billmate_cardpay/callback');
        $secret = substr($this->config->get('billmate_cardpay_secret'),0,12);

		$prod_url = 'https://cardpay.billmate.se/pay';
		$tst_url = 'https://cardpay.billmate.se/pay/test'; 

		if( $this->config->get('billmate_cardpay_test') ) {
			$url = $tst_url;
		} else {
			$url = $prod_url;
		}
		$this->data['capture_now'] = $this->config->get('billmate_cardpay_transaction_method') == 'sale' ? 'YES':'NO';
		
		$pay_method = 'CARD';
		$callback_url = 'http://api.billmate.se/callback.php';
		$request_method = 'GET';
		$do3dsecure     = $this->config->get('billmate_enable_3dsecure') == 'NO' ? 'NO' : 'YES';
		$promptname     = $this->config->get('billmate_prompt_name') == 'YES' ? 'YES' : 'NO';

		$languageCode = strtoupper( $this->language->get('code') );
		
		$languageCode = $languageCode == 'DA' ? 'DK' : $languageCode;
		$languageCode = $languageCode == 'SV' ? 'SE' : $languageCode;
		$languageCode = $languageCode == 'EN' ? 'GB' : $languageCode;
		
		
        $mac_str = $accept_url . $amount . $callback_url .  $cancel_url . $this->data['capture_now'] . $currency . $do3dsecure . $languageCode . $merchant_id . $order_id . $pay_method . $promptname . $request_method. $secret;

        $mac = hash ( "sha256", $mac_str );

		$this->data['url'] = $url;
		$this->data['order_id'] = $order_id;
		$this->data['amount'] = $amount;
		$this->data['merchant_id'] = $merchant_id;
		$this->data['currency'] = $currency;
		$this->data['language'] = $languageCode;
		$this->data['request_method'] = $request_method;
		$this->data['do3dsecure'] = $do3dsecure;
		$this->data['promptname'] = $promptname;
		$this->data['accept_url'] = $accept_url;
		$this->data['callback_url'] = $callback_url;
		$this->data['cancel_url'] = $cancel_url;
		$this->data['pay_method'] = $pay_method;
        $this->session->data['capture_now']=$this->config->get('billmate_cardpay_transaction_method');

		$this->billmate_transaction(true); 

		$this->data['mac'] = $mac;
		$this->data['description'] = $this->config->get('billmate_cardpay_description');

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
		
        
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_cardpay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/billmate_cardpay.tpl';
		} else {
			$this->template = 'default/template/payment/billmate_cardpay.tpl';
		}
		
		$this->render();
	}

	private function calculateResMac() {

		$data = empty($this->request->post)? $this->request->get : $this->request->post;


                $mac_str="";    
                ksort( $data );
                foreach($data as $key => $value){
                        if( $key != "mac" ) {
                                $mac_str .= $value;
                        }
                }
		$mac_str.=$this->config->get('billmate_cardpay_secret');
                $mac = hash( "sha256", $mac_str );

                return $mac;
	}
	
	public function accept() {
		$this->language->load('payment/billmate_cardpay');

		$error_msg = '';

		$post = empty($this->request->post)? $this->request->get : $this->request->post;
		
		if( isset($post['mac']) && isset($post['order_id']) && isset($post['status']) ) {
			
			$mac_calc = $this->calculateResMac();
			$mac_posted = $post['mac'];
			$data['hash_match'] = true; //($mac_calc == $mac_posted);

                	if( $data['hash_match'] ) {
                        	$order_id = $post['order_id'];
                        	$this->load->model('checkout/order');
                        	$order_info = $this->model_checkout_order->getOrder($order_id);
                        
                        	if ($order_info) {
                                	if ($post['status'] == '0') {
                                        	$this->model_checkout_order->confirm($order_id, $this->config->get('billmate_cardpay_order_status_id'));

                        			$msg = '';
                        			if (isset($post['trans_id'])) {
                                			$msg .= 'trans_id: ' . $post['trans_id'] . "\n";
                        			}
                        			if( isset($post['status'])) {
                                			$msg .= 'status: '. $post['status'] . "\n";
                        			}
                        		
						$this->model_checkout_order->update($order_id, $this->config->get('billmate_cardpay_order_status_id'), $msg, false);
					} else {
						$error_msg = $this->language->get('text_declined');
					}
                        	} else {
					$error_msg = $this->language->get('text_unable');
				}
                	} else {
				$error_msg = $this->language->get('text_com');
			}
		} else {
			$error_msg = $this->language->get('text_fail');
		}

        if($post['status']!= 0 ){
            $error_msg = $post['error_message'];
        }
		if( $error_msg != '' ) {
			$this->data['heading_title'] = $this->language->get('text_failed');
                        $this->data['text_message'] = sprintf($this->language->get('text_error_msg'), $error_msg, $this->url->link('information/contact'));
                        $this->data['button_continue'] = $this->language->get('button_continue');
                        $this->data['continue'] = $this->url->link('common/home');
                        
                        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_cardpay_failure.tpl')) {
                                $this->template = $this->config->get('config_template') . '/template/payment/billmate_cardpay_failure.tpl';
                        } else {
                                $this->template = 'default/template/payment/billmate_cardpay_failure.tpl';
                        }       

                        $this->children = array(
                                'common/column_left',
                                'common/column_right',
                                'common/content_top',
                                'common/content_bottom',
                                'common/footer',
                                'common/header'
                        );
                        
                        $this->response->setOutput($this->render());
		} else {
			try{
				$this->billmate_transaction();			
			}catch(Exception $ex ){
					$this->data['heading_title'] = $this->language->get('text_failed');
					$this->data['text_message'] = sprintf($this->language->get('text_error_msg'), $ex->getMessage(), $this->url->link('information/contact'));
					$this->data['button_continue'] = $this->language->get('button_continue');
					$this->data['continue'] = $this->url->link('common/home');
					
					if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_cardpay_failure.tpl')) {
							$this->template = $this->config->get('config_template') . '/template/payment/billmate_cardpay_failure.tpl';
					} else {
							$this->template = 'default/template/payment/billmate_cardpay_failure.tpl';
					}       

					$this->children = array(
							'common/column_left',
							'common/column_right',
							'common/content_top',
							'common/content_bottom',
							'common/footer',
							'common/header'
					);
					
					$this->response->setOutput($this->render());
			}
		}
	}

	public function callback() {
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_cardpay_callback.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/billmate_cardpay_callback.tpl';
		} else {
			$this->template = 'default/template/payment/billmate_cardpay_callback.tpl';
		}
		$this->response->setOutput($this->render());
	}
	public function billmate_transaction($add_order = false){

		if( !empty($this->session->data['order_created']) ) return;
		$post = empty($this->request->post)? $this->request->get : $this->request->post;
		
		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		$countryData    = $countryQuery->row;

		if( !empty( $post['order_id'] ) ){
			$order_id = $post['order_id'];		
		} else {
			$order_id = $this->session->data['order_id'];
		}
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if( !empty( $this->session->data["shipping_method"] ) )
		$shipping_method = $this->session->data["shipping_method"];
		
		require_once dirname(DIR_APPLICATION).'/billmate/BillMate.php';
		include(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpc.inc');
		include(dirname(DIR_APPLICATION).'/billmate/lib/xmlrpcs.inc');
		
		$eid = (int)$this->config->get('billmate_cardpay_merchant_id');
		
		$key = (int)$this->config->get('billmate_cardpay_secret');
		$ssl = true;

		$debug = false;
		$k = new BillMate($eid,$key,$ssl,$debug, $this->config->get('billmate_cardpay_test') == 1 );
		
		$country_to_currency = array(
			'NOR' => 'NOK',
			'SWE' => 'SEK',
			'FIN' => 'EUR',
			'DNK' => 'DKK',
			'DEU' => 'EUR',
			'NLD' => 'EUR',
		);
		$shippiISO = !empty( $order_info['shipping_iso_code_3'] ) ? $order_info['shipping_iso_code_3'] : $order_info['payment_iso_code_3'];
		
		switch ($shippiISO) {
			// Sweden
			case 'SWE':
				$country = 209;
				$language = 138;
				$encoding = 2;
				$currency = 0;
				break;
			// Finland
			default:
				$country = $order_info['payment_country'];
				$language = 138;
				$encoding = 2;
				$currency = 0;
				break;
		}
		
		$ship_address = array(
			'email'           => $order_info['email'],
			'telno'           => '',
			'cellno'          => '',
			'fname'           => $order_info['shipping_firstname'],
			'lname'           => $order_info['shipping_lastname'],
			'company'         => $order_info['shipping_company'],
			'careof'          => '',
			'street'          => $order_info['shipping_address_1'],
			'house_number'    => '',
			'house_extension' => '',
			'zip'             => $order_info['shipping_postcode'],
			'city'            => $order_info['shipping_city'],
			'country'         => $country,
		);
		
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
		/*$product_query = $this->db->query("SELECT `name`, `model`, `price`, `quantity`, `tax` / `price` * 100 AS 'tax_rate' FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = " . (int) $order_info['order_id'] . " UNION ALL SELECT '', `code`, `amount`, '1', 0.00 FROM `" . DB_PREFIX . "order_voucher` WHERE `order_id` = " . (int) $order_info['order_id'])->rows;	
		foreach ($product_query as $product) {
			$goods_list[] = array(
				'qty'   => (int)$product['quantity'],
				'goods' => array(
					'artno'    => $product['model'],
					'title'    => $product['name'],
					'price'    => (int)$this->currency->format($product['price']*100, 'SEK', '', false),
					'vat'      => (float)($product['tax_rate']),///$product['quantity']
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
				$total['value'] = round($total['value'],2);
				
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
		
		
		$pno = '';
		$pclass = -1;
		$status = $this->session->data['capture_now'] == 'sale' ? 'Paid':'';
		
		$transaction = array(
			"order1"=>(string)$this->session->data['order_id'],
			'order2'=>'',
			"comment"=>'',
			"flags"=>0,
			"gender"=>0,
			"reference"=>"",
			"reference_code"=>"",
			"currency"=>$this->currency->getCode(),//$currency,
			"country"=>209, //$this->config->get('config_country_id')
			"language"=>$this->language->get('code'),//$language,
			"pclass"=>$pclass,
			"shipInfo"=>array("delay_adjust"=>"1"),
			"travelInfo"=>array(),
			"incomeInfo"=>array(),
			"bankInfo"=>array(),
			"sid"=>array("time"=>microtime(true)),
			"extraInfo"=>array(array("cust_no"=>(string)$order_info['customer_id'],"creditcard_data"=>$post)) 
		);

		if(!empty($status )) $transaction["extraInfo"][0]["status"] = $status;
		$bill_address = array_map("utf8_decode",$bill_address);
		$ship_address = array_map("utf8_decode",$ship_address);
		$fingerprint = md5(serialize(array($bill_address, $ship_address,$goods_list)));

		if( empty( $goods_list ) ){
			$result1 = 'Unable to find product in cart';
			throw new Exception ($result1);
			return false;
		}
		
		if( $add_order ) {
			if( !isset($this->session->data['order_api_called']) || $this->session->data['order_api_called']!=$fingerprint) {
				$this->session->data['order_api_called'] = $fingerprint;
				return $k->AddOrder('',$bill_address,$ship_address,$goods_list,$transaction);
			} else {
				return;
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_method` = '" . $this->db->escape($this->language->get('text_title_name')) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);
		$result1 = $k->AddInvoice('',$bill_address,$ship_address,$goods_list,$transaction);

		if(!is_array($result1))
		{ 
			throw new Exception (utf8_encode($result1));
		} else {
			$this->session->data['order_created'] = $result1[0];
			$this->session->data['order_api_called'] = false;			
			$this->redirect($this->url->link('checkout/success'));
		}
	}
}
?>
