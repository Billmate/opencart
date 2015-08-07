<?php
// Heading
$_['heading_title']      = 'Billmate Bank';
$_['heading_bankpay'] = 'Billmate Bank';
$_['text_billmate_bankpay'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/billmate_bank_s.png" alt="Billmate" title="Billmate" height="35px" />';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified Billmate Bank payment module!';

// Entry
$_['entry_merchant_id']     = 'Billmate ID:';
$_['entry_merchant_help']   = '(estore id) to use for the Billmate service (provided by Billmate).';
$_['latest_release']     = 'There is a new version released for this plugin';

$_['entry_secret']     = 'Billmate Key:';
$_['entry_secret_help']     = 'Shared secret to use with the Billmate service (provided by Billmate).';

$_['entry_description']     = 'Description:';

$_['entry_test']         = 'Test Mode:';
$_['entry_prompt_name']  = 'Display Name:';
$_['entry_3dsecure']     = 'Enable 3D Secure:';
$_['entry_total']        = 'Total:';
$_['help_total'] = '<br><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_order_status'] = 'Order Status:';
$_['entry_order_cancel_status'] = 'Cancelled Order Status:';
$_['entry_available_countries'] = 'Available countries (autocomplete)';

$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify payment Billmate Bank!';
$_['error_merchant_id']     = 'Billmate ID missing';
$_['error_secret']     = 'Billmate key missing';


$_['entry_transaction_method'] = 'Transaction Method';
$_['entry_billmate_bankpay_authorization'] = 'Authorization';
$_['entry_billmate_bankpay_sale'] = 'Sale';
