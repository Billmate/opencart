<script type="text/javascript" src="<?php echo (defined('HTTPS_SERVER')?HTTPS_SERVER : HTTP_SERVER); ?>/billmate/js/billmatepopup.js"></script>
<link rel="stylesheet" href="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/css/billmatepopup.css"/>
<?php if (!empty($error_warning)) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div style="margin-bottom: 10px;"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/bm_faktura_l.png" /></div>
<div id="payment">
  <div style="margin-bottom: 3px;"><b><?php echo $text_additional; ?></b></div>
  <div class="content">
    <table class="form">
	  <tr class="trBillmateInvoicePno">
        <td><?php echo $entry_pno; ?><br /><span class="help"><?php echo $help_pno; ?></span></td>
        <td><input type="text" name="pno" autocomplete="off" value="" /></td>
      </tr>
      <tr>
        <td><input type="checkbox" checked="checked" name="confirm_verify_email" value="on" /></td>
        <td><?php echo $entry_phone_no; ?></td>
      </tr>
    </table>	
  </div>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript">
	<!--
jQuery('#button-confirm').bind('click', function() {
	ajax_load('');
});
var billmatewindowtitle = '<?php echo $wrong_person_number; ?>';
function ajax_load(udata)
{
	jQuery.ajax({
		url: 'index.php?route=payment/billmate_invoice/send'+udata,
		type: 'post',
		data: jQuery('#payment input[type=\'text\'], #payment input[type=\'checkbox\']:checked, #payment input[type=\'radio\']:checked, #payment select'),
		dataType: 'json',		
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
			
			if (json['error']) {
				jQuery('#payment').before('<div class="warning">' + json['error'] + '</div>');
				jQuery('#button-confirm').attr('disabled', false);
			}
			if(json['address'])
			{ 
				$title = billmatewindowtitle;
				$height = 350;
				if(typeof json['title'] != 'undefined'){
					$title = json['title'];
				}
				if(typeof json['height'] != 'undefined'){
					$height = json['height'];
				}
				
				ShowMessage(json['address'],$title);
//				modalWin.ShowMessage(json['address'],350,500,billmatewindowtitle);
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
}
//--></script>
