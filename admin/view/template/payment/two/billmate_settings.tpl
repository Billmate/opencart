<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-bankpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($error_credentials) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_credentials; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title.' - '.$billmate_version; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-bankpay" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-test-status"><?php echo $entry_callback_events; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_settings_callback_events" id="input-test-status" class="form-control">
                                <option value="1"
                                <?php if ($billmate_settings_callback_events) :?>
                                selected="selected"
                                <?php endif; ?>>
                                <?php echo $text_yes; ?>
                                </option>
                                <option value="0"
                                <?php if (!$billmate_settings_callback_events) :?>
                                selected="selected"
                                <?php endif; ?>>
                                <?php echo $text_no; ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <a href="https://billmate.se/plugins/manual/Installationsmanual_Opencart_Billmate.pdf" target="_blank">Installationsmanual Billmate Modul ( Manual Svenska )</a><br />
        <a href="https://billmate.se/plugins/manual/Installation_Manual_Opencart_Billmate.pdf" target="_blank">Installation Manual Billmate ( Manual English )</a>
    </div>
</div>
<script type="text/javascript">
    var token = '<?php echo $token; ?>';
</script>
<script src="../billmate/js/billmate.js"></script>
<?php echo $footer; ?>