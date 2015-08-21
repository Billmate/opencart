<?php
// Text
$_['text_title']          = 'Billmate faktura';
$_['text_title_fee']      = 'Billmate Faktura - Betala inom 14-dagar';
$_['text_title_fee2']      = 'Betala inom 14-dagar';
$_['text_fee']            = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" alt="Billmate faktura" style="float: left;margin: 2px 6px;">%s (%s Faktura avgift tillkommer på ordern) <a id="terms"> Köpvillkor </a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_no_fee']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" alt="Billmate faktura" style="float: left;margin: 2px 6px;">%s<a id="terms"> Köpvillkor </a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_fee2']            = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" alt="Billmate faktura" style="float: left;margin: 2px 6px;">%s (%s Faktura avgift tillkommer på ordern) <a id="terms"> Köpvillkor </a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_no_fee2']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" alt="Billmate faktura" style="float: left;margin: 2px 6px;">%s<a id="terms"> Köpvillkor </a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_additional']     = 'Billmate faktura behöver lite mer information för att processa din order.';
$_['text_wait']           = 'Var god vänta!';
$_['address_should_match'] = 'Adress ska matcha';
$_['text_comment']        = "Billmate faktura ID: %s";
$_['your_billing_wrong']  = "Köp mot faktura kan bara göras till den adress som är angiven i folkbokföringen. Vill du genomföra köpet med adressen:";
$_['correct_address_is']  = 'Din bokföringsadress: ';
$_['if_u_continue']       = 'Klicka Byt för att byta faktura och leveransadress. Klicka Avbryt för att välja en annan betalningsmetod.';
$_['bill_yes']            = 'Ja, genomför köp med denna adress';
$_['bill_no']             = 'Nej jag vill ange ett annat personnummer eller byta betalsätt';
$_['else_click']          = 'eller klicka';
$_['wrong_person_number'] = "Ej giltigt organisations-/personnummer. Kontrollera numret.";
// Entry
$_['Close'] 			  = 'Stäng';
$_['close_other_payment'] = '<i>Klicka på stäng för att välja en annan betalningsmetod.</i>';
$_['requried_pno'] = 'Ej giltigt organisations-/personnummer. Kontrollera numret.';
$_['entry_gender']         = 'Kön:';
$_['entry_pno']            = 'Organisations-/personnummer:';
$_['help_pno'] 			   = 'Vänligen skriv in ditt organisationsnummer (företag) eller personnummer (privat).';
$_['entry_dob']            = 'Date of Birth:';
$_['entry_phone_no']       = 'Min e-postadress %s är korrekt och får användas för fakturering.';//Mobilnummer:<br /><span class="help">Vänligen skriv in ditt mobilnummer.</span>';
$_['entry_reference']         = 'Referens:';

// Error
$_['error_deu_terms']     = 'You must agree to Billmate\'s privacy policy ';
$_['error_address_match'] = 'Faktura och leveransadress måste vara samma om du ska använda billmate faktura.';
$_['error_network']       = 'Ingen kontakt med billmate server. Försök igen senare.';
$_['required_pno']        = 'Personnummer krävs.';
$_['requried_confirm_verify_email']='Vänligen kryssa i rutan för att bekräfta att er e-postadress är giltig.';
$_['payment_error'] = 'Betalning med Billmate misslyckades';


// terms and conditions
$_['page_title'] = 'Köpvillkor';
$_['body_title'] = 'Köpvillkor';
$_['subtitle']   = 'Handla nu - betala först efter leverans!';
$_['short_description'] = '
        När du betalar via faktura administreras detta av eFinance Nordic AB under varumärket Billmate. Detta
innebär att du handlar tryggt och enkelt. Du slipper uppge dina kortuppgifter, och betalar först efter det
att du mottagit dina varor.';
$_['subline'] = 'Detta erbjuder vi dig:';
$_['li1'] = 'Få alltid hem varan innan du betalar';
$_['li2'] = '14 dagar betalningstid';
$_['li3'] = 'Du behöver aldrig lämna ut kortuppgifter';
$_['li4'] = 'Alltid 14 dagars ångerrätt i enlighet med distans- och hemförsäljningslagen*';
$_['li5'] = 'Tillgång till dina fakturor via Billmate online ';
$_['li6'] = 'Möjlighet till delbetalning';
$_['long_description'] = 'En aviavgift om xx kr per köp tillkommer. Vid försenad betalning tillkommer lagstadgad påminnelse-/
förseningsavgift samt dröjsmålsränta om 2 % per månad. Vid utebliven betalning överlämnas fakturan
till inkasso. För att kunna beställa mot faktura måste beställaren vara ett registrerat svenskt företag
eller en person över 18 år, vara folkbokförd i Sverige samt godkännas i den kreditprövning som
genomförs vid köpet. Kreditprövningen kan i vissa fall innebära att en kreditupplysning tas. I sådana
fall kommer ni bli meddelade om detta via e-post. Kreditupplysningen sköts via Bisnode och är inget
som belastar när man ansöker om kredit hos kreditinstitut.<br>
Personuppgifter hanteras i enlighet med gällande lagstiftning. eFinance behandlar personuppgifter i
syfte att utföra kundanalys, identifikation, kreditkoll samt marknadsföring. Personnummer kan används
som kundnummer i kundhanteringssyfte.';
$_['footer_one'] = '* Gäller ej för alla varor och tjänster, t ex. flygresor, evenemang och specialtillverkade varor.';
$_['footer_two'] = 'eFinance Nordic AB, organisationsnummer 556918-4129, telefonnummer 040-30 35 00.';
$_['close'] = 'Stäng';