<?php

// Text
$_['text_title']          = 'Billmate Invoice';
$_['text_title_fee']      = 'Billmate Invoice - Pay within 14 days';
$_['text_title_fee2']      = 'Pay within 14 days';
$_['text_no_fee']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" style="float: left;margin: 2px 6px;" alt="Billmate Invoice">Billmate Invoice <a id="terms">Terms of invoice</a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_fee']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" style="float: left;margin: 2px 6px;" alt="Billmate Invoice">%s (%s handling fee is added to your order) <a id="terms">Terms of invoice</a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee: "%s"});
});</script>';
$_['text_no_fee2']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" style="float: left;margin: 2px 6px;" alt="Billmate Invoice">%s<a id="terms">Terms of invoice</a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee:0});
});</script>';
$_['text_fee2']         = '<img src="'.(defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER).'/billmate/images/bm_faktura_l.png" style="float: left;margin: 2px 6px;" alt="Billmate Invoice">%s (%s handling fee is added to your order) <a id="terms">Terms of invoice</a><script type="text/javascript">$.getScript("https://efinance.se/billmate/base.js", function(){
		$("#terms").Terms("villkor",{invoicefee: "%s"});
});</script>';//$_['text_no_fee']         = 'Billmate Invoice - Pay within 14 days <a class="billmate_terms" href="javascript://" targeturl="https://efinance.se/billmate/villkor.html" rel="superbox[iframe][700x700]">Terms of invoice</a><script type="text/javascript">$.getScript("/billmate/jquery-superbox/jquery.superbox-min.js");$(\'<link rel="stylesheet" type="text/css" href="/billmate/jquery-superbox/jquery.superbox.css" />\').appendTo("head");$.getScript("/billmate/js/openbox.js");</script>';
$_['address_should_match'] = 'Address Should be match';
$_['text_additional']     = 'Billmate Invoice requires some additional information before they can proccess your order.';
$_['text_wait']           = 'Please wait!';
$_['text_comment']        = "Billmate's Invoice ID: %s";
$_['your_billing_wrong']  = "Pay by invoice can be made only to the address listed in the National Register. Would you make the purchase with address:";
$_['correct_address_is']  = 'Correct Address is ';
$_['if_u_continue']       = 'Click Yes to continue with new address, No to choose other payment method';
$_['close_other_payment'] = '<i>Choose other payment method</i>';
$_['bill_yes']            = 'Yes, make purchase with this address';
$_['bill_no']             = 'No, I want to specify a different number or change payment method';
$_['else_click']          = 'else click';

$_['wrong_person_number'] = "You have entered a wrong number, please check if you accidentally mistype";
// Entry
$_['entry_gender']         = 'Gender:';
$_['entry_pno']            = 'Personal Number:<br /><span class="help">Please enter your Social Security number here.</span>';
$_['entry_dob']            = 'Date of Birth:';
$_['entry_phone_no']       = 'My email address %s is correct and can be used for invoicing purposes.';//Phone number:<br /><span class="help">Please enter your phone number.</span>';
$_['entry_reference']         = 'Reference:';

// Error
$_['error_deu_terms']     = 'You must agree to Billmate\'s privacy policy (Datenschutz)';
$_['error_address_match'] = 'Billing and Shipping addresses must match if you want to use Billmate Invoice';
$_['error_network']       = 'Error occurred while connecting to Billmate. Please try again later.';
$_['requried_pno'] = 'Not valid organisation number/personal number. Please check the number.';
$_['requried_confirm_verify_email']='Please check the checkbox for confirm email is valid';
$_['payment_error'] = 'The payment with Billmate failed';

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
