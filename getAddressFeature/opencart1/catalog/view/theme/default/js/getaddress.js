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
    										if(result.data.firstname != ''){
    										$('input[name="firstname"]').val(result.data.firstname);
    										$('input[name="lastname"]').val(result.data.lastname);
    										} else {
    											$('input[name="company"]').val(result.data.lastname);
    											$('input[name="firstname"]').val('');
        										$('input[name="lastname"]').val('');
    										}
    										$('input[name="address_1"]').val(result.data.street);
    										$('input[name="postcode"]').val(result.data.zip);
    										$('input[name="city"]').val(result.data.city);
    										$('input[name="email"]').val(result.data.email);
    										$('input[name="telephone"]').val(result.data.phone);


    										if($('input[name="pno"]').length > 0){
    											$('input[name="pno"]').val(pno);
    										}
    										if($('span.pno_error').length > 0){
    											$('span.pno_error').remove();
    										}
    									} else {
    										$('input[name="company"]').val('');
    										$('input[name="firstname"]').val('');
    										$('input[name="lastname"]').val('');
    										$('input[name="address_1"]').val('');
    										$('input[name="postcode"]').val('');
    										$('input[name="city"]').val('');
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