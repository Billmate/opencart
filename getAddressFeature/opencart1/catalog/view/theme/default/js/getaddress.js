/**
 * JS file containing logic for getaddress from Billmate
 * Author: Jesper Johansson jesper@boxedlogistics.se
 */
$(function(){
	if($.isFunction($.fn.uniform)){
		$(" .styled, input:radio.styled").uniform().removeClass('styled');
	}
	if($.isFunction($.fn.colorbox)){
		$('.colorbox').colorbox({
			width: 640,
			height: 480
		});
	}
	if($.isFunction($.fn.fancybox)){
		$('.fancybox').fancybox({
			width: 640,
			height: 480
		});
	}
});
$(document).ready(function(){
    					
    					$('#fetch_address').live('click',function(e){
    						
    						e.preventDefault();
    						var pno = $('#payment_address_pno').val();
    						if(pno != ''){
    							
    							$.ajax({
    								url: 'index.php?route=payment/billmate_invoice/getaddress',
    								data: {pno: pno },
    								type: 'POST',
    								success: function(response){
    									var result = JSON.parse(response);
    									
    									if(result.success){

                                            /* Prefix for checkouts */
                                            var checkoutInputAddressPrefix = "";

                                            /* Ajax Quick Checkout by Dreamvention */
                                            if($(document).find('input[name="payment_address.firstname"]').length) {
                                                checkoutInputAddressPrefix = "payment_address.";
                                            }

    										if(result.data.firstname != ''){
                                                $('input[name="' + checkoutInputAddressPrefix + 'firstname"]').val(result.data.firstname);
                                                $('input[name="' + checkoutInputAddressPrefix + 'lastname"]').val(result.data.lastname);
    										} else {
                                                $('input[name="' + checkoutInputAddressPrefix + 'company"]').val(result.data.lastname);
                                                $('input[name="' + checkoutInputAddressPrefix + 'firstname"]').val('');
                                                $('input[name="' + checkoutInputAddressPrefix + 'lastname"]').val('');
    										}
                                                $('input[name="' + checkoutInputAddressPrefix + 'address_1"]').val(result.data.street);
                                                $('input[name="' + checkoutInputAddressPrefix + 'postcode"]').val(result.data.zip);
                                                $('input[name="' + checkoutInputAddressPrefix + 'city"]').val(result.data.city);
                                                $('input[name="' + checkoutInputAddressPrefix + 'email"]').val(result.data.email);
                                                $('input[name="' + checkoutInputAddressPrefix + 'telephone"]').val(result.data.phone);
                                                $('select[name="' + checkoutInputAddressPrefix + 'country_id"]').val(result.data.country_id);

                                            if(checkoutInputAddressPrefix == "payment_address.") {
                                                checkoutInputFieldNames = ["firstname", "lastname", "company", "lastname", "address_1", "postcode", "city", "email", "telephone", "country_id"];
                                                jQuery.each(checkoutInputAddressPrefix, function(checkoutInputFieldName) {
                                                    $('select[name="' + checkoutInputAddressPrefix + checkoutInputFieldName + ']').change();
                                                });
                                            }


                                            if($('input[name="' + checkoutInputAddressPrefix + 'pno"]').length > 0){
                                                $('input[name="' + checkoutInputAddressPrefix + 'pno"]').val(pno);
    										}
    										if($('span.pno_error').length > 0){
    											$('span.pno_error').remove();
    										}
    									} else {
                                            $('input[name="' + checkoutInputAddressPrefix + 'company"]').val('');
                                            $('input[name="' + checkoutInputAddressPrefix + 'firstname"]').val('');
                                            $('input[name="' + checkoutInputAddressPrefix + 'lastname"]').val('');
                                            $('input[name="' + checkoutInputAddressPrefix + 'address_1"]').val('');
                                            $('input[name="' + checkoutInputAddressPrefix + 'postcode"]').val('');
                                            $('input[name="' + checkoutInputAddressPrefix + 'city"]').val('');

                                            if(checkoutInputAddressPrefix == "payment_address.") {
                                                checkoutInputFieldNames = ["company", "firstname", "lastname", "address_1", "postcode", "city"];
                                                jQuery.each(checkoutInputAddressPrefix, function(checkoutInputFieldName) {
                                                    $('select[name="' + checkoutInputAddressPrefix + checkoutInputFieldName + ']').change();
                                                });
                                            }

    										var html = $('div#pno_input').html();
    										var message = '<span class="pno_error">'+result.message+'</span>';
    										$('div#pno_input').html(message+html);
    									}
    								}
    							})
    						}
    						e.stopPropagation();
    					})
    				});