<form action="<?php echo $url; ?>" method="post" id="payment">
  	<input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>" />
  	<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
  	<input type="hidden" name="amount" value="<?php echo $amount; ?>" />
	<input type="hidden" name="currency" value="<?php echo $currency; ?>" />
	<input type="hidden" name="accept_url" value="<?php echo $accept_url; ?>" />
    <input type="hidden" name="language" value="<?php echo $language; ?>" />
	<input type="hidden" name="callback_url" value="<?php echo $callback_url; ?>" />
	<input type="hidden" name="capture_now" value="<?php echo $capture_now; ?>" />
	<input type="hidden" name="pay_method" value="<?php echo $pay_method; ?>" />
	<input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>" />
	<input type="hidden" name="return_method" value="<?php echo $request_method; ?>" />
	<input type="hidden" name="mac" value="<?php echo $mac; ?>" />
  <div class="buttons">
    <div class="right"><a onclick="jQuery('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
  </div>
</form>
