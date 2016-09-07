<?php

require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'JSON.php';

class ControllerPaymentBillmateInvoice extends Controller {

    public function terms(){
        $this->language->load('payment/billmate_invoice');
        
        $data['page_title'] = $this->language->get('page_title');
        $data['body_title'] = $this->language->get('body_title');
        $data['subtitle'] = $this->language->get('subtitle');
        $data['short_description'] = $this->language->get('short_description');
        $data['subline'] = $this->language->get('subline');
        $data['li1'] = $this->language->get('li1');
        $data['li2'] = $this->language->get('li2');
        $data['li3'] = $this->language->get('li3');
        $data['li4'] = $this->language->get('li4');
        $data['li5'] = $this->language->get('li5');
        $data['li6'] = $this->language->get('li6');
        $data['long_description'] = $this->language->get('long_description');
        $data['footer_one'] = $this->language->get('footer_one');
        $data['footer_two'] = $this->language->get('footer_two');
	
	    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/villkor.tpl')) {
		    $this->template = $this->config->get('config_template') . '/template/payment/villkor.tpl';
	    } else {
		    $this->template = 'default/template/payment/villkor.tpl';
	    }
        $json['output'] = $this->render();
        $this->response->setOutput($json['output']);
    }
	public function getInfo(){
		echo 'Billmate Plugin Version: '.PLUGIN_VERSION; 
		phpinfo();
	}
    public function index() {
	    $this->load->model('checkout/order');

	    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

	    if ($order_info) {      

			$store_currency = $this->config->get('config_currency');
			$store_country  = $this->config->get('config_country_id');
			$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
			$countryData    = $countryQuery->row;
			//$this->db->query('update '.DB_PREFIX.'order set order_status_id = 1 where order_id='.$this->session->data['order_id']);		   

			$this->language->load('payment/billmate_invoice');

            $data['text_additional'] = $this->language->get('text_additional');
            $data['text_payment_option'] = $this->language->get('text_payment_option');	
		    $data['text_wait'] = $this->language->get('text_wait');		
			
		    $data['entry_pno'] = $this->language->get('entry_pno');
            $data['help_pno'] = $this->language->get('help_pno');
		    $data['entry_phone_no'] = sprintf($this->language->get('entry_phone_no'),$order_info['email'] );
		    $data['button_confirm'] = $this->language->get('button_confirm');
			$data['wrong_person_number'] = $this->language->get('your_billing_wrong');
			$data['billmate_pno'] = isset($this->session->data['billmate_pno']) ? $this->session->data['billmate_pno'] : false;


		    $billmate_invoice = $this->config->get('billmate_invoice');
	
		    $data['merchant'] = (int)$billmate_invoice['SWE']['merchant'];
		    $data['phone_number'] = $order_info['telephone'];
				
		    if ($countryData['iso_code_3'] == 'DEU' || $countryData['iso_code_3'] == 'NLD') {
			    $address = $this->splitAddress($order_info['payment_address_1']);
		
			    $data['street'] = $address[0];
			    $data['street_number'] = $address[1];
			    $data['street_extension'] = $address[2];
		
			    if ($countryData['iso_code_3'] == 'DEU') {
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
	
		    // Get the invoice fee
		    $query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = " . (int) $order_info['order_id'] . " AND `code` = 'billmate_fee'");
	        
		    if ($query->num_rows && !$query->row['value']) {
			    $data['billmate_fee'] = $query->row['value'];
		    } else {
			    $data['billmate_fee'] = '';
		    }
			$data['description'] = $billmate_invoice['SWE']['description'];

            if(version_compare(VERSION,'2.0.0','>=')){
                $prefix = (version_compare(VERSION,'2.2.0','>=')) ? '' : 'default/template/';
                $preTemplate = (version_compare(VERSION,'2.2.0','>=')) ? '' : $this->config->get('config_template') . '/template/';
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/oc2/billmate_invoice.tpl')) {
                    return $this->load->view($preTemplate . 'payment/oc2/billmate_invoice.tpl',$data);
                } else {
                    return $this->load->view($prefix.'payment/oc2/billmate_invoice.tpl',$data);
                }
            } else {
                $this->data = $data;
                if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/billmate_invoice.tpl')) {
                    $this->template = $this->config->get('config_template') . '/template/payment/billmate_invoice.tpl';
                } else {
                    $this->template = 'default/template/payment/billmate_invoice.tpl';
                }

                $this->render();
            }
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
		$order_id = $this->session->data['order_id'];
		// Order must have identical shipping and billing address or have no shipping address at all
		if ($order_info) {

			if (!$json) {
				$billmate_invoice = $this->config->get('billmate_invoice');
                if(!defined('BILLMATE_SERVER')) define('BILLMATE_SERVER','2.1.7');
                if(!defined('BILLMATE_CLIENT')) define('BILLMATE_CLIENT','Opencart:Billmate:2.2.0');
                if(!defined('BILLMATE_LANGUAGE')) define('BILLMATE_LANGUAGE',($this->language->get('code') == 'se') ? 'sv' : $this->language->get('code'));
				require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';

    				
				$eid = (int)$billmate_invoice['SWE']['merchant'];
				$key = $billmate_invoice['SWE']['secret'];
				
				$ssl = true;
				$debug = false;
                $k = new BillMate($eid,$key,$ssl,$billmate_invoice['SWE']['server'] == 'beta' ,$debug);
                $values['PaymentData'] = array(
                    'method' => 1,
                    'currency' => $this->session->data['currency'],
                    'language' => ($this->language->get('code') == 'se') ? 'sv' : $this->language->get('code'),
                    'country' => 'SE',
                    'autoactivate' => 0,
                    'orderid' => $order_id,
                    'logo' => (isset($billmate_invoice['SWE']['logo']) && strlen($billmate_invoice['SWE']['logo']) > 0) ? $billmate_invoice['SWE']['logo'] : ''

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
                $myocRounding = 0;

                foreach ($products as $product) {

                    $product_total_qty = $product['quantity'];

                    if ($product['minimum'] > $product_total_qty) {
                        $data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
                    }
                    $rates=0;

                    $price = $product['price'];
                    $price = $this->currency->format($price, $order_info['currency_code'], $order_info['currency_value'], false);
                    $tax_rates = $this->tax->getRates($price,$product['tax_class_id']);
                    foreach($tax_rates as $rate){
                        $rates+= $rate['rate'];
                    }
                    $title = $product['name'];
                    if(count($product['option']) > 0){
                        foreach($product['option'] as $option){

                            if(version_compare(VERSION,'2.0','>=')){
                                $title .= ' - ' . $option['name'] . ': ' . $option['value'];
                            } else {
                                $title .= ' - ' . $option['name'] . ': ' . $option['option_value'];
                            }
                        }
                    }
                    $productValue = $this->currency->format($price, $order_info['currency_code'], $order_info['currency_value'], false);
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
                        $totalArr = false;
                        if(version_compare(VERSION,'2.2','>=')){
                            $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                            $this->{'model_total_'.$result['code']}->getTotal($totalArr);
                        }
                        else
                            $this->{'model_total_'.$result['code']}->getTotal($total_data, $total, $taxes);
                        set_error_handler($oldhandler);

                        $amount = 0;
                        if(isset($totalArr) && $totalArr != false) {
                            extract($totalArr);
                        }
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
                        $totalTypeTotal = $this->currency->format($total['value']*100, $order_info['currency_code'], $order_info['currency_value'], false);
                        if($total['code'] != 'billmate_fee' && $total['code'] != 'shipping'){
                            if($total['code'] != 'myoc_price_rounding') {
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
                            } else {
                                $myocRounding = $totalTypeTotal;
                            }
                        }
                        if($total['code'] == 'shipping'){
                            if($total['value'] > 0) {
                                $values['Cart']['Shipping'] = array(
                                    'withouttax' => $this->currency->format($total['value'], $order_info['currency_code'],
                                            $order_info['currency_value'], false) * 100,
                                    'taxrate' => $total['tax_rate']
                                );
                                $orderTotal += $this->currency->format($total['value'], $order_info['currency_code'],
                                        $order_info['currency_value'], false) * 100;
                                $taxTotal += ($this->currency->format($total['value'], $order_info['currency_code'],
                                            $order_info['currency_value'], false) * 100) * ($total['tax_rate'] / 100);
                            }
                        }
                        if($total['code'] == 'billmate_fee'){
                            if($total['value'] > 0) {
                                $values['Cart']['Handling'] = array(
                                    'withouttax' => $this->currency->format($total['value'], $order_info['currency_code'],
                                            $order_info['currency_value'], false) * 100,
                                    'taxrate' => $total['tax_rate']
                                );
                                $orderTotal += $this->currency->format($total['value'], $order_info['currency_code'],
                                        $order_info['currency_value'], false) * 100;
                                $taxTotal += ($this->currency->format($total['value'], $order_info['currency_code'],
                                            $order_info['currency_value'], false) * 100) * ($total['tax_rate'] / 100);
                            }
                        }


                        if($total['code'] != 'myoc_price_rounding' )
                        {
                            if (isset($prepareDiscount[$total['tax_rate']]))
                                $prepareDiscount[$total['tax_rate']] += $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                            else
                                $prepareDiscount[$total['tax_rate']] = $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                            $subtotal += $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'], false) * 100;
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
                                $taxes = array();
                                $total = 0;
                                $total_data = array();
                                $shippingtax = 0;
                                if ($this->config->get($shipping['code'].'_status'))
                                {
                                    $this->load->model('total/'.$shipping['code']);
                                    if(version_compare(VERSION,'2.2','>=')){
                                        $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                                        $this->{'model_total_' . $result['code']}->getTotal($totalArr);
                                    }
                                    else
                                        $this->{'model_total_'.$shipping['code']}->getTotal($total_data, $total, $taxes);

                                    if(isset($totalArr) && $totalArr != false) {
                                        extract($totalArr);
                                    }
                                    foreach ($taxes as $key => $value)
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

                                        $discountIncl = $percent * ($discountValue);

                                        $discountExcl = $discountIncl / (1 + $tax / 100);
                                        $discountToArticle = $this->currency->format($discountIncl, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                                        //$discountToArticle = $this->currency->convert($discountIncl,$this->config->get('config_currency'),$this->session->data['currency']);
                                        if($discountToArticle != 0) {
                                            $values['Articles'][] = array(
                                                'quantity' => 1,
                                                'artnr' => '',
                                                'title' => $total['title'] .' '.$coupon_info['name'].' ' . $tax . $this->language->get('% tax'),
                                                'aprice' => $discountToArticle,
                                                'taxrate' => $tax,
                                                'discount' => 0.0,
                                                'withouttax' => $discountToArticle

                                            );
                                            $orderTotal += $discountToArticle;
                                            $taxTotal += $discountToArticle * ($tax/100);
                                        }

                                    }
                                }
                                $freeshipTotal = $this->currency->format(-$shipping['value'] * 100, $order_info['currency_code'], $order_info['currency_value'], false);
                                //$freeshipTotal = $this->currency->convert(-$shipping['value'] * 100,$this->config->get('config_currency'),$this->session->data['currency']);

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
                                    $discount     = $percent * ($total['value']);
                                    $discountToArticle = $this->currency->format($discount, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                                    //$discountToArticle = $this->currency->convert($discount,$this->config->get('config_currency'),$this->session->data['currency']);

                                    $values['Articles'][] = array(
                                        'quantity'   => 1,
                                        'artnr'    => '',
                                        'title'    => $total['title'].' '.$coupon_info['name'].' ' .$tax.$this->language->get('tax_discount'),
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
                    if(version_compare(VERSION,'2.1.0','>=')){
                        $this->load->model('total/coupon');
                        $coupon_info = $this->model_total_coupon->getCoupon($this->session->data['coupon']);
                    } else {
                        $this->load->model('checkout/coupon');
                        $coupon_info = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);
                    }
                    if(($coupon_info['type'] == 'P' || $coupon_info['type'] == 'F') && $coupon_info['shipping'] == 1)
                    {
                        $shipping = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE code = 'shipping' AND order_id = ".$this->session->data['order_id']);
                        $shipping = $shipping->row;
                        $taxes = array();
                        $total = 0;
                        $total_data = array();
                        $shippingtax = 0;
                        if ($this->config->get($shipping['code'].'_status'))
                        {
                            $this->load->model('total/'.$shipping['code']);

                            if(version_compare(VERSION,'2.2','>='))
                            {
                                $totalArr = array('total_data' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);
                                $this->{'model_total_' . $result['code']}->getTotal($totalArr);
                            }
                            else
                                $this->{'model_total_'.$shipping['code']}->getTotal($total_data, $total, $taxes);

                            if(isset($totalArr) && $totalArr != false) {
                                extract($totalArr);
                            }
                            foreach ($taxes as $key => $value)
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

                                $discountIncl = $percent * ($discountValue);

                                $discountExcl = $discountIncl / (1 + $tax / 100);
                                $discountToArticle = $this->currency->format($discountIncl, $order_info['currency_code'], $order_info['currency_value'], false) * 100;
                                //$discountToArticle = $this->currency->convert($discountIncl,$this->config->get('config_currency'),$this->session->data['currency']);
                                if($discountToArticle != 0) {
                                    $values['Articles'][] = array(
                                        'quantity' => 1,
                                        'artnr' => '',
                                        'title' => $total['title'] .' '.$coupon_info['name'].' ' . $tax . $this->language->get('tax_discount'),
                                        'aprice' => $discountToArticle,
                                        'taxrate' => $tax,
                                        'discount' => 0.0,
                                        'withouttax' => $discountToArticle

                                    );
                                    $orderTotal += $discountToArticle;
                                    $taxTotal += $discountToArticle * ($tax / 100);
                                }

                            }
                        }
                        $freeshipTotal =  $this->currency->format(-$shipping['value'] * 100, $order_info['currency_code'], $order_info['currency_value'], false);
                        //$freeshipTotal = $this->currency->convert(-$shipping['value'] * 100,$this->config->get('config_currency'),$this->session->data['currency']);

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
                            $discount     = $percent * ($total['value']);
                            $discountToArticle = $this->currency->format($discount, $order_info['currency_code'],$order_info['currency_value'], false) * 100;
                            //$discountToArticle = $this->currency->convert($discount,$this->config->get('config_currency'),$this->session->data['currency']);
                            $values['Articles'][] = array(
                                'quantity'   => 1,
                                'artnr'    => '',
                                'title'    => $total['title'].' '.$coupon_info['name'].' ' .$tax.$this->language->get('tax_discount'),
                                'aprice'    => $discountToArticle,
                                'taxrate'      => $tax,
                                'discount' => 0.0,
                                'withouttax'    => $discountToArticle

                            );
                            $orderTotal += $discountToArticle;
                            $taxTotal += $discountToArticle * ($tax/100);

                        }
                    }

                } // End discount isset
                $total = $this->currency->format($order_info['total'],$order_info['currency_code'],$order_info['currency_value'],false);
                $round = round($total*100) - round($orderTotal + $taxTotal);
                if(abs($myocRounding) > abs($round)){
                    $round = $myocRounding;
                }
                $values['Cart']['Total'] = array(
                    'withouttax' => round($orderTotal),
                    'tax' => round($taxTotal),
                    'rounding' => round($round),
                    'withtax' => round($orderTotal + $taxTotal + $round)
                );
                $pno = trim($this->request->post['pno']);


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

                    }
                        $json['error'] = "";
                    }

				}
			if(!isset($json['error'])){
				$ship_api_address = array();
				try {

                    $data = array(
                        'fname'      => empty($addr['firstname']) ? '' : Encoding::fixUTF8($addr['firstname']),
                        'lname'       => empty($addr['lastname']) ? '' : Encoding::fixUTF8($addr['lastname']),
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

					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_code` = 'billmate_invoice', `payment_method` = '" . $this->db->escape($this->language->get('text_title')) . "' WHERE `order_id` = " . (int)$this->session->data['order_id']);
						
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
                            $order_status = $billmate_invoice['SWE']['accepted_status_id'];
						} elseif ($billmate_order_status == 'Pending') {
                            $order_status = $billmate_invoice['SWE']['pending_status_id'];
						} else {
							$order_status = $this->config->get('config_order_status_id');
						}
						
						$comment = sprintf($this->language->get('text_comment'), $result1['number']);

                        if(version_compare(VERSION,'2.0.0','>=')) {
                            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $order_status, $comment, false);
                        }else {
                            $this->model_checkout_order->confirm($this->session->data['order_id'], $order_status, $comment, 1);
                        }
                        if(isset($this->session->data['billmate_pno']))
                            unset($this->session->data['billmate_pno']);

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

    public function getaddress()
    {
        $billmate_invoice = $this->config->get('billmate_invoice');

        require_once dirname(DIR_APPLICATION).'/billmate/Billmate.php';

        $eid = (int)$billmate_invoice['SWE']['merchant'];
        $key = $billmate_invoice['SWE']['secret'];

        $ssl = true;
        $debug = false;
        if(!defined('BILLMATE_SERVER')) define('BILLMATE_SERVER','2.1.7');
        if(!defined('BILLMATE_CLIENT')) define('BILLMATE_CLIENT','Opencart:Billmate:2.2.0');
        if(!defined('BILLMATE_LANGUAGE')) define('BILLMATE_LANGUAGE',$this->language->get('code'));
        $k = new BillMate($eid,$key,$ssl,$billmate_invoice['SWE']['server'] == 'beta' ,$debug);

        $result = $k->getAddress(array('pno' => $this->request->post['pno']));
        if(!isset($result['code'])){
            $db = $this->registry->get('db');
            if( $db == NULL ) $db = $this->db;

            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $result['country']. "' AND status = '1'");
            $countryinfo = $query->row;

            $result['country_id'] = $countryinfo['country_id'];
            $this->session->data['billmate_pno'] = $this->request->post['pno'];
            $response['success'] = true;
            $response['data'] = $result;
        } else {
            $response['success'] = false;
            $response['error'] = $result['message'];
        }
        $this->response->setOutput(json_encode($response));

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
