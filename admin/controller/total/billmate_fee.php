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
            if(version_compare(VERSION,'2.0.0','>=')) {
                if (version_compare(VERSION, '2.3', '<'))
                    $this->response->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
                else
                    $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
            }
            else
                $this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
        }
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		
		$data['entry_fee'] = $this->language->get('entry_fee');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
					
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
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
            'text'      => $this->language->get('text_total'),
            'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('total/billmate_fee', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
                
		$data['action'] = $this->url->link('total/billmate_fee', 'token=' . $this->session->data['token'], 'SSL');


        if(version_compare(VERSION,'2.3','<'))
            $data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');
        else
            $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');

		$data['countries'] = array();
		
		$data['countries'][] = array(
			'name' => $this->language->get('text_sweden'),
			'code' => 'SWE'
		);

		
        if (isset($this->request->post['billmate_fee'])) {
            $data['billmate_fee'] = $this->request->post['billmate_fee'];
        } else {
            $data['billmate_fee'] = $this->config->get('billmate_fee');
        }
             
        $this->load->model('localisation/tax_class');   
		
        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

        if(version_compare(VERSION,'2.0.0','>=')){

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('total/two/billmate_fee.tpl', $data));
        } else {
            $this->data = $data;
            $this->template = 'total/billmate_fee.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );

            $this->response->setOutput($this->render());
        }
    }

    private function validate() {
        if(version_compare(VERSION,'2.3','<')) {
            if (!$this->user->hasPermission('modify', 'total/billmate_fee')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
        } else {
            if (!$this->user->hasPermission('modify', 'extension/total/billmate_fee')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>