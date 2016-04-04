<script type="text/javascript" src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/js/billmatepopup.js"></script>
<link rel="stylesheet" href="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/css/billmatepopup.css"/>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div style="margin-bottom: 10px;"><img src="<?php echo (defined('HTTP_IMAGE')?dirname(HTTP_IMAGE) : HTTP_SERVER); ?>/billmate/images/bm_delbetalning_l.png" /></div>
<div id="payment">
  <div style="margin-bottom: 3px;"><b><?php echo $text_payment_option; ?></b></div>
  <div class="content"> 
    <table class="radio">
      <?php foreach ($payment_options as $payment_option) { ?>
      <tr class="highlight">
        <td><?php if (!isset($code)) { ?>
          <?php $code = $payment_option['code']; ?>
          <input type="radio" name="code" value="<?php echo $payment_option['code']; ?>" id="plan-id<?php echo $payment_option['code']; ?>" checked="checked" />
          <?php } else { ?>
          <input type="radio" name="code" value="<?php echo $payment_option['code']; ?>" id="plan-id<?php echo $payment_option['code']; ?>" />
          <?php } ?></td>
        <td><label for="plan-id<?php echo $payment_option['code']; ?>"><?php echo $payment_option['title']; ?></label></td>
      </tr>
      <?php } ?>
    </table>
  </div>
  <div style="margin-bottom: 3px;"><b><?php echo $text_additional; ?></b></div>
  <div class="content">
    <table class="form">
      <?php if (!$company) { ?>
      <?php if ($iso_code_3 == 'DEU' || $iso_code_3 == 'NLD') { ?>
      <tr>
        <td><?php echo $entry_dob; ?></td>
        <td><select name="pno_day">
            <option value=""><?php echo $text_day; ?></option>
            <?php foreach ($days as $day) { ?>
            <option value="<?php echo $day['value']; ?>"><?php echo $day['text']; ?></option>
            <?php } ?>
          </select>
          <select name="pno_month">
            <option value=""><?php echo $text_month; ?></option>
            <?php foreach ($months as $month) { ?>
            <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
            <?php } ?>
          </select>
          <select name="pno_year">
            <option value=""><?php echo $text_year; ?></option>
            <?php foreach ($years as $year) { ?>
            <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
            <?php } ?>
          </select></td>
      </tr>
      <?php } else { ?>
      <tr>
        <td><?php echo $entry_pno; ?><br /><span class="help"><?php echo $help_pno; ?></span></td>
        <td><input type="text" name="pno" value="" /></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td><?php echo $entry_company; ?></td>
        <td><input type="text" name="pno" value="" /></td>
      </tr>
      <?php } ?>
      <?php if ($iso_code_3 == 'DEU' || $iso_code_3 == 'NLD') { ?>
      <tr>
        <td><?php echo $entry_gender; ?></td>
        <td><input type="radio" name="gender" value="1" id="male" />
          <label for="male"><?php echo $text_male; ?></label>
          <input type="radio" name="gender" value="0" id="female" />
          <label for="female"><?php echo $text_female; ?></label></td>
      </tr>
      <tr>
        <td><?php echo $entry_street; ?></td>
        <td><input type="text" name="street" value="<?php echo $street; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo $entry_house_no; ?></td>
        <td><input type="text" name="house_no" value="<?php echo $street_number; ?>" /></td>
      </tr>
      <?php } ?>
      <?php if ($iso_code_3 == 'NLD') { ?>
      <tr>
        <td><?php echo $entry_house_ext; ?></td>
        <td><input type="text" name="house_ext" value="<?php echo $street_extension; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td><input type="checkbox" checked="checked" name="confirm_verify_email" value="on" /></td>
        <td><?php echo $entry_phone_no; ?></td>
      </tr>
      <?php if ($iso_code_3 == 'DEU') { ?>
      <tr>
        <td colspan="2"><input type="checkbox" name="deu_terms" value="1" />
          Mit der Übermittlung der für die Abwicklung des Rechnungskaufes und einer Identitäts - und Bonitätsprüfung erforderlichen 
          Daten an Billmate bin ich einverstanden. Meine kann ich jederzeit mit Wirkung für die Zukunft widerrufen.</td>
      </tr>
      <?php } ?>
    </table>
  </div>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
jQuery('#button-confirm').bind('click', function() {
	ajax_load('');
});
var billmatewindowtitle = '<?php echo $wrong_person_number; ?>';
function ajax_load(udata)
{
	jQuery.ajax({
		url: 'index.php?route=payment/billmate_partpayment/send'+udata,
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
			}
			
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
}
//--></script>
