<?php
/*echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG);
echo '<pre>';
print_r(get_defined_constants());
echo '</pre>';
*/
// Heading
$_['heading_title']         = 'Billmate Invoice';
$_['text_billmate_invoice'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_faktura_l.png" alt="Billmate Invoice" title="Billmate Invoice" height="35px" />';

// Text
$_['text_payment']          = 'Payment';
$_['tab_log']          = 'Logs';

$_['text_success']          = 'Success: You have modified Billmate Payment module!';
$_['text_billmate_fee']     = 'Billmate Fee';
$_['text_live']             = 'Live';
$_['text_beta']             = 'Test';
$_['text_sweden']           = 'Sweden';
$_['text_norway']           = 'Norway';
$_['text_finland']          = 'Finland';
$_['text_denmark']          = 'Denmark';
$_['entry_description']     = 'Description:';
$_['entry_test']         = 'Test Mode:';

// Entry
$_['entry_merchant']        = 'Billmate ID:<br /><span class="help">(estore id) to use for the Billmate service (provided by Billmate).</span>';
$_['entry_secret']          = 'Billmate Key:<br /><span class="help">Shared secret to use with the Billmate service (provided by Billmate).</span>';
$_['entry_server']          = 'Server:';
$_['entry_mintotal']           = 'Minimum Total:<br /><span class="help">The checkout total the order must reach minimum this payment method becomes active.</span>';
$_['entry_maxtotal']           = 'maximum Total:<br /><span class="help">The checkout total the order must reach maximum this payment method becomes active.</span>';
$_['entry_pending_status']  = 'Pending Status:';
$_['entry_accepted_status'] = 'Accepted Status:';
$_['entry_geo_zone']        = 'Geo Zone:';
$_['entry_status']          = 'Status:';
$_['entry_sort_order']      = 'Sort Order:';
$_['entry_invoice_fee']      = 'Invoice fee:';
$_['entry_invoice_fee_tax']      = 'Invoice fee tax class';
$_['entry_available_countries'] = 'Available countries (autocomplete)';

// Error
$_['error_permission']      = 'Warning: You do not have permission to modify payment Billmate Part Payment!';
?>
