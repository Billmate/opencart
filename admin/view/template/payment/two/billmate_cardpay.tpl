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
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-bankpay" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_cardpay_status" id="input-status" class="form-control">
                                <?php if ($billmate_cardpay_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-merchant_id"><span data-toggle="tooltip" title="<?php echo $entry_merchant_help; ?>"><?php echo $entry_merchant_id; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="billmate_cardpay_merchant_id" value="<?php echo $billmate_cardpay_merchant_id; ?>" placeholder="<?php echo $entry_merchant_id; ?>" id="input-merchant_id" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret"><span data-toggle="tooltip" title="<?php echo $entry_secret_help; ?>"><?php echo $entry_secret; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="billmate_cardpay_secret" value="<?php echo $billmate_cardpay_secret; ?>" placeholder="<?php echo $entry_secret; ?>" id="input-secret" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-description"><?php echo $entry_description; ?></label>
                        <div class="col-sm-10">
                            <textarea cols="84" rows="10" name="billmate_cardpay_description"><?php echo $billmate_cardpay_description; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-prompt" class="col-sm-2 control-label"><?php echo $prompt_name_entry; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_prompt_name" class="form-control" id="input-prompt">
                                <?php if ($billmate_prompt_name == 'YES') { ?>
                                <option value="YES" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="NO"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="YES"><?php echo $text_yes; ?></option>
                                <option value="NO"  selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-prompt" class="col-sm-2 control-label"><?php echo $enable_3dsecure; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_enable_3dsecure" class="form-control" id="input-prompt">
                                <?php if ($billmate_enable_3dsecure == 'YES') { ?>
                                <option value="YES" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="NO"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="YES"><?php echo $text_yes; ?></option>
                                <option value="NO"  selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-prompt" class="col-sm-2 control-label"><?php echo $entry_transaction_method; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_cardpay_transaction_method" class="form-control" id="input-prompt">
                                <option value="authorization" <?php echo $billmate_cardpay_transaction_method == 'authorization'? 'selected="selected"':''?>>
                                <?php echo $entry_billmate_cardpay_authorization; ?>
                                </option>
                                <option value="sale" <?php echo $billmate_cardpay_transaction_method == 'sale'? 'selected="selected"':''?>><?php echo $entry_billmate_cardpay_sale; ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="billmate_cardpay_total" value="<?php echo $billmate_cardpay_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_cardpay_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $billmate_cardpay_order_status_id) { ?>
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
                            <input type="text" name="billmatecard-country" class="form-control">
                            <div class="dropdown-menu"></div>
                            <div class="well well-sm" id="billmatecard-country">
                                <?php if(isset($billmate_country) && is_array($billmate_country)){ ?>
                                <?php foreach ($billmate_country as $key => $b_country) { ?>
                                <div id="billmatecard-country<?php echo $key; ?>"><i class="fa fa-minus-circle"></i> <?php echo $b_country['name']; ?>
                                    <input type="hidden" name="billmatecard-country[<?php echo $key;?>][name];?>" value="<?php echo $b_country['name']; ?>" />
                                </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-test-status"><?php echo $entry_test; ?></label>
                        <div class="col-sm-10">
                            <select name="billmate_cardpay_test" id="input-test-status" class="form-control">
                                <?php if ($billmate_cardpay_test) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="billmate_cardpay_sort_order" value="<?php echo $billmate_cardpay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var token = '<?php echo $token; ?>';
</script>
<script src="/billmate/js/billmate.js"></script>
<?php echo $footer; ?>