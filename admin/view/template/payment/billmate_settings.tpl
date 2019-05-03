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
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title.' - '.$billmate_version; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
            <tr>
                <td><?php echo $entry_callback_events; ?></td>
                <td><?php if ($billmate_settings_callback_events) { ?>
                        <input type="radio" name="billmate_settings_callback_events" value="1" checked="checked" />
                        <?php echo $text_yes; ?>
                        <input type="radio" name="billmate_settings_callback_events" value="0" />
                        <?php echo $text_no; ?>
                    <?php } else { ?>
                        <input type="radio" name="billmate_settings_callback_events" value="1" />
                        <?php echo $text_yes; ?>
                        <input type="radio" name="billmate_settings_callback_events" value="0" checked="checked" />
                        <?php echo $text_no; ?>
                    <?php } ?>
                </td>
            </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
