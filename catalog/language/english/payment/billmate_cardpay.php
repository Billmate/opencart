<?php
// Text
$_['text_title_name'] = '<img src="'.(defined('HTTPS_SERVER') ? dirname(HTTPS_SERVER) : dirname(HTTP_SERVER)).'/billmate/images/bm_kort_l.png" alt="Billmate card"> %s.';
$_['text_title'] = 'Billmate Card';
$_['text_wait'] = 'Redirecting to Payment gateway';
$_['text_unable']         = 'Unable to locate or update your order status';
$_['text_declined']       = 'Payment was declined by Billmate Card';
$_['text_failed']         = 'Billmate Card Transaction Failed';
$_['text_com']		= 'Billmate Card Communication Error';	
$_['text_error_msg']	= '<p>Unfortunately there was an error processing your payment.</p><p><b>Warning: </b>%s</p>';
$_['tax_discount'] = '% tax';
$_['billmate_card_failed'] = 'Unfortunately your card payment was not processed with the provided card details. Please try again or choose another payment method.';
$_['billmate_card_cancelled'] = 'The card payment has been canceled before it was processed. Please try again or choose a different payment method.';