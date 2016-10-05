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
        <?php if($latest_release != ''){ ?>
        <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $latest_release; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
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
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-log" data-toggle="tab"><?php echo $tab_log ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                            <ul class="nav nav-tabs" id="country">
                                <?php foreach ($countries as $country) { ?>
                                <li><a href="#tab-<?php echo $country['code']; ?>" data-toggle="tab"><?php echo $country['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <?php foreach ($countries as $country) { ?>
                                    <div class="tab-pane" id="tab-<?php echo $country['code']; ?>">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                                            <div class="col-sm-10">
                                                <select name="billmate_invoice[<?php echo $country['code']; ?>][status]" id="input-status" class="form-control">
                                                    <?php if ($billmate_invoice[$country['code']]['status']) { ?>
                                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                                    <option value="0"><?php echo $text_disabled; ?></option>
                                                    <?php } else { ?>
                                                    <option value="1"><?php echo $text_enabled; ?></option>
                                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-merchant"><span data-toggle="tooltip" title="<?php echo $entry_merchant_help; ?>"><?php echo $entry_merchant; ?></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][merchant]" value="<?php echo $billmate_invoice[$country['code']]['merchant']; ?>" id="input-merchant" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-secret"><span data-toggle="tooltip" title="<?php echo $entry_secret_help; ?>"><?php echo $entry_secret; ?></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][secret]" value="<?php echo $billmate_invoice[$country['code']]['secret']; ?>" id="input-secret" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-secret"><span data-toggle="tooltip" title="<?php echo $entry_logo_help; ?>"><?php echo $entry_logo; ?></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][logo]" value="<?php echo isset($billmate_invoice[$country['code']]['logo']) ? $billmate_invoice[$country['code']]['logo'] : ''; ?>" placeholder="<?php echo $entry_logo; ?>" id="input-logo" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
                                            <div class="col-sm-10">
                                                <textarea cols="84" rows="10" name="billmate_invoice[<?php echo $country['code'];?>][description]"><?php echo isset($billmate_invoice[$country['code']]['description']) ? $billmate_invoice[$country['code']]['description'] : ''; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-mintotal"><span data-toggle="tooltip" title="<?php echo $entry_mintotal_help; ?>"><?php echo $entry_mintotal; ?></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][mintotal]" value="<?php echo isset($billmate_invoice[$country['code']]['mintotal']) ? $billmate_invoice[$country['code']]['mintotal'] : '' ; ?>" id="input-mintotal" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-maxtotal"><span data-toggle="tooltip" title="<?php echo $entry_maxtotal_help; ?>"><?php echo $entry_maxtotal; ?></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][maxtotal]" value="<?php echo isset($billmate_invoice[$country['code']]['maxtotal']) ? $billmate_invoice[$country['code']]['maxtotal'] : '' ; ?>" id="input-maxtotal" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_pending_status; ?></label>
                                            <div class="col-sm-10">
                                                <select name="billmate_invoice[<?php echo $country['code']; ?>][pending_status_id]" id="input-order-status" class="form-control">
                                                    <?php foreach ($order_statuses as $order_status) { ?>
                                                    <?php if ($order_status['order_status_id'] == $billmate_invoice[$country['code']]['pending_status_id']) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                    <?php } else { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_accepted_status; ?></label>
                                            <div class="col-sm-10">
                                                <select name="billmate_invoice[<?php echo $country['code']; ?>][accepted_status_id]" id="input-order-status" class="form-control">
                                                    <?php foreach ($order_statuses as $order_status) { ?>
                                                    <?php if ($order_status['order_status_id'] == $billmate_invoice[$country['code']]['accepted_status_id']) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                    <?php } else { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="input-country" class="col-sm-2 control-label"><?php echo $entry_available_countries;; ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmateinvoice-country" class="form-control">
                                                <div class="dropdown-menu"></div>
                                                <div class="well well-sm" id="billmateinvoice-country">
                                                    <?php if(isset($billmate_country) && is_array($billmate_country)){ ?>
                                                    <?php foreach ($billmate_country as $key => $b_country) { ?>
                                                    <div id="billmateinvoice-country<?php echo $key; ?>"><i class="fa fa-minus-circle"></i> <?php echo $b_country['name']; ?>
                                                        <input type="hidden" name="billmateinvoice-country[<?php echo $key;?>][name];?>" value="<?php echo $b_country['name']; ?>" />
                                                    </div>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-test-status"><?php echo $entry_server; ?></label>
                                            <div class="col-sm-10">
                                                <select name="billmate_invoice[<?php echo $country['code']; ?>][server]" id="input-test-status" class="form-control">
                                                    <?php if (isset($billmate_invoice[$country['code']]) && $billmate_invoice[$country['code']]['server'] == 'live') { ?>
                                                    <option value="live" selected="selected"><?php echo $text_live; ?></option>
                                                    <?php } else { ?>
                                                    <option value="live"><?php echo $text_live; ?></option>
                                                    <?php } ?>
                                                    <?php if (isset($billmate_invoice[$country['code']]) && $billmate_invoice[$country['code']]['server'] == 'beta') { ?>
                                                    <option value="beta" selected="selected"><?php echo $text_beta; ?></option>
                                                    <?php } else { ?>
                                                    <option value="beta"><?php echo $text_beta; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="billmate_invoice[<?php echo $country['code']; ?>][sort_order]" value="<?php echo $billmate_invoice[$country['code']]['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-log">
                            <p>
                                <textarea wrap="off" rows="15" class="form-control"><?php echo $log ?></textarea>
                            </p>
                            <div class="text-right"><a href="<?php echo $clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $button_clear ?></a></div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    $('#country a:first').tab('show');
    //--></script>
<script type="text/javascript">
    var token = '<?php echo $token; ?>';
</script>
<script src="../billmate/js/billmate.js"></script>
<?php echo $footer; ?>