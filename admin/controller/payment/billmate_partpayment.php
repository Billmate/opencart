<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmatePartpayment extends Controller {
    private $error = array();

    public function index() {
		$this->language->load('payment/billmate_partpayment');
        
        $this->load->model('payment/billmate');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

        $billmatepart = $this->model_setting_setting->getSetting('billmate_partpayment');
        if(!isset($billmatepart['version']) || $billmatepart['version'] != PLUGIN_VERSION){
            include_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'update.php';
	        echo '<script>document.reload()</script>';
        }
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$status = false;
			
			foreach ($this->request->post['billmate_partpayment'] as $billmate_partpayment) {
				if ($billmate_partpayment['status']) {
					$status = true;
					
					break;
				}
			}	
			$this->session->data['success'] = $this->language->get('text_success');

			$this->model_setting_setting->editSetting(
				'billmate_partpayment_country', 
				array()
			);
            $this->request->post['version'] = PLUGIN_VERSION;
			$this->model_setting_setting->editSetting('billmate_partpayment', $this->request->post);
		
			$numFoundPClasses = $this->model_payment_billmate->updatePClasses();
			$pclassURL = $this->url->link('payment/billmate_partpayment/pclasses', 'token=' . $this->session->data['token'], 'SSL');
			$this->session->data['success'] .=
				' ' .
				str_replace('{count}', $numFoundPClasses, $this->language->get('text_pclasses_updated'));
            
			$data = array(
				'billmate_partpayment_pclasses' => $this->model_setting_setting->getSetting('billmate_partpayment_country')
,
				'billmate_partpayment_status'   => $status
			);
			
			$this->model_setting_setting->editSetting('billmate_partpayment', array_merge($this->request->post, $data));



            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
		
 		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_live'] = $this->language->get('text_live');
		$this->data['text_beta'] = $this->language->get('text_beta');
		$this->data['text_sweden'] = $this->language->get('text_sweden');
		$this->data['text_norway'] = $this->language->get('text_norway');
		$this->data['text_finland'] = $this->language->get('text_finland');
		$this->data['text_denmark'] = $this->language->get('text_denmark');
				
		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_secret'] = $this->language->get('entry_secret');
		$this->data['entry_server'] = $this->language->get('entry_server');
		$this->data['entry_mintotal'] = $this->language->get('entry_mintotal');	
		$this->data['entry_maxtotal'] = $this->language->get('entry_maxtotal');	
		$this->data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$this->data['entry_accepted_status'] = $this->language->get('entry_accepted_status');		

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_description'] = $this->language->get('entry_description');
        $this->data['entry_available_countries'] = $this->language->get('entry_available_countries');
		
		$this->data['entry_invoice_fee'] = $this->language->get('entry_invoice_fee');
		$this->data['entry_invoice_fee_tax'] = $this->language->get('entry_invoice_fee_tax');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_clear'] = $this->language->get('button_clear');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_log'] = $this->language->get('tab_log');
		$this->data['regen_pclasses'] = $this->language->get('regen_pclasses');

		$store_currency = $this->config->get('config_currency');
		$store_country  = $this->config->get('config_country_id');
		$countryQuery   = $this->db->query('select * from '. DB_PREFIX.'country where country_id = '.$store_country);
		
		$countryData    = $countryQuery->row;
		$query = $this->db->query('SELECT value FROM '.DB_PREFIX.'setting where serialized=1 and `key`="'.$countryData['iso_code_3'].'"');
		if(!empty($query->row['value'])){
			$countryRates = unserialize( $query->row['value']);
			$countryRates = $countryRates[0];
		}else{
			$countryRates = array(array());
		}

		$this->data['all_pclasses'] = !is_array($countryRates)?array():$countryRates;
				       
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
         
        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/billmate_partpayment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
        $this->data['action'] = $this->url->link('payment/billmate_partpayment', 'token=' . $this->session->data['token'], 'SSL');
       
	    $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['countries'] = array();
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_sweden'),
			'code' => 'SWE'
		);
		
/*		$this->data['countries'][] = array(
			'name' => $this->language->get('text_denmark'),
			'code' => 'DNK'
		);		

		$this->data['countries'][] = array(
			'name' => $this->language->get('text_norway'),
			'code' => 'NOR'
		);
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_finland'),
			'code' => 'FIN'
		);*/

		if (isset($this->request->post['billmate_partpayment'])) {
			$this->data['billmate_partpayment'] = $this->request->post['billmate_partpayment'];
		} else {
			$this->data['billmate_partpayment'] = $this->config->get('billmate_partpayment');
		}
        if(isset($this->request->post['billmatepart-country'])){

            $this->data['billmate_country'] = $this->request->post['billmatepart-country'];

        } else {
            $this->data['billmate_country'] = $this->config->get('billmatepart-country');
        }

		$this->load->model('localisation/order_status');
			
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->data['token'] = $this->session->data['token'];
		$file = DIR_LOGS . 'billmate_partpayment.log';
        
        if (file_exists($file)) {
            $this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        } else {
            $this->data['log'] = '';
        }
        
        $this->data['clear'] = $this->url->link('payment/billmate_partpayment/clear', 'token=' . $this->session->data['token'], 'SSL'); 

        $this->template = 'payment/billmate_partpayment.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/billmate_partpayment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
				
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function install()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE name = 'Sweden' ORDER BY name ASC");
        $country = $query->row;
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('billmate_partpayment',array('version' => PLUGIN_VERSION,'billmatepart-country' =>array($country['country_id'] => array('name' => $country['name']))));

    }
    
    private function parseResponse($node, $document) {
        $child = $node;

        switch ($child->nodeName) {
            case 'string':
                $value = $child->nodeValue;
                break;

            case 'boolean':
                $value = (string) $child->nodeValue;

                if ($value == '0') {
                    $value = false;
                } elseif ($value == '1') {
                    $value = true;
                } else {
                    $value = null;
                }

                break;

            case 'integer':
            case 'int':
            case 'i4':
            case 'i8':
                $value = (int) $child->nodeValue;
                break;

            case 'array':
                $value = array();
                
                $xpath = new DOMXPath($document);
                $entries = $xpath->query('.//array/data/value', $child);
                
                for ($i = 0; $i < $entries->length; $i++) {
                    $value[] = $this->parseResponse($entries->item($i)->firstChild, $document);
                }

                break;

            default:
                $value = null;
        }

        return $value;
    }
	
    public function clear() {
        $this->language->load('payment/billmate_partpayment');
		
		$file = DIR_LOGS . 'billmate_partpayment.log';
		
		$handle = fopen($file, 'w+'); 
				
		fclose($handle); 
				
		$this->session->data['success'] = $this->language->get('text_success');
        
        $this->redirect($this->url->link('payment/billmate_partpayment', 'token=' . $this->session->data['token'], 'SSL'));
    }    
}
