<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmateInvoice extends Controller {
    private $error = array();

    public function index() {
		$this->language->load('payment/billmate_invoice');
        
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
        $billmateinvoice = $this->model_setting_setting->getSetting('billmate_invoice');
        if(!isset($billmateinvoice['billmate_invoice_version']) || $billmateinvoice['billmate_invoice_version'] != PLUGIN_VERSION){
            include_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'update.php';

        }
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$status = false;
			
			foreach ($this->request->post['billmate_invoice'] as $billmate_invoice) {
				if ($billmate_invoice['status']) {
					$status = true;
					
					break;
				}
			}			
			
			$data = array(
				'billmate_invoice_pclasses' => $this->pclasses,
				'billmate_invoice_status'   => $status,
                'billmate_invoice_version' => PLUGIN_VERSION
			);
            $this->request->post['billmate_invoice_country'] = $this->request->post['billmateinvoice-country'];
			$this->model_setting_setting->editSetting('billmate_invoice', array_merge($this->request->post, $data));
			
			$this->session->data['success'] = $this->language->get('text_success');
            if(version_compare(VERSION,'2.0.0','>='))
                $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
            else
                $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
		
 		$data['heading_title'] = $this->language->get('heading_title');
        $this->load->model('payment/billmate');
        $data['latest_release'] = (!$this->model_payment_billmate->isLatestRelease(PLUGIN_VERSION)) ? $this->language->get('latest_release') : '';
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_beta'] = $this->language->get('text_beta');
		$data['text_sweden'] = $this->language->get('text_sweden');
		$data['text_norway'] = $this->language->get('text_norway');
		$data['text_finland'] = $this->language->get('text_finland');
		$data['text_denmark'] = $this->language->get('text_denmark');
				
		$data['entry_merchant'] = $this->language->get('entry_merchant');
        $data['entry_merchant_help'] = $this->language->get('entry_merchant_help');
		$data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_secret_help'] = $this->language->get('entry_secret_help');
		$data['entry_server'] = $this->language->get('entry_server');
		$data['entry_mintotal'] = $this->language->get('entry_mintotal');	
		$data['entry_mintotal_help'] = $this->language->get('entry_mintotal_help');
		$data['entry_maxtotal'] = $this->language->get('entry_maxtotal');
		$data['entry_maxtotal_help'] = $this->language->get('entry_maxtotal_help');
		$data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$data['entry_accepted_status'] = $this->language->get('entry_accepted_status');
        $data['entry_test'] = $this->language->get('entry_test');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_description'] = $this->language->get('entry_description');
        $data['entry_available_countries'] = $this->language->get('entry_available_countries');
		
		$data['entry_invoice_fee'] = $this->language->get('entry_invoice_fee');
		$data['entry_invoice_fee_tax'] = $this->language->get('entry_invoice_fee_tax');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_clear'] = $this->language->get('button_clear');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_log'] = $this->language->get('tab_log');
				       
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->error['credentials'])) {
            $data['error_credentials'] = $this->error['credentials'];
        } else {
            $data['error_credentials'] = '';
        }
        
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/billmate_invoice', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
        $data['action'] = $this->url->link('payment/billmate_invoice', 'token=' . $this->session->data['token'], 'SSL');
       
	    $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		$data['countries'] = array();
		
		$data['countries'][] = array(
			'name' => $this->language->get('text_sweden'),
			'code' => 'SWE'
		);
		
/*		$data['countries'][] = array(
			'name' => $this->language->get('text_denmark'),
			'code' => 'DNK'
		);
		
		$data['countries'][] = array(
			'name' => $this->language->get('text_norway'),
			'code' => 'NOR'
		);
		
		$data['countries'][] = array(
			'name' => $this->language->get('text_finland'),
			'code' => 'FIN'
		);
*/
		if (isset($this->request->post['billmate_invoice'])) {
			$data['billmate_invoice'] = $this->request->post['billmate_invoice'];
		} else {
			$data['billmate_invoice'] = $this->config->get('billmate_invoice');
		}

        if(isset($this->request->post['billmateinvoice-country'])){

            $data['billmate_country'] = $this->request->post['billmateinvoice-country'];

        } else {
            $data['billmate_country'] = $this->config->get('billmate_invoice_country');
        }
		

		$this->load->model('localisation/order_status');
			
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        
		$file = DIR_LOGS . 'billmate_invoice.log';
        
        if (file_exists($file)) {
            $data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        } else {
            $data['log'] = '';
        }
        $data['token'] = $this->session->data['token'];
        $data['clear'] = $this->url->link('payment/billmate_invoice/clear', 'token=' . $this->session->data['token'], 'SSL');

        if(version_compare(VERSION,'2.0.0','>=')){

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('payment/two/billmate_invoice.tpl', $data));
        } else {
            $this->data = $data;
            $this->template = 'payment/billmate_invoice.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        }
    }

    public function country_autocomplete(){
        $json = array();
        if(isset($this->request->get['filter_name'])){

            $data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start' => 0,
                'limit' => 20
            );
            $results = $this->getCountries($data);
            foreach($results as $result){
                $json[] = array(
                    'country_id' => $result['country_id'],
                    'name' => $result['name']
                );
            }

            $sort_order = array();
            foreach($json as $key => $value){
                $sort_order[$key] = $value['name'];
            }

            array_multisort($sort_order,SORT_ASC,$json);

            $this->response->setOutput(json_encode($json));

        }
    }

    private function getCountries($data){
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "country";

            $sort_data = array(
                'name',
                'iso_code_2',
                'iso_code_3'
            );
            if(!empty($data['filter_name'])){
                $sql .= " WHERE LOWER(name) LIKE '".$this->db->escape(strtolower($data['filter_name']))."%'";
            }


            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }


            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $country_data = $this->cache->get('country');

            if (!$country_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country ORDER BY name ASC");

                $country_data = $query->rows;

                $this->cache->set('country', $country_data);
            }

            return $country_data;
        }
    }


    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/billmate_invoice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $billmateInvoice = $this->request->post['billmate_invoice'];

        if(!$billmateInvoice['SWE']['merchant']){
            $this->error['merchant_swe'] = $this->language->get('error_merchant_id');
        }
        if(!$billmateInvoice['SWE']['secret']){
            $this->error['secret_swe'] = $this->language->get('error_secret');
        }
        $this->load->model('payment/billmate');
        if(!$this->model_payment_billmate->validateCredentials($billmateInvoice['SWE']['merchant'],$billmateInvoice['SWE']['secret'])){
            $this->error['credentials'] = $this->language->get('error_credentials');
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
        $this->model_setting_setting->editSetting('billmate_invoice',array('billmate_invoice_version' => PLUGIN_VERSION,'billmate_invoice_country' =>array($country['country_id'] => array('name' => $country['name']))));

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
        $this->language->load('payment/billmate_invoice');
		
		$file = DIR_LOGS . 'billmate_invoice.log';
		
		$handle = fopen($file, 'w+'); 
				
		fclose($handle); 
				
		$this->session->data['success'] = $this->language->get('text_success');
        
        $this->redirect($this->url->link('payment/billmate_invoice', 'token=' . $this->session->data['token'], 'SSL'));
    }    
}
