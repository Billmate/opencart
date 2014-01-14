<?php
// Heading
$_['heading_title']      = 'Billmate Bank';
$_['text_billmate_bankpay'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/billmate_bank_s.png" alt="Billmate" title="BillMate" height="35px" />';

// Text
$_['text_payment']       = 'Betalning';
$_['text_success']       = 'Sparat: Du har modifierat Billmate Bank betalningsmodul!';

// Entry
$_['entry_merchant_id']     = 'Billmate ID';
$_['entry_secret']     = 'Billmate Nyckel:';

$_['entry_test']         = 'Testläge:';
$_['entry_prompt_name']  = 'Visa namn i betalfönster:';
$_['entry_3dsecure']     = 'Aktivera 3D secure:';
$_['entry_total']        = 'Totalbelopp:<br /><span class="help">Kundvagnens totalbelopp måste vara över detta för att aktivera betalningsmetoden.</span>';
$_['entry_order_status'] = 'Orderstatus:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sorteringsordning:';
$_['entry_description']     = 'Beskrivning:';

// Error
$_['error_permission']   = 'Varning: Du har inte access till att ändra Billmate kort inställningar!';
$_['error_merchant_id']     = 'Billmate ID saknas';
$_['error_secret']     = 'Billmate nyckel saknas';


$_['entry_transaction_method'] = 'Betalningsmetod';
$_['entry_billmate_bankpay_authorization'] = 'Reservera belopp';
$_['entry_billmate_bankpay_sale'] = 'Debitera belopp direkt';
