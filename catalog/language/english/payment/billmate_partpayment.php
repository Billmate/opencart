<?php
// Text
$_['text_title']           = 'Billmate Part Payment';
$_['text_pay_month']  =
$_['text_no_fee']     = '<img src="' . (defined('HTTPS_SERVER') ? HTTPS_SERVER : HTTP_SERVER) . 'billmate/images /bm_delbetalning_l.png" alt="Billmate Part Payment"> Billmate Partpayment - %s (Pay from %s/month) <a id="terms-delbetalning">Terms of part pay</a><script type="text/javascript">$.getScript("https://billmate.se/billmate/base.js", function(){
		//effectiverate=(monthlyfee*numberofmonths-1)/100 %,
		$("#terms-delbetalning").Terms("villkor_delbetalning",{eid: "%s",effectiverate:34});
});</script>';
$_['text_no_fee2']     = '<img src="' . (defined('HTTPS_SERVER') ? HTTPS_SERVER : HTTP_SERVER) . 'billmate/images/bm_delbetalning_l.png" alt="Billmate Part Payment" > %s (Pay from %s/month) <a id="terms-delbetalning">Terms of part pay</a><script type="text/javascript">$.getScript("https://billmate.se/billmate/base.js", function(){
		//effectiverate=(monthlyfee*numberofmonths-1)/100 %,
		$("#terms-delbetalning").Terms("villkor_delbetalning",{eid: "%s",effectiverate:34});
});</script>';

$_['text_information']     = 'Billmate Part Payment Information';
$_['text_additional']      = 'Billmate Part Payment requires some additional information before they can proccess your order.';
$_['text_wait']            = 'Please wait!';
$_['text_male']            = 'Male';
$_['text_female']          = 'Female';
$_['text_year']            = 'Year';
$_['text_month']           = 'Month';
$_['text_day']             = 'Day';
$_['text_payment_option']  = 'Payment options';
$_['text_single_payment']  = 'Single Payment';
$_['text_monthly_payment'] = '%s months partpayment - %s per month';
$_['text_comment']         = "Billmate's Invoice ID: %s";
$_['your_billing_wrong']  = "Pay by invoice can be made only to the address listed in the National Register. Would you make the purchase with address:";
$_['correct_address_is']  = 'Correct Address is ';
$_['if_u_continue']       = 'Click Yes to continue with new address, No to choose other payment method';
$_['bill_yes']            = 'Yes, make purchase with this address';
$_['bill_no']             = 'No, I want to specify a different number or change payment method';
$_['else_click']          = 'else click';
$_['wrong_person_number'] = "You have entered a wrong number, please check if you accidentally mistype";
$_['requried_pno'] = 'Not valid organisation number/personal number. Please check the number.';
$_['requried_confirm_verify_email']='Please check the checkbox for confirm email is valid';

// Entry
$_['entry_gender']         = 'Gender:';
$_['entry_pno']            = 'Personal Identification Number/ Organisation number:';
$_['help_pno']  		   = 'Please enter your Social Security number here.';
$_['entry_dob']            = 'Date of Birth:';
$_['entry_phone_no']       = 'My email address %s is correct and can be used for invoicing purposes.';//Phone number:<br /><span class="help">Please enter your phone number.</span>';
$_['entry_street']         = 'Street:<br /><span class="help">Please note that delivery can only take place to the registered address when paying with Billmate.</span>';
$_['entry_house_no']       = 'House No.:<br /><span class="help">Please enter your house number.</span>';
$_['entry_house_ext']      = 'House Ext.:<br /><span class="help">Please submit your house extension here. E.g. A, B, C, Red, Blue ect.</span>';
$_['entry_company']        = 'Company Registration Number:<br /><span class="help">Please enter your Company\'s registration number</span>';

// Error
$_['error_deu_terms']      = 'You must agree to Billmate\'s privacy policy (Datenschutz)';
$_['error_address_match']  = 'Billing and Shipping addresses must match if you want to use Billmate Payments';
$_['error_network']        = 'Error occurred while connecting to Billmate. Please try again later.';
$_['payment_error'] = 'The payment with Billmate failed';
$_['tax_discount'] = '% tax';

