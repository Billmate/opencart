<?php if(version_compare(VERSION,'2.0.0','>=')): ?>
    <?php echo $header; ?><?php echo $column_left; ?>
<?php else: ?>
    <?php echo $header; ?>
<?php endif; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
    <?php if($latest_release != ''){ ?>
    <div class="warning"><?php echo $latest_release; ?></div>
    <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
    <?php if ($error_credentials) { ?>
    <div class="warning"><?php echo $error_credentials; ?></div>
    <?php } ?>
  <div class="box">
    <div class="<?php echo version_compare(VERSION,'2.0.0','>=') ? 'pull-right' : 'heading'; ?>">
        <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title.' - '.$billmate_version; ?></h1>

      <div class="buttons"><a onclick="$('#form').submit();" class="<?php echo version_compare(VERSION,'2.0.0','>=') ? 'btn btn-primary' : 'button'; ?>"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="<?php echo version_compare(VERSION,'2.0.0','>=') ? 'btn btn-default' : 'button'; ?>"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">

          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant_id; ?></td>
            <td><input type="text" name="billmate_bankpay_merchant_id" value="<?php echo $billmate_bankpay_merchant_id; ?>" />
              <?php if ($error_merchant_id) { ?>
              <span class="error"><?php echo $error_merchant_id; ?></span>
              <?php } ?></td>
          </tr>

          <tr>
            <td><span class="required">*</span> <?php echo $entry_secret; ?></td>
            <td><input type="text" name="billmate_bankpay_secret" value="<?php echo $billmate_bankpay_secret; ?>" />
              <?php if ($error_secret) { ?>
              <span class="error"><?php echo $error_secret; ?></span>
              <?php } ?></td>
          </tr>
		  
          <tr>
            <td valign="top"><?php echo $entry_description; ?></td>
            <td><textarea cols="84" rows="10" name="billmate_bankpay_description"><?php echo $billmate_bankpay_description; ?></textarea>
			</tr>

          <tr>
             <td><?php echo $entry_test; ?></td>
            <td><?php if ($billmate_bankpay_test) { ?>
              <input type="radio" name="billmate_bankpay_test" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="billmate_bankpay_test" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="billmate_bankpay_test" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="billmate_bankpay_test" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_total.'<br /><span class="help">'.$help_total; ?></span></td>
            <td><input type="text" name="billmate_bankpay_total" value="<?php echo $billmate_bankpay_total; ?>" /></td>
          </tr>        
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="billmate_bankpay_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $billmate_bankpay_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
            <tr>
                <td><?php echo $entry_available_countries; ?></td>
                <td><input type="text" name="billmatebank-country" value="" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><div id="billmatebank-country" class="scrollbox">
                        <?php $class = 'odd'; ?>
                        <?php if(isset($billmate_country) && is_array($billmate_country)){ ?>
                        <?php foreach ($billmate_country as $key => $b_country) { ?>
                        <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                        <div id="billmate-country<?php echo $key; ?>" class="<?php echo $class; ?>"><?php echo $b_country['name']; ?><img src="view/image/delete.png" alt="" />
                            <input type="hidden" name="billmatebank-country[<?php echo $key;?>][name];?>" value="<?php echo $b_country['name']; ?>" />

                        </div>
                        <?php } ?>
                        <?php } ?>
                    </div></td>
            </tr>
            <script type="text/javascript">
                var token = '<?php echo $token; ?>';
            </script>
            <?php if(version_compare(VERSION,'2.0.0','>=')): ?>

                <script src="/billmate/js/billmate.js"></script>
            <?php else: ?>
                <script src="/billmate/js/legacy-billmate.js"></script>
            <?php endif; ?>
            <tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="billmate_bankpay_status">
                <?php if ($billmate_bankpay_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="billmate_bankpay_sort_order" value="<?php echo $billmate_bankpay_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
