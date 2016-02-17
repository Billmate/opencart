<?php 
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmateCardpay extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/billmate_cardpay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
        $billmatecard = $this->model_setting_setting->getSetting('billmate_cardpay');
        if(!isset($billmatecard['billmate_cardpay_version']) || $billmatecard['billmate_cardpay_version'] != PLUGIN_VERSION){
            include_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'update.php';
        }
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['billmate_cardpay_version'] = PLUGIN_VERSION;
            $this->request->post['billmate_cardpay_country'] = $this->request->post['billmatecard-country'];
			$this->model_setting_setting->editSetting('billmate_cardpay', $this->request->post);

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
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_logo'] = $this->language->get('entry_logo');
        $data['entry_logo_help'] = $this->language->get('entry_logo_help');

		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_merchant_help'] = $this->language->get('entry_merchant_help');
		$data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_secret_help'] = $this->language->get('entry_secret_help');
        $data['entry_test'] = $this->language->get('entry_test');				
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');
        $data['help_total'] = $this->language->get('help_total');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_transaction_method'] = $this->language->get('entry_transaction_method');
		$data['entry_billmate_cardpay_authorization'] = $this->language->get('entry_billmate_cardpay_authorization');
		$data['entry_billmate_cardpay_sale'] = $this->language->get('entry_billmate_cardpay_sale');
		$data['prompt_name_entry'] = $this->language->get('entry_prompt_name');
		$data['enable_3dsecure'] = $this->language->get('entry_3dsecure');
        $data['entry_available_countries'] = $this->language->get('entry_available_countries');
		
		$data['billmate_cardpay_transaction_method'] = $this->config->get('billmate_cardpay_transaction_method');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
        $data['billmate_version'] = PLUGIN_VERSION;

        $data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['merchant_id'])) {
                $data['error_merchant_id'] = $this->error['merchant'];
        } else {
                $data['error_merchant_id'] = '';
        }

        if (isset($this->error['secret'])) {
                $data['error_secret'] = $this->error['secret'];
        } else {
                $data['error_secret'] = '';
        }


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
			'href'      => $this->url->link('payment/billmate_cardpay', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('payment/billmate_cardpay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');	

        if (isset($this->request->post['billmate_cardpay_merchant_id'])) {
                $data['billmate_cardpay_merchant_id'] = $this->request->post['billmate_cardpay_merchant_id'];
        } else {
                $data['billmate_cardpay_merchant_id'] = $this->config->get('billmate_cardpay_merchant_id');
        }

        if (isset($this->request->post['billmate_cardpay_secret'])) {
                $data['billmate_cardpay_secret'] = $this->request->post['billmate_cardpay_secret'];
        } else {
                $data['billmate_cardpay_secret'] = $this->config->get('billmate_cardpay_secret');
        }

        if (isset($this->request->post['billmate_prompt_name'])) {
                $data['billmate_prompt_name'] = $this->request->post['billmate_cardpay_prompt_name'];
        } else {
                $data['billmate_prompt_name'] = $this->config->get('billmate_cardpay_prompt_name');
        }
       if (isset($this->request->post['billmate_cardpay_description'])) {
                $data['billmate_cardpay_description'] = $this->request->post['billmate_cardpay_description'];
        } else {
                $data['billmate_cardpay_description'] = $this->config->get('billmate_cardpay_description');
        }
        if (isset($this->request->post['billmate_enable_3dsecure'])) {
                $data['billmate_enable_3dsecure'] = $this->request->post['billmate_cardpay_enable_3dsecure'];
        } else {
                $data['billmate_enable_3dsecure'] = $this->config->get('billmate_cardpay_enable_3dsecure');
        }

        if (isset($this->request->post['billmate_cardpay_test'])) {
                $data['billmate_cardpay_test'] = $this->request->post['billmate_cardpay_test'];
        } else {
                $data['billmate_cardpay_test'] = $this->config->get('billmate_cardpay_test');
        }

        if (isset($this->request->post['billmate_cardpay_logo'])) {
            $data['billmate_cardpay_logo'] = $this->request->post['billmate_cardpay_logo'];
        } else {
            $data['billmate_cardpay_logo'] = $this->config->get('billmate_cardpay_logo');
        }
	
		if (isset($this->request->post['billmate_cardpay_total'])) {
			$data['billmate_cardpay_total'] = $this->request->post['billmate_cardpay_total'];
		} else {
			$data['billmate_cardpay_total'] = $this->config->get('billmate_cardpay_total'); 
		}
				
		if (isset($this->request->post['billmate_cardpay_order_status_id'])) {
			$data['billmate_cardpay_order_status_id'] = $this->request->post['billmate_cardpay_order_status_id'];
		} else {
			$data['billmate_cardpay_order_status_id'] = $this->config->get('billmate_cardpay_order_status_id'); 
		}

        $this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		


		if (isset($this->request->post['billmate_cardpay_status'])) {
			$data['billmate_cardpay_status'] = $this->request->post['billmate_cardpay_status'];
		} else {
			$data['billmate_cardpay_status'] = $this->config->get('billmate_cardpay_status');
		}
		
		if (isset($this->request->post['billmate_cardpay_sort_order'])) {
			$data['billmate_cardpay_sort_order'] = $this->request->post['billmate_cardpay_sort_order'];
		} else {
			$data['billmate_cardpay_sort_order'] = $this->config->get('billmate_cardpay_sort_order');
		}

        if(isset($this->request->post['billmatecard-country'])){

            $data['billmate_country'] = $this->request->post['billmatecard-country'];

        } else {
            $data['billmate_country'] = $this->config->get('billmate_cardpay_country');
        }
        $data['token'] = $this->session->data['token'];
        if(version_compare(VERSION,'2.0.0','>=')){

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('payment/two/billmate_cardpay.tpl', $data));
        } else {
            $this->data = $data;
            $this->template = 'payment/billmate_cardpay.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        }
	}
    public function install()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE name = 'Sweden' ORDER BY name ASC");
        $country = $query->row;
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('billmate_cardpay',array('billmate_cardpay_version' => PLUGIN_VERSION,'billmate_cardpay_country' =>array($country['country_id'] => array('name' => $country['name']))));

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
        $this->load->model('payment/billmate');
        if(!$this->model_payment_billmate->validateCredentials($this->request->post['billmate_cardpay_merchant_id'],$this->request->post['billmate_cardpay_secret'])){
            $this->error['credentials'] = $this->language->get('error_credentials');
        }
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>
