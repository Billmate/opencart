<?php 
class ModelPaymentBillmateBankPay extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/billmate_bankpay');
		
		$status = true;
		$zone_id = $this->config->get('billmate_bankpay_geo_zone_id');
		if ($this->config->get('billmate_bankpay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('billmate_bankpay_geo_zone_id')) {
			$status = true;
		} 
		
		if( $status){
            $available_countries = array_keys($this->config->get('billmatebank-country'));
            if(in_array($address['country_id'],$available_countries)){
                $status = true;
            } else {
                $status = false;
            }
		} 

		$method_data = array();
	
		if ($status) {  
			$description = $this->config->get('billmate_bankpay_description');
			$description = strlen( $description) ? $description : 'Betala med banköverföring.';
      		$method_data = array( 
        		'code'       => 'billmate_bankpay',
        		'title'      => sprintf($this->language->get('text_title'),$description),
				'sort_order' => $this->config->get('billmate_bankpay_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
