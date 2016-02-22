<div style="margin-bottom: 10px;"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/bm_kort_l.png" /></div>
<div id="payment">

</div>
<div class="buttons">
    <div class="right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
    </div>
</div>
<script type="text/javascript">
    jQuery('#button-confirm').bind('click',function(){
        $.ajax({
            url: 'index.php?route=payment/billmate_cardpay/sendinvoice',
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
                    jQuery('#button-confirm').attr('disabled', false);
                }


                if (result.success) {
                    location = result.url;
                }
            }
        })
    })
</script>
