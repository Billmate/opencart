<?php
class ControllerTotalBillmateFee extends Controller {
    private $error = array();
    
    public function index() {
		$this->language->load('total/billmate_fee');

        $this->document->setTitle($this->language->get('heading_title'));
        
		$this->load->model('setting/setting');
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$status = false;
			
			foreach ($this->request->post['billmate_fee'] as $billmate_account) {
				if ($billmate_account['status']) {
					$status = true;
					
					break;
				}
			}
            
            $this->model_setting_setting->editSetting('billmate_fee', array_merge($this->request->post, array('billmate_fee_status' => $status)));

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
        }
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_none'] = $this->language->get('text_none');
		
		$this->data['entry_fee'] = $this->language->get('entry_fee');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
					
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
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
            'text'      => $this->language->get('text_total'),
            'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('total/billmate_fee', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
                
		$this->data['action'] = $this->url->link('total/billmate_fee', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['countries'] = array();
		
		$this->data['countries'][] = array(
			'name' => $this->language->get('text_sweden'),
			'code' => 'SWE'
		);

		
        if (isset($this->request->post['billmate_fee'])) {
            $this->data['billmate_fee'] = $this->request->post['billmate_fee'];
        } else {
            $this->data['billmate_fee'] = $this->config->get('billmate_fee');
        }
             
        $this->load->model('localisation/tax_class');   
		
        $this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        $this->template = 'total/billmate_fee.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'total/billmate_fee')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>