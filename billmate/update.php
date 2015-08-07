<?php
/**
 * Created by PhpStorm.
 * User: jesper
 * Date: 2015-02-10
 * Time: 13:31
 */
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = 'SE' ORDER BY name ASC");
$country = $query->row;
//$this->load->model('setting/setting');
$billmatebank = $this->model_setting_setting->getSetting('billmate_bankpay');
$billmatecard = $this->model_setting_setting->getSetting('billmate_cardpay');
$billmateinvoice = $this->model_setting_setting->getSetting('billmate_invoice');
$billmatepart = $this->model_setting_setting->getSetting('billmate_partpayment');

$billmatebank['billmate_bankpay_version'] = PLUGIN_VERSION;
$billmatecard['billmate_cardpay_version'] = PLUGIN_VERSION;
$billmateinvoice['billmate_invoice_version'] = PLUGIN_VERSION;
$billmatepart['billmate_partpay_version'] = PLUGIN_VERSION;

// Default Countries
$billmatebank['billmate_bankpay_country'] = array($country['country_id'] => array('name' => $country['name']));
$billmatecard['billmate_cardpay_country'] = array(0 => array('name' => 'All countries'));
$billmateinvoice['billmate_invoice_country'] = array($country['country_id'] => array('name' => $country['name']));
$billmatepart['billmate_partpay_country'] = array($country['country_id'] => array('name' => $country['name']));
$this->model_setting_setting->editSetting('billmate_bankpay',$billmatebank);
$this->model_setting_setting->editSetting('billmate_cardpay',$billmatecard);
$this->model_setting_setting->editSetting('billmate_invoice',$billmateinvoice);
$this->model_setting_setting->editSetting('billmate_partpayment',$billmatepart);