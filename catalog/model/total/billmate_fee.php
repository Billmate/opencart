<?php
if (version_compare(VERSION, '2.2', '<')) {
	class BillmateTotalHelper extends Model
	{
		public function getTotal(&$total_data, &$total, &$taxes)
		{
			$data = array('totals' => &$total_data, 'total' => &$total, 'taxes' => &$taxes);

			$this->calculateTotal($data);
		}
	}
} else {
	class BillmateTotalHelper extends Model
	{
		public function getTotal($data)
		{
			$this->calculateTotal($data);
		}
	}
}

class ModelTotalBillmateFee extends BillmateTotalHelper {

    public function calculateTotal($data) {

		$total_data =& $data['totals'];
		$total =& $data['total'];
		$taxes =& $data['taxes'];

        $this->language->load('total/billmate_fee');

		$status = true;
		
		$billmate_fee = $this->config->get('billmate_fee');
		
		if (!isset($this->session->data['payment_method']['code']) || $this->session->data['payment_method']['code'] != 'billmate_invoice') {
			$status = false;
		} elseif (!isset($billmate_fee['SWE'])) {
			$status = false;
		} elseif (!$billmate_fee['SWE']['status']) {
			$status = false;
		} elseif($billmate_fee['SWE']['fee'] == 0 || strlen($billmate_fee['SWE']['fee']) == 0){
			$status = false;
		}
		
		
        if ($status) {
			$total_data[] = array(
				'code'       => 'billmate_fee',
				'title'      => $this->language->get('text_billmate_fee'),
				'text'       => $this->currency->format($billmate_fee['SWE']['fee'],$this->session->data['currency']),
				'display_text' =>$this->currency->format($billmate_fee['SWE']['fee'],$this->session->data['currency']),
				'value'      => $billmate_fee['SWE']['fee'],
				'sort_order' => $billmate_fee['SWE']['sort_order']
			);
			
			$tax_rates = $this->tax->getRates($billmate_fee['SWE']['fee'], $billmate_fee['SWE']['tax_class_id']);
			
			foreach ($tax_rates as $tax_rate) {
				if (!isset($taxes[$tax_rate['tax_rate_id']])) {
					$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
				} else {
					$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
				}
			}
			
			$total += $billmate_fee['SWE']['fee'];
        }
    }
}
?>
