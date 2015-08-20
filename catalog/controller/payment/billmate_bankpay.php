<?php

require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';

class ControllerPaymentBillmateBankpay extends Controller {
	public function cancel(){

        if(version_compare(VERSION,'2.0.0','>='))
            $this->response->redirect($this->url->link('checkout/checkout'));
        else
		    $this->redirect($this->url->link('checkout/checkout'));
	}
	public function index() {
        $this->language->load('payment/billmate_bankpay');

        if( !empty($this->session->data['order_created']) ) $this->session->data['order_created'] = '';
				
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_wait'] = $this->language->get('text_wait');
        $this->load->model('checkout/order');

        $data['description'] = $this->config->get('billmate_cardpay_description');
        error_log('index bank');
        if(version_compare(VERSION,'2.0.0','>=')){

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl',$data);
            } else {
                return $this->load->view('default/template/payment/billmate_bankpay.tpl',$data);
            }
        } else {
            $this->data = $data;
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay.tpl';
            } else {
                $this->template = 'default/template/payment/billmate_bankpay.tpl';
            }

            $this->render();
        }


	}
	
	public function accept() {
		$this->language->load('payment/billmate_bankpay');

		$error_msg = '';

		$post = empty($this->request->post)? $this->request->get : $this->request->post;
        $eid = (int)$this->config->get('billmate_bankpay_merchant_id');

        $key = (int)$this->config->get('billmate_bankpay_secret');

        require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
        $k = new BillMate($eid,$key);
        if(is_array($post))
        {
            foreach($post as $key => $value)
                $post[$key] = htmlspecialchars_decode($value,ENT_COMPAT);
        }

        $post = $k->verify_hash($post);
        $this->log->write('postAcc'.print_r($post,true));
		if(isset($post['orderid']) && isset($post['status']) ) {



                        	$order_id = $post['orderid'];
                        	$this->load->model('checkout/order');
                        	$order_info = $this->model_checkout_order->getOrder($order_id);
                        
                        	if ($order_info) {
                                if (($post['status'] == 'Paid' ) && $order_info['order_status_id'] != $this->config->get('billmate_bankpay_order_status_id') && !$this->cache->get('order'.$order_id)) {
                                    $this->cache->set('order'.$order_id,1);
                                    if(version_compare(VERSION,'<','2.0'))
                                        $this->model_checkout_order->confirm($order_id, $this->config->get('billmate_bankpay_order_status_id'));

                                    $msg = '';
                                    if (isset($post['number'])) {
                                            $msg .= 'invoice_id: ' . $post['number'] . "\n";
                                    }
                                    if( isset($post['status'])) {
                                            $msg .= 'status: '. $post['status'] . "\n";
                                    }
                                    if(version_compare(VERSION,'<','2.0'))
                                        $this->model_checkout_order->update($order_id, $this->config->get('billmate_bankpay_order_status_id'), $msg, false);
                                    else
                                        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id',$msg,true));


                                    $this->cache->delete('order'.$order_id);
                                } else if($post['status'] == 'Pending'){
                                    $this->cache->set('order'.$order_id,1);
                                    if(version_compare(VERSION,'<','2.0'))
                                        $this->model_checkout_order->confirm($order_id, 1);
                                    /* Set STatus to pending */
                                    $msg = '';
                                    if (isset($post['number'])) {
                                        $msg .= 'invoice_id: ' . $post['number'] . "\n";
                                    }
                                    if( isset($post['status'])) {
                                        $msg .= 'status: '. $post['status'] . "\n";
                                    }
                                    if(version_compare(VERSION,'>=','2.0'))
                                        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('billmate_bankpay_order_status_id'),$msg,false);
                                    else
                                        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('billmate_bankpay_order_status_id'), $msg, 1);


                                    $this->cache->delete('order'.$order_id);
                                }
                                else {
                                    $error_msg = ($order_info['order_status_id'] == $this->config->get('billmate_bankpay_order_status_id')) ? '' :$this->language->get('text_declined');
                                }
                        	} else {
					$error_msg = $this->language->get('text_unable');
				}

		} else {
			$error_msg = $this->language->get('text_fail');
		}

        if($post['status'] == 'Cancelled'){
            $error_msg = $post['error_message'];
        }
		if( $error_msg != '' ) {
			$data['heading_title'] = $this->language->get('text_failed');
                        $data['text_message'] = sprintf($this->language->get('text_error_msg'), $error_msg, $this->url->link('information/contact'));
                        $data['button_continue'] = $this->language->get('button_continue');
                        $data['continue'] = $this->url->link('common/home');
                        if(version_compare(VERSION,'2.0.0','>=')){

                            $data['column_left'] = $this->load->controller('common/column_left');
                            $data['header'] = $this->load->controller('common/header');
                            $data['footer'] = $this->load->controller('common/footer');
                            $data['content_top'] = $this->load->controller('common/content_top');
                            $data['content_bottom'] = $this->load->controller('common/content_bottom');
                            $data['column_right'] = $this->load->controller('common/column_right');

                            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                                return $this->load->view($this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl',$data);
                            } else {
                                return $this->load->view('default/template/payment/billmate_bankpay_failure.tpl',$data);
                            }
                        } else {
                            $this->data = $data;
                            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl';
                            } else {
                                $this->template = 'default/template/payment/billmate_bankpay_failure.tpl';
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
		} else {
			try{
                if(version_compare(VERSION,'2.0.0','>='))
                    $this->response->redirect($this->url->link('checkout/success'));
                else
                    $this->redirect($this->url->link('checkout/success'));
			}catch(Exception $ex ){
					$data['heading_title'] = $this->language->get('text_failed');
					$data['text_message'] = sprintf($this->language->get('text_error_msg'), $ex->getMessage(), $this->url->link('information/contact'));
					$data['button_continue'] = $this->language->get('button_continue');
					$data['continue'] = $this->url->link('common/home');

                if(version_compare(VERSION,'2.0.0','>=')){

                    $data['column_left'] = $this->load->controller('common/column_left');
                    $data['header'] = $this->load->controller('common/header');
                    $data['footer'] = $this->load->controller('common/footer');
                    $data['content_top'] = $this->load->controller('common/content_top');
                    $data['content_bottom'] = $this->load->controller('common/content_bottom');
                    $data['column_right'] = $this->load->controller('common/column_right');

                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                        return $this->load->view($this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl',$data);
                    } else {
                        return $this->load->view('default/template/payment/billmate_bankpay_failure.tpl',$data);
                    }
                } else {
                    $this->data = $data;
                    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl')) {
                        $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_failure.tpl';
                    } else {
                        $this->template = 'default/template/payment/billmate_bankpay_failure.tpl';
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
	}

	public function callback() {

        $post = file_get_contents('php://input');

        $post = !isset($post['data']) ? $this->request->get : $post;
        $post = !isset($post['data']) ? $_REQUEST : $post;
        if(is_array($post))
        {
            foreach($post as $key => $value)
                $post[$key] = htmlspecialchars_decode($value,ENT_COMPAT);
        }
        $this->request->post = $post;
        $this->load->model('checkout/order');
        $eid = (int)$this->config->get('billmate_bankpay_merchant_id');

        $key = (int)$this->config->get('billmate_bankpay_secret');

        require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
        $k = new BillMate($eid,$key);
        $post = $k->verify_hash($post);

        if(isset($post['orderid']) && isset($post['status']) && isset($post['number'])){

            $order_info = $this->model_checkout_order->getOrder($post['orderid']);

            if(($post['status'] == 'Paid') && $order_info && $order_info['order_status_id'] != $this->config->get('billmate_bankpay_order_status_id') && !$this->cache->get('order'.$post['orderid'])){
                $this->cache->set('order'.$post['orderid'],1);

                $order_id = $post['orderid'];
                if(version_compare(VERSION,'<','2.0'))
                    $this->model_checkout_order->confirm($order_id, $this->config->get('billmate_bankpay_order_status_id'));

                $msg = '';
                $msg .= 'invoice_id: ' . $post['number'] . "\n";
                $msg .= 'status: '. $post['status'] . "\n";
                if(version_compare(VERSION,'>=','2.0'))
                    $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('billmate_bankpay_order_status_id'),$msg,false);
                else
                    $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('billmate_bankpay_order_status_id'), $msg, 1);

                $this->cache->delete('order'.$post['orderid']);

            }else if($post['status'] == 'Pending' && $order_info && $order_info['order_status_id'] != $this->config->get('billmate_cardpay_order_status_id') && !$this->cache->get('order'.$post['orderid'])){
                $this->cache->set('order'.$post['orderid'],1);

                $order_id = $post['orderid'];
                if(version_compare(VERSION,'<','2.0'))
                    $this->model_checkout_order->confirm($order_id, 1);
                /* Set STatus to pending */
                $msg = '';
                if (isset($post['number'])) {
                    $msg .= 'invoice_id: ' . $post['number'] . "\n";
                }
                if( isset($post['status'])) {
                    $msg .= 'status: '. $post['status'] . "\n";
                }
                if(version_compare(VERSION,'<','2.0'))
                    $this->model_checkout_order->update($order_id, 1,$msg, false);
                else
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('billmate_bankpay_order_status_id',$msg,false));

                $this->cache->delete('order'.$post['orderid']);
            } elseif($this->cache->get('order'.$post['orderid'])) {

                die('ERROR');
            }
        }

        if(version_compare(VERSION,'2.0.0','>=')){
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl');
            } else {
                return $this->load->view('default/template/payment/billmate_bankpay_callback.tpl');
            }
        }else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/payment/billmate_bankpay_callback.tpl';
            } else {
                $this->template = 'default/template/payment/billmate_bankpay_callback.tpl';
            }
            $this->response->setOutput($this->render());
        }
	}
	public function sendinvoice(){
        $this->language->load('payment/billmate_bankpay');


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
		// Fix for checkouts that creates new orders for every action.
		// Check if order is Created AND old_order_id is equal to $order_id
		if( !empty($this->session->data['order_created']) && isset($this->session->data['old_order_id']) && $this->session->data['old_order_id'] == $order_id ) return;

		// If order_id not equal old_order_id reset order_api_called to force order to be sent again.
		if(isset($this->session->data['old_order_id']) && $this->session->data['old_order_id'] != $order_id){

			$this->session->data['order_api_called'] = '';
		}
        $this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);

		if( !empty( $this->session->data["shipping_method"] ) )
		$shipping_method = $this->session->data["shipping_method"];
		
		require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';
		
		$eid = (int)$this->config->get('billmate_bankpay_merchant_id');
		
		$key = (int)$this->config->get('billmate_bankpay_secret');
		$ssl = true;

		$debug = false;

        define('BILLMATE_SERVER','2.1.7');
        define('BILLMATE_CLIENT','Opencart:Billmate:2.0');
		$k = new BillMate($eid,$key,$ssl,$this->config->get('billmate_bankpay_test') == 1 ,$debug);
		$values['PaymentData'] = array(
            'method' => 16,
            'currency' => $this->currency->getCode(),
            'language' => $this->language->get('code'),
            'country' => 'SE',
            'autoactivate' => 0,
            'orderid' => $order_id
        );

		$values['PaymentInfo'] = array(
            'paymentdate' => date('Y-m-d')
        );

        $values['Card'] = array(
            'callbackurl' => $this->url->link('payment/billmate_bankpay/callback'),
            'accepturl' => $this->url->link('payment/billmate_bankpay/accept'),
            'cancelurl' => $this->url->link('payment/billmate_bankpay/cancel'),
            'returnmethod' => 'GET'
        );
        $values['Customer']['nr'] = $this->customer->getId();
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

			$tax_rates = $this->tax->getRates($product['price'],$product['tax_class_id']);
			foreach($tax_rates as $rate){
				$rates+= $rate['rate'];
			}
            $title = $product['name'];
            if(count($product['option']) > 0){
                foreach($product['option'] as $option){
                    $title .= ' - '.$option['name'].': '.$option['option_value'];
                }
            }
            $productValue = $this->currency->format($product['price']*100, $this->currency->getCode(), '', false);
            $values['Articles'][] = array(
				    'quantity'   => (int)$product_total_qty,
					'artnr'    => $product['model'],
					'title'    => $title,
					'aprice'    => $product['price']*100,
					'taxrate'      => (float)($rates),
					'discount' => 0.0,
					'withouttax'    => $product_total_qty * ($product['price']*100),

			);
            $orderTotal += $product_total_qty * ($product['price']*100);
            $taxTotal += ($product_total_qty * ($product['price']*100)) * ($rates/100);

			$subtotal += ($product['price'] * 100) * $product_total_qty;
			$productTotal += ($product['price'] * 100) * $product_total_qty;
			if(isset($prepareDiscount[$rates])){
				$prepareDiscount[$rates] += ($product['price'] * 100) * $product_total_qty;
			} else {
				$prepareDiscount[$rates] = ($product['price'] * 100) * $product_total_qty;
			}
			if(isset($prepareProductDiscount[$rates])){
				$prepareProductDiscount[$rates] += ($product['price'] * 100) * $product_total_qty;
			} else {
				$prepareProductDiscount[$rates] = ($product['price'] * 100) * $product_total_qty;
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
                    'withouttax' => $total['value'] * 100,
                    'taxrate' => $total['tax_rate']
                );
                    $orderTotal += $total['value'] * 100;
                    $taxTotal += ($total['value'] * 100) * ($total['tax_rate']/100);
                }
                if($total['code'] == 'billmate_fee'){
                    $values['Cart']['Handling'] = array(
                    'withouttax' => $total['value'] * 100,
                    'taxrate' => $total['tax_rate']
                );
                    $orderTotal += $total['value'] * 100;
                    $taxTotal += ($total['value'] * 100) * ($total['tax_rate']/100);
                }
				if($total['code'] != 'myoc_price_rounding' )
				{
					if (isset($prepareDiscount[$total['tax_rate']]))
						$prepareDiscount[$total['tax_rate']] += $total['value'] * 100;
					else
						$prepareDiscount[$total['tax_rate']] = $total['value'] * 100;
					$subtotal += $total['value'] * 100;
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
                                $values['Articles'] = array(
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
						$values['Articles'] = array(
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
				$values['Articles'] = array(
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
					$values['Articles'] = array(
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

        $round = ($order_info['total']*100) - ($orderTotal + $taxTotal);
        $values['Cart']['Total'] = array(
            'withouttax' => $orderTotal,
            'tax' => $taxTotal,
            'rounding' => $round,
            'withtax' => $orderTotal + $taxTotal + $round
        );
		


		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_code` = 'billmate_bankpay',  `payment_method` = '" . $this->db->escape(strip_tags($this->language->get('text_title_name'))) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);

        $result1 = $k->addPayment($values);
        if(isset($result1['code'])){
            $response['success'] = false;
            $response['message'] = $result1['message'];
        } else {
            $this->session->data['order_created'] = $result1['orderid'];
            $this->session->data['bankorder_api_called'] = false;
            $response['success'] = true;
            $response['url'] = $result1['url'];
        }
        $this->response->setOutput(my_json_encode($response));
	}
}
?>
