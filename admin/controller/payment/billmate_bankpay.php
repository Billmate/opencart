<?php 
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmateBankpay extends Controller {
	private $error = array();

	 
	public function index() {

		$this->load->language('payment/billmate_bankpay');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
        $billmatebank = $this->model_setting_setting->getSetting('billmate_bankpay');
        if(!isset($billmatebank['version']) || $billmatebank['version'] != PLUGIN_VERSION){
            include_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'update.php';
        }
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['version'] = PLUGIN_VERSION;
			$this->model_setting_setting->editSetting('billmate_bankpay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
            if(version_compare(VERSION,'2.0.0','>='))
                $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
            else
                $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

		$data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_test'] = $this->language->get('entry_test');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_total'] = $this->language->get('entry_total');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['entry_description'] = $this->language->get('entry_description');
        $data['entry_available_countries'] = $this->language->get('entry_available_countries');

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
			'href'      => $this->url->link('payment/billmate_bankpay', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('payment/billmate_bankpay', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

                if (isset($this->request->post['billmate_bankpay_merchant_id'])) {
                        $data['billmate_bankpay_merchant_id'] = $this->request->post['billmate_bankpay_merchant_id'];
                } else {
                        $data['billmate_bankpay_merchant_id'] = $this->config->get('billmate_bankpay_merchant_id');
                }
               
                if (isset($this->request->post['billmate_bankpay_secret'])) {
                        $data['billmate_bankpay_secret'] = $this->request->post['billmate_bankpay_secret'];
                } else {
                        $data['billmate_bankpay_secret'] = $this->config->get('billmate_bankpay_secret');
                }
				
                if (isset($this->request->post['billmate_bankpay_test'])) {
                        $data['billmate_bankpay_test'] = $this->request->post['billmate_bankpay_test'];
                } else {
                        $data['billmate_bankpay_test'] = $this->config->get('billmate_bankpay_test');
                }
                if (isset($this->request->post['billmate_bankpay_description'])) {
                        $data['billmate_bankpay_description'] = $this->request->post['billmate_bankpay_description'];
                } else {
                        $data['billmate_bankpay_description'] = $this->config->get('billmate_bankpay_description');
                }
	
		if (isset($this->request->post['billmate_bankpay_total'])) {
			$data['billmate_bankpay_total'] = $this->request->post['billmate_bankpay_total'];
		} else {
			$data['billmate_bankpay_total'] = $this->config->get('billmate_bankpay_total');
		}
				
		if (isset($this->request->post['billmate_bankpay_order_status_id'])) {
			$data['billmate_bankpay_order_status_id'] = $this->request->post['billmate_bankpay_order_status_id'];
		} else {
			$data['billmate_bankpay_order_status_id'] = $this->config->get('billmate_bankpay_order_status_id');
		}
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		

		if (isset($this->request->post['billmate_bankpay_status'])) {
			$data['billmate_bankpay_status'] = $this->request->post['billmate_bankpay_status'];
		} else {
			$data['billmate_bankpay_status'] = $this->config->get('billmate_bankpay_status');
		}
		
		if (isset($this->request->post['billmate_bankpay_sort_order'])) {
			$data['billmate_bankpay_sort_order'] = $this->request->post['billmate_bankpay_sort_order'];
		} else {
			$data['billmate_bankpay_sort_order'] = $this->config->get('billmate_bankpay_sort_order');
		}
        if(isset($this->request->post['billmatebank-country'])){

            $data['billmate_country'] = $this->request->post['billmatebank-country'];

        } else {
            $data['billmate_country'] = $this->config->get('billmatebank-country');
        }
        $data['token'] = $this->session->data['token'];
        if(version_compare(VERSION,'2.0.0','>=')){

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('payment/billmate_bankpay.tpl', $data));
        } else {
            $this->data = $data;
            $this->template = 'payment/billmate_bankpay.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        }
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/billmate_bankpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

                if (!$this->request->post['billmate_bankpay_merchant_id']) {
                        $this->error['merchant'] = $this->language->get('error_merchant_id');
                }

                if (!$this->request->post['billmate_bankpay_secret']) {
                        $this->error['secret'] = $this->language->get('error_secret');
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
        $this->model_setting_setting->editSetting('billmate_bankpay',array('version' => PLUGIN_VERSION,'billmatebank-country' =>array($country['country_id'] => array('name' => $country['name']))));
    }
}
?>
