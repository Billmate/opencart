<div style="margin-bottom: 10px;"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/billmate_bank_s.png" /></div>
<div id="payment">

</div>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
    </div>
</div>
<script type="text/javascript">
    jQuery('#button-confirm').bind('click',function(){
        $.ajax({
            url: 'index.php?route=payment/billmate_bankpay/sendinvoice',
            beforeSend: function() {
                jQuery('#button-confirm').attr('disabled', true);

                jQuery('.warning, .error').remove();

                jQuery('#payment').before('<div class="attention"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
            },
            complete: function() {
                jQuery('#button-confirm').attr('disabled', false);
                jQuery('.attention').remove();
            },
            success: function(json) {
                var result = JSON.parse(json);
                if (!result.success) {
                    jQuery('#payment').before('<div class="warning">' + result.message + '</div>');
                }


                if (result.success) {
                    location = result.url;
                }
            }
        })
    })
</script>

