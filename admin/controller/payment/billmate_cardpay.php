<?php 
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmateCardpay extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/billmate_cardpay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
        $billmatecard = $this->model_setting_setting->getSetting('billmate_cardpay');
        if(!isset($billmatecard['version']) || $billmatecard['version'] != PLUGIN_VERSION){
            include_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'update.php';
        }
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['version'] = PLUGIN_VERSION;
			$this->model_setting_setting->editSetting('billmate_cardpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');

		$this->data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$this->data['entry_secret'] = $this->language->get('entry_secret');
        $this->data['entry_test'] = $this->language->get('entry_test');				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_order_cancel_status'] = $this->language->get('entry_order_cancel_status');		
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_transaction_method'] = $this->language->get('entry_transaction_method');
		$this->data['entry_billmate_cardpay_authorization'] = $this->language->get('entry_billmate_cardpay_authorization');
		$this->data['entry_billmate_cardpay_sale'] = $this->language->get('entry_billmate_cardpay_sale');
		$this->data['prompt_name_entry'] = $this->language->get('entry_prompt_name');
		$this->data['enable_3dsecure'] = $this->language->get('entry_3dsecure');
        $this->data['entry_available_countries'] = $this->language->get('entry_available_countries');
		
		$this->data['billmate_cardpay_transaction_method'] = $this->config->get('billmate_cardpay_transaction_method');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

                if (isset($this->error['merchant_id'])) {
                        $this->data['error_merchant_id'] = $this->error['merchant'];
                } else {
                        $this->data['error_merchant_id'] = '';
                }

                if (isset($this->error['secret'])) {
                        $this->data['error_secret'] = $this->error['secret'];
                } else {
                        $this->data['error_secret'] = '';
                }

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
			'href'      => $this->url->link('payment/billmate_cardpay', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('payment/billmate_cardpay', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');	

                if (isset($this->request->post['billmate_cardpay_merchant_id'])) {
                        $this->data['billmate_cardpay_merchant_id'] = $this->request->post['billmate_cardpay_merchant_id'];
                } else {
                        $this->data['billmate_cardpay_merchant_id'] = $this->config->get('billmate_cardpay_merchant_id');
                }
               
                if (isset($this->request->post['billmate_cardpay_secret'])) {
                        $this->data['billmate_cardpay_secret'] = $this->request->post['billmate_cardpay_secret'];
                } else {
                        $this->data['billmate_cardpay_secret'] = $this->config->get('billmate_cardpay_secret');
                }
 
                if (isset($this->request->post['billmate_prompt_name'])) {
                        $this->data['billmate_prompt_name'] = $this->request->post['billmate_prompt_name'];
                } else {
                        $this->data['billmate_prompt_name'] = $this->config->get('billmate_prompt_name');
                }
               if (isset($this->request->post['billmate_cardpay_description'])) {
                        $this->data['billmate_cardpay_description'] = $this->request->post['billmate_cardpay_description'];
                } else {
                        $this->data['billmate_cardpay_description'] = $this->config->get('billmate_cardpay_description');
                }
                if (isset($this->request->post['billmate_enable_3dsecure'])) {
                        $this->data['billmate_enable_3dsecure'] = $this->request->post['billmate_enable_3dsecure'];
                } else {
                        $this->data['billmate_enable_3dsecure'] = $this->config->get('billmate_enable_3dsecure');
                }
				
                if (isset($this->request->post['billmate_cardpay_test'])) {
                        $this->data['billmate_cardpay_test'] = $this->request->post['billmate_cardpay_test'];
                } else {
                        $this->data['billmate_cardpay_test'] = $this->config->get('billmate_cardpay_test');
                }
	
		if (isset($this->request->post['billmate_cardpay_total'])) {
			$this->data['billmate_cardpay_total'] = $this->request->post['billmate_cardpay_total'];
		} else {
			$this->data['billmate_cardpay_total'] = $this->config->get('billmate_cardpay_total'); 
		}
				
		if (isset($this->request->post['billmate_cardpay_order_status_id'])) {
			$this->data['billmate_cardpay_order_status_id'] = $this->request->post['billmate_cardpay_order_status_id'];
		} else {
			$this->data['billmate_cardpay_order_status_id'] = $this->config->get('billmate_cardpay_order_status_id'); 
		}

        if (isset($this->request->post['billmate_cardpay_order_cancel_status_id'])) {
            $this->data['billmate_cardpay_order_cancel_status_id'] = $this->request->post['billmate_cardpay_order_cancel_status_id'];
        } else {
            $this->data['billmate_cardpay_order_cancel_status_id'] = $this->config->get('billmate_cardpay_order_cancel_status_id');
        }
        $this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		


		if (isset($this->request->post['billmate_cardpay_status'])) {
			$this->data['billmate_cardpay_status'] = $this->request->post['billmate_cardpay_status'];
		} else {
			$this->data['billmate_cardpay_status'] = $this->config->get('billmate_cardpay_status');
		}
		
		if (isset($this->request->post['billmate_cardpay_sort_order'])) {
			$this->data['billmate_cardpay_sort_order'] = $this->request->post['billmate_cardpay_sort_order'];
		} else {
			$this->data['billmate_cardpay_sort_order'] = $this->config->get('billmate_cardpay_sort_order');
		}

        if(isset($this->request->post['billmatecard-country'])){

            $this->data['billmate_country'] = $this->request->post['billmatecard-country'];

        } else {
            $this->data['billmate_country'] = $this->config->get('billmatecard-country');
        }
        $this->data['token'] = $this->session->data['token'];
		$this->template = 'payment/billmate_cardpay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
    public function install()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE name = 'Sweden' ORDER BY name ASC");
        $country = $query->row;
        $this->log->write(print_r($country,true));
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('billmate_cardpay',array('version' => PLUGIN_VERSION,'billmatecard-country' =>array($country['country_id'] => array('name' => $country['name']))));

    }

    public function country_autocomplete(){
        $this->load->language('payment/billmate_cardpay');
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
            $json[] = array(
                'country_id' => 0,
                'name' => $this->language->get('text_all_countries')
            );

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
		if (!$this->user->hasPermission('modify', 'payment/billmate_cardpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

                if (!$this->request->post['billmate_cardpay_merchant_id']) {
                        $this->error['merchant'] = $this->language->get('error_merchant_id');
                }

                if (!$this->request->post['billmate_cardpay_secret']) {
                        $this->error['secret'] = $this->language->get('error_secret');
                }
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>
