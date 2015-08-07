<?php
// Heading
$_['heading_title']      = 'Billmate Kort';
$_['text_billmate_cardpay'] = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_CATALOG).'/billmate/images/bm_kort_l.png" alt="Billmate" title="Billmate" height="35px" />';

// Text
$_['text_payment']       = 'Betalning';
$_['text_success']       = 'Sparat: Du har modifierat Billmate Kort betalningsmodul!';
$_['text_all_countries'] = 'Alla länder';

// Entry
$_['entry_merchant_id']     = 'Billmate ID';
$_['entry_merchant_help']   = 'Affärens ID för att använda Billmates tjänster (fås av Billmate).';
$_['entry_secret']     = 'Billmate Nyckel:';
$_['entry_secret_help']     = 'Hemlig nyckel för att använda Billmates tjänster (fås av Billmate).';
$_['latest_release']     = 'Det finns en nyare version av denna plugin';



$_['entry_test']         = 'Testläge:';
$_['entry_prompt_name']  = 'Visa namn i betalfönster:';
$_['entry_3dsecure']     = 'Aktivera 3D secure:';
$_['entry_total']        = 'Totalbelopp:';
$_['help_total']         = 'Kundvagnens totalbelopp måste vara över detta för att aktivera betalningsmetoden.';
$_['entry_order_status'] = 'Orderstatus:';
$_['entry_geo_zone']     = 'Geo Zone:';
$_['entry_status']       = 'Status:';
$_['entry_sort_order']   = 'Sorteringsordning:';
$_['entry_description']     = 'Beskrivning:';
$_['entry_order_cancel_status'] = 'Cancelled Order Status:';
$_['entry_available_countries'] = 'Tillgängliga länder (autocomplete)';

// Error
$_['error_permission']   = 'Varning: Du har inte access till att ändra Billmate kort inställningar!';
$_['error_merchant_id']     = 'Billmate ID saknas';
$_['error_secret']     = 'Billmate nyckel saknas';


$_['entry_transaction_method'] = 'Betalningsmetod';
$_['entry_billmate_cardpay_authorization'] = 'Reservera belopp';
$_['entry_billmate_cardpay_sale'] = 'Debitera belopp direkt';
