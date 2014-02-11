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
			$sql = 'select * from ' . DB_PREFIX . 'zone_to_geo_zone where country_id='.(int)$address['country_id'].' and geo_zone_id = '.(int)$zone_id ;
			$query2 = $this->db->query($sql);
			
			if( $zone_id == 0  ){
				$status = true;
			}elseif($query2->num_rows){
				$status = true;
			}else{
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
