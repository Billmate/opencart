<script type="text/javascript" src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/js/billmatepopup.js"></script>
<link rel="stylesheet" href="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/css/billmatepopup.css"/>
<?php if (!empty($error_warning)) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div style="margin-bottom: 10px;"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/bm_faktura_l.png" /></div>
<div id="payment">
  <div style="margin-bottom: 3px;"><b><?php echo $text_additional; ?></b></div>
  <div class="content">
	  <form id="payment" class="form-horizontal">
	  <div class="form-group required trBillmateInvoicePno">
		  <label for="pno" class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_pno; ?>"><?php echo $entry_pno; ?></span></label>
		<div class="col-sm-3">
			<input type="text" name="pno" id="pno" class="form-control" value="<?php echo ($billmate_pno) ? $billmate_pno : ''; ?>" />
		</div>

	  </div>
		<div class="form-group required">
			<label for="confirm_verify_email" class="col-sm-12 control-label"><input type="checkbox" checked="checked" name="confirm_verify_email" value="on" /><?php echo $entry_phone_no; ?></label>

		</div>

	  </form>
  </div>
</div>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
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
			
			jQuery('#payment').before('<div class="attention"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			jQuery('#button-confirm').attr('disabled', false);
			jQuery('.attention').remove();
		},		
		success: function(json) {	
			
			if (json['error']) {
				jQuery('#pno').before('<div class="alert alert-danger error" role="alert">' + json['error'] + '</div>');
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
