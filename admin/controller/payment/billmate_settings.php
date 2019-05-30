<?php
require_once dirname(DIR_APPLICATION).DIRECTORY_SEPARATOR.'billmate'.DIRECTORY_SEPARATOR.'commonfunctions.php';

class ControllerPaymentBillmateSettings extends Controller {

    /**
     * @var array
     */
	private $error = array();

    /**
     * ControllerPaymentBillmateCardpay constructor.
     *
     * @param $registry
     */
	public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('payment/bmsetup');
        $this->load->model('setting/setting');
    }

    public function index()
    {
		$this->load->language('payment/billmate_settings');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('billmate_settings', $this->request->post);
            if ($this->request->post['billmate_settings_callback_events']) {
                $this->getBmSetupModel()->registerEvents();
            } else {
                $this->getBmSetupModel()->unregisterEvents();
            }

			$this->session->data['success'] = $this->language->get('text_success');
			if(version_compare(VERSION,'2.0.0','>=')) {
                if (version_compare(VERSION, '2.3', '<')) {
                    $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
                } else {
                    $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
                }
            } else {
                $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
            }
		}

		$templateData = $this->getTemplateData();

        if (version_compare(VERSION,'2.0.0','>=')) {
            $templateData['header'] = $this->load->controller('common/header');
            $templateData['column_left'] = $this->load->controller('common/column_left');
            $templateData['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput(
                $this->load->view(
                    'payment/two/billmate_settings.tpl',
                    $templateData
                )
            );
            return;
        }

        $this->data = $templateData;
        $this->template = 'payment/billmate_settings.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());

	}

    /**
     * @return bool
     */
    private function validate()
    {
        if(version_compare(VERSION,'2.3','<')) {
            if (!$this->user->hasPermission('modify', 'payment/billmate_settings')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
        } else {
            if (!$this->user->hasPermission('modify', 'extension/payment/billmate_settings')) {
                $this->error['warning'] = $this->language->get('error_permission');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getTemplateData()
    {
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['entry_callback_events'] = $this->language->get('entry_callback_events');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['billmate_version'] = PLUGIN_VERSION;

        $data['tab_general'] = $this->language->get('tab_general');

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
            'href'      => $this->url->link('payment/billmate_settings', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/billmate_settings', 'token=' . $this->session->data['token'], 'SSL');


        if (isset($this->request->post['billmate_settings_callback_events'])) {
            $data['billmate_settings_callback_events'] = $this->request->post['billmate_settings_callback_events'];
        } else {
            $data['billmate_settings_callback_events'] = $this->config->get('billmate_settings_callback_events');
        }
        return $data;
    }

    /**
     * @return ModelPaymentBmsetup
     */
    protected function getBmSetupModel()
    {
        return $this->model_payment_bmsetup;
    }
}
