<?php 
class ModelPaymentBillmateCardpay extends Model {
  	public function getMethod($address, $total) {
		$this->load->language('payment/billmate_cardpay');
		
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('billmate_cardpay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		$status = true;
		$zone_id = $this->config->get('billmate_cardpay_geo_zone_id');
		if ($this->config->get('billmate_cardpay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('billmate_cardpay_geo_zone_id')) {
			$status = true;
		} /*elseif ($query->num_rows) {
			$status = true;
		} */
		
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
			$description = $this->config->get('billmate_cardpay_description');
			$description = strlen( $description) ? $description : 'Billmate Kort - Betala med Visa & Mastercard';
      		$method_data = array( 
        		'code'       => 'billmate_cardpay',
        		'title'      => sprintf($this->language->get('text_title'),$description),
				'sort_order' => $this->config->get('billmate_cardpay_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
}
?>
