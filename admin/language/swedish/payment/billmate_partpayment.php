<?php
$_['heading_title'] = 'Billmate Delbetalning';
$_['heading_partpayment'] = 'Billmate Delbetalning';
$_['text_billmate_partpayment'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_delbetalning_l.png" alt="Billmate" title="Billmate" height="35px" />';


// Text
$_['text_payment']          = 'Betalning';
$_['tab_log']          = 'Logs';
$_['text_success']          = 'Sparat: Du har modifierat Billmate Delbetalning';
$_['text_billmate_invoice']   = '';
$_['text_live']             = 'Live';
$_['text_beta']             = 'Test';
$_['text_sweden']           = 'Sverige';
$_['text_norway']           = 'Norge';
$_['text_finland']          = 'Finland';
$_['text_denmark']          = 'Danmark';
$_['entry_description']     = 'Beskrivning:';

// Entry
$_['entry_merchant']        = 'Billmate ID:<br /><span class="help">Affärens ID för att använda Billmates tjänster (fås av Billmate).</span>';
$_['entry_secret']          = 'Billmate Nyckel:<br /><span class="help">Hemlig nyckel för att använda Billmates tjänster (fås av Billmate).</span>';
$_['entry_server']          = 'Server:';
$_['entry_mintotal']           = 'Minimum Total:<br /><span class="help">Kundvagnens totalbelopp måste vara minst detta belopp för att aktivera betalningsmetoden.</span>';
$_['entry_maxtotal']           = 'Maximum Total:<br /><span class="help">Kundvagnens totalbelopp måste vara max detta belopp för att aktivera betalningsmetoden.</span>';
$_['entry_pending_status']  = 'Pending Status:';
$_['entry_accepted_status'] = 'Godkänd Status:';
$_['entry_geo_zone']        = 'Geo Zone:';
$_['entry_status']          = 'Status:';
$_['entry_sort_order']      = 'Sorteringsordning:';
$_['entry_invoice_fee']      = 'Fakturaavgift:';
$_['entry_invoice_fee_tax']      = 'Fakturaavgift Momsklass:';

// Error
$_['error_permission']      = 'Varning: Du har inte access till att ändra Billmate inställningar!';
$_['regen_pclasses'] = 'Save & Regen Pclasses';
$_['text_pclasses_updated'] = 'Successful update: fetched {count} PClasses from Billmate.';
$_['text_pclasses_updated_link'] = 'Would you like to view them?';

