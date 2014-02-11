<?php

require_once(DIR_SYSTEM . 'library/billmate/OpenCartBillmate.php');

class ModelPaymentBillmate extends Model {
    private $moduleType;

    function addData(&$data, &$errors) {

        // Headings and general text
        $data['heading_title'] = $this->language->get('heading_' . $data['module_type']) . ' ' . OpenCartBillmate::ocGetModuleVersion();
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_update_pclasses'] = $this->language->get('button_save_and_update_pclasses');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        if(isset($this->request->post['perform_pclass_update']) &&
            $this->request->post['perform_pclass_update'] == 1)
        {
            $data['text_pclasses_not_updated'] = $this->language->get('text_pclasses_not_updated');
        } else {
            $data['text_pclasses_not_updated'] = '';
        }

        // Entries
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_status_explanation'] = $this->language->get('entry_order_status_explanation');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_countries'] = $this->language->get('entry_countries');
        $data['entry_countries_explanation'] = $this->language->get('entry_countries_explanation');
        $data['entry_eid'] = $this->language->get('entry_eid');
        $data['entry_secret'] = $this->language->get('entry_secret');
        $data['entry_invoice_fee'] = $this->language->get('entry_invoice_fee');
        $data['entry_agb_url'] = $this->language->get('entry_agb_url');
        $data['entry_server'] = $this->language->get('entry_server');
        $data['entry_monthly_cost'] = $this->language->get('entry_monthly_cost');
        $data['entry_shared_settings'] = $this->language->get('entry_shared_settings');
        $data['entry_shared_settings_explanation'] = $this->language->get('entry_shared_settings_explanation');
        $data['entry_shared_settings_invoice_fee_disclaimer'] = $this->language->get('entry_shared_settings_invoice_fee_disclaimer');
        $data['entry_update_check'] = $this->language->get('entry_update_check');
        $data['entry_update_check_explanation'] = $this->language->get('entry_update_check_explanation');
        $data['entry_pclasses'] = $this->language->get('entry_pclasses');
        $data['entry_pclasses_explanation'] = $this->language->get('entry_pclasses_explanation');

        // Countries
        $data['countries'] = array(
            array(
                'name' => $this->language->get('country_sweden'),
                'code' => 'se',
                'currency' => 'SEK'
            ),
            array(
                'name' => $this->language->get('country_norway'),
                'code' => 'no',
                'currency' => 'NOK'
            ),
            array(
                'name' => $this->language->get('country_denmark'),
                'code' => 'dk',
                'currency' => 'DKK'
            ),
            array(
                'name' => $this->language->get('country_finland'),
                'code' => 'fi',
                'currency' => 'EUR'
            ),
            array(
                'name' => $this->language->get('country_germany'),
                'code' => 'de',
                'currency' => 'EUR'
            ),
            array(
                'name' => $this->language->get('country_netherlands'),
                'code' => 'nl',
                'currency' => 'EUR'
            )
        );

        $data['invoice_fee_enabled'] = $this->config->get('billmate_invoice_fee_status');

        // Set values
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->setValue($data, 'billmate_order_status_id');
        $this->setValue($data, 'billmate_status');
        $this->setValue($data, 'billmate_sort_order');
        $this->setValue($data, 'billmate_server');
        $this->setValue($data, 'billmate_update_check');
        $this->setValue($data, 'billmate_shared_settings');
        $this->setValue($data, 'billmate_agb_url');
        $this->setValue($data, 'billmate_enabled_countries');
        if($data['billmate_enabled_countries'] == null)
            $data['billmate_enabled_countries'] = array();

        $data['pclasses'] = $this->getPClasses();
        $data['text_number_of_pclasses'] = str_replace(
            '{count}',
            count($data['pclasses']),
            $this->language->get('text_number_of_pclasses'));
        $data['text_click_to_display'] = $this->language->get('text_click_to_display');
        $data['pclass_country'] = $this->language->get('pclass_country');
        $data['pclass_eid'] = $this->language->get('pclass_eid');
        $data['pclass_id'] = $this->language->get('pclass_id');
        $data['pclass_description'] = $this->language->get('pclass_description');
        $data['pclass_interest_rate'] = $this->language->get('pclass_interest_rate');
        $data['pclass_minimum_amount'] = $this->language->get('pclass_minimum_amount');
        $data['pclass_invoice_fee'] = $this->language->get('pclass_invoice_fee');
        $data['pclass_starting_fee'] = $this->language->get('pclass_starting_fee');
        $data['pclass_expiry_date'] = $this->language->get('pclass_expiry_date');


        foreach($data['countries'] AS $country) {
            $this->setValue($data, 'billmate_eid_' . $country['code']);
            $this->setValue($data, 'billmate_secret_' . $country['code']);
            $this->setValue($data, 'billmate_invoice_fee_' . $country['code']);
        }

        // Set update message
        $data['text_update_available'] = '';
        if($data['billmate_update_check']) {
            $newVersion = false;
            if($newVersion !== false) {
                $updateString = $this->language->get('text_update_available');
                $updateString = str_replace('{current}', OpenCartBillmate::ocGetModuleVersion(), $updateString);
                $updateString = str_replace('{new}', $newVersion, $updateString);
                $data['text_update_available'] = $updateString;
            }
        }

        // Set breadcrumbs
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
            'text'      => $this->language->get('heading_' . $data['module_type']),
            'href'      => $this->url->link('payment/billmate_' . $data['module_type'], 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('payment/billmate_' . $data['module_type'], 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        // Take care of error messages
        $possibleErrors = array('warning', 'sort_order');
        foreach($data['countries'] AS $country) {
            $possibleErrors[] = 'eid_' . $country['code'];
            $possibleErrors[] = 'secret_' . $country['code'];
            $possibleErrors[] = 'invoice_fee_' . $country['code'];
        }

        foreach($possibleErrors AS $error) {
            if(isset($errors[$error]))
                $data['error_' . $error] = $errors[$error];
            else
                $data['error_' . $error] = '';
        }
    }

    public function addPClassViewData(&$data) {
        $data['heading_title'] = $this->language->get('heading_pclasses');
        $data['button_back'] = $this->language->get('button_back');
        $data['pclasses'] = $this->getPClasses();
        $data['pclass_country'] = $this->language->get('pclass_country');
        $data['pclass_eid'] = $this->language->get('pclass_eid');
        $data['pclass_id'] = $this->language->get('pclass_id');
        $data['pclass_description'] = $this->language->get('pclass_description');
        $data['pclass_interest_rate'] = $this->language->get('pclass_interest_rate');
        $data['pclass_minimum_amount'] = $this->language->get('pclass_minimum_amount');
        $data['pclass_invoice_fee'] = $this->language->get('pclass_invoice_fee');
        $data['pclass_starting_fee'] = $this->language->get('pclass_starting_fee');
        $data['pclass_expiry_date'] = $this->language->get('pclass_expiry_date');
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
            'text'      => $this->language->get('heading_pclasses'),
            'href'      => $this->url->link('payment/billmate_' . $data['module_type'] . '/pclasses', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $data['back'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
    }

    public function saveSharedSettings() {
        switch($this->request->post['billmate_module_type']) {
            case 'invoice':
                $otherModules = array('partpayment', 'specialcampaigns');
                $thisModule = 'invoice';
                break;
            case 'partpayment':
                $otherModules = array('invoice', 'specialcampaigns');
                $thisModule = 'partpayment';
                break;
            case 'specialcampaigns':
                $otherModules = array('partpayment', 'invoice');
                $thisModule = 'specialcampaigns';
                break;
        }

        $notShared = array('status', 'order_status_id', 'sort_order');

        foreach($otherModules AS $module) {
            $settings = $this->model_setting_setting->getSetting('billmate_' . $module);
            foreach($this->request->post AS $key => $value) {
                if(strpos($key, 'billmate_') !== false) {
                    $key = str_replace('billmate_' . $thisModule . '_', '', $key);
                    if(!in_array($key, $notShared)) {
                        $key = 'billmate_' . $module . '_' . $key;
                        $settings[$key] = $value;
                    }
                }
            }

            // Special solution for the updates checkbox
            if(
                !isset($this->request->post['billmate_' . $thisModule . '_update_check']) &&
                isset($settings['billmate_' . $module . '_update_check'])) {

                unset($settings['billmate_' . $module . '_update_check']);
            }

            $this->model_setting_setting->editSetting('billmate_' . $module, $settings);
        }
    }

    public function unshareSettings() {
        $modules = array('invoice', 'partpayment', 'specialcampaigns');

        foreach($modules AS $module) {
            $settings = $this->model_setting_setting->getSetting('billmate_' . $module);
            if(isset($settings['billmate_' . $module . '_enabled_countries'])) {
                $settings['billmate_' . $module . '_enabled_countries'] =
                    unserialize($settings['billmate_' . $module . '_enabled_countries']);
            }
            $settings['billmate_' . $module . '_shared_settings'] = '';
            $this->model_setting_setting->editSetting('billmate_' . $module, $settings);
        }
    }

    public function validate(&$errors) {
        $this->moduleType = $this->request->post['billmate_module_type'];

        if (!$this->user->hasPermission('modify', 'payment/billmate_' . $this->moduleType)) {
            $errors['warning'] = $this->language->get('error_permission');
        }

        if($this->getPostValue('sort_order') && !ctype_digit($this->getPostValue('sort_order'))) {
            $errors['sort_order'] = $this->language->get('error_sort_order');
        }

        $enabledCountries = $this->getPostValue('enabled_countries');
        if(is_array($enabledCountries)) {
            foreach(array_keys($enabledCountries) AS $country) {
                if(!$this->getPostValue('eid_' . $country)) {
                    $errors['eid_' . $country] = $this->language->get('error_no_eid');
                } elseif(intval($this->getPostValue('eid_' . $country)) <= 0) {
                    $errors['eid_' . $country] = $this->language->get('error_bad_eid');
                }
                if(!$this->getPostValue('secret_' . $country)) {
                    $errors['secret_' . $country] = $this->language->get('error_no_secret');
                }
                if($this->getPostValue('invoice_fee_' . $country) && !is_numeric($this->getPostValue('invoice_fee_' . $country))) {
                    $errors['invoice_fee_' . $country] = $this->language->get('error_invoice_fee');
                }
            }
        }

        if(empty($errors))
            return true;
        else
            return false;
    }

    public function getPClasses() {
        $billmate = new OpenCartBillmate();

        $allPClasses = array();
        $allPClassesRaw = $billmate->ocGetAllPClasses();

        foreach($allPClassesRaw AS $country => $EIDs) {
            if(is_array($EIDs)) {
                $billmateCountry = Billmate::getCountryForCode($country);
                $billmateLanguage = $billmate->getLanguageForCountry($billmateCountry);
                $language = $billmate->getLanguageCode($billmateLanguage);
                $billmateCurrency = $billmate->getCurrencyForCountry($billmateCountry);
                $currency = strtoupper($billmate->getCurrencyCode($billmateCurrency));
                $this->currency->set($currency);
                foreach($EIDs AS $eid => $PIDs) {
                    foreach($PIDs AS $pid => $pclass) {
                        $expiryDate = $pclass->getExpire();
                        if($expiryDate) {
                            $expiryDate = date('Y-m-d', $expiryDate);
                        } else {
                            $expiryDate = '-';
                        }
                        $allPClasses[] = array(
                            'flag' => HTTP_SERVER .
                                '../system/library/billmate/checkout/flags/' .
                                $language .
                                '.png',
                            'eid' => $eid,
                            'country' => $country,
                            'pid' => $pid,
                            'desc' => $pclass->getDescription(),
                            'interest_rate' => $pclass->getInterestRate() . '%',
                            'minimum_amount' => $this->currency->format($pclass->getMinAmount(), $currency, 1),
                            'invoice_fee' => $this->currency->format($pclass->getInvoiceFee(), $currency, 1),
                            'starting_fee' => $this->currency->format($pclass->getStartFee(), $currency, 1),
                            'expiry_date' => $expiryDate
                        );
                    }
                }
            }
        }
        return $allPClasses;
    }

    public function updatePClasses() {
        $billmate = new OpenCartBillmate();

       // $billmate->ocClearPClasses();

        // Loop through all enabled countries and find unique country/eid/server combinations
        $modules = array('partpayment', 'specialcampaigns');
        $countryData = array();
        $data = array();
        foreach($modules AS $module) {
            // Check that module is enabled
           // if(!$this->config->get('billmate_' . $module . '_status')) {
            //    continue;
           // }
            //$settings = $this->model_setting_setting->getSetting('billmate_' . $module);
			$settings = $_POST;
            /*if(!isset($settings['billmate_' . $module . '_enabled_countries'])) {
                continue;
            }
            $enabledCountries = $settings['billmate_' . $module . '_enabled_countries'];

            // Starting with OpenCart v1.5.1.3 the variable may already be serialized
            if(!is_array($enabledCountries)) {
                $enabledCountries = unserialize($enabledCountries);
            }

            if(!array($enabledCountries)) {
                continue;
            }*/
            
            foreach( array_keys($settings['billmate_partpayment']) AS $country) {
                if( $settings['billmate_partpayment'][$country]['status'] != 1 ) continue;

                $eid = $settings['billmate_partpayment'][$country]['merchant'];
                $secret = $settings['billmate_partpayment'][$country]['secret'];
                $server = $settings['billmate_partpayment'][$country]['server'];
                
                if( empty( $eid ) || empty( $secret ) ) continue;
                
                if(isset($countryData[$country]) && is_array($countryData[$country])) {
                    // Since there are only two modules there's no need to iterate over
                    // the country. We'll go straight for index 0.
                    if(
                        $countryData[$country][0]['eid'] == $eid &&
                        $countryData[$country][0]['server'] == $server)
                    {
                        continue;
                    }
                    $countryData[$country][] = array(
                        'eid' => $eid,
                        'secret' => $secret,
                        'server' => $server);
                } else {
                    $countryData[$country] = array(array(
                        'eid' => $eid,
                        'secret' => $secret,
                        'server' => $server));
                }
            }
        }

        // Iterate over found country/eid/server combinations and fetch the pclasses
        $numFoundPClasses = 0;
        
        foreach($countryData AS $countryCode => $countryConfigurations) {

            foreach($countryConfigurations AS $config) {
                try {
                    $billmate->ocFetchPClasses(
                        $countryCode,
                        $config['eid'],
                        $config['secret'],
                        $config['server']);
                } catch(Exception $e) {
                    continue;
                }
                $tmppclasses = $billmate->getPClasses();
                $numFoundPClasses += count($tmppclasses);
            }
            if( empty($data[$countryCode])){
                $data[$countryCode] = array();
            }
            
            $data[$countryCode][] = $tmppclasses;

        }
		
        $this->model_setting_setting->editSetting( 'billmate_partpayment_country', $data );
        return $numFoundPClasses;
    }

    private function getPostValue($key) {
        $key = 'billmate_' . $this->moduleType . '_' . $key;
        if(isset($this->request->post[$key]))
            return $this->request->post[$key];
        else
            return null;
    }

    private function setValue(&$data, $option) {
        $moduleOption = str_replace('billmate_', 'billmate_' . $data['module_type'] . '_', $option);
        if(isset($this->request->post[$moduleOption])) {
            $data[$option] = $this->request->post[$moduleOption];
        } else {
            $data[$option] = $this->config->get($moduleOption);
        }
    }

}
