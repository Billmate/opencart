<?php
/**
 * Created by PhpStorm.
 * User: jesper
 * Date: 2015-02-10
 * Time: 13:31
 */
$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE name = 'Sweden' ORDER BY name ASC");
$country = $query->row;
//$this->load->model('setting/setting');
$billmatebank = $this->model_setting_setting->getSetting('billmate_bankpay');
$billmatecard = $this->model_setting_setting->getSetting('billmate_cardpay');
$billmateinvoice = $this->model_setting_setting->getSetting('billmate_invoice');
$billmatepart = $this->model_setting_setting->getSetting('billmate_partpayment');

$billmatebank['version'] = PLUGIN_VERSION;
$billmatecard['version'] = PLUGIN_VERSION;
$billmateinvoice['version'] = PLUGIN_VERSION;
$billmatepart['version'] = PLUGIN_VERSION;

// Default Countries
$billmatebank['billmatebank-country'] = array($country['country_id'] => array('name' => $country['name']));
$billmatecard['billmatecard-country'] = array(0 => array('name' => 'All countries'));
$billmateinvoice['billmate-country'] = array($country['country_id'] => array('name' => $country['name']));
$billmatepart['billmatepart-country'] = array($country['country_id'] => array('name' => $country['name']));
$this->model_setting_setting->editSetting('billmate_bankpay',$billmatebank);
$this->model_setting_setting->editSetting('billmate_cardpay',$billmatecard);
$this->model_setting_setting->editSetting('billmate_invoice',$billmateinvoice);
$this->model_setting_setting->editSetting('billmate_partpayment',$billmatepart);