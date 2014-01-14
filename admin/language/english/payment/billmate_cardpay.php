<?php
// Heading

$_['heading_title']      = 'BillMate Card';
$_['heading_cardpay'] = 'BillMate Card';
$_['text_billmate_cardpay'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_kort_l.png" alt="Billmate" title="BillMate" height="35px" />';

// Text
$_['text_payment']       = 'Payment';
$_['text_success']       = 'Success: You have modified BillMate Card payment module!';

// Entry
$_['entry_merchant_id']     = 'Billmate ID:';
$_['entry_secret']     = 'Billmate Key:';
$_['entry_description']     = 'Description:';

$_['entry_test']         = 'Test Mode:';
$_['entry_prompt_name']  = 'Display Name:';
$_['entry_3dsecure']     = 'Enable 3D Secure:';
$_['entry_total']        = 'Total:<br><span class="help">The checkout total the order must reach before this payment method becomes active.</span>';
$_['entry_order_status'] = 'Order Status:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sort Order:';

// Error
$_['error_permission']   = 'Warning: You do not have permission to modify payment BillMate Card!';
$_['error_merchant_id']     = 'Billmate ID missing';
$_['error_secret']     = 'Billmate key missing';


$_['entry_transaction_method'] = 'Transaction Method';
$_['entry_billmate_cardpay_authorization'] = 'Authorization';
$_['entry_billmate_cardpay_sale'] = 'Sale';
