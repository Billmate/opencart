<?php
/**
 * Created by PhpStorm.
 * User: Boxedsolutions
 * Date: 2017-02-16
 * Time: 14:04
 */
if (!defined('DIR_APPLICATION')) {
    die();
}
$controller = dirname(dirname(__DIR__)).'/payment/billmate_invoice.php';
require_once $controller;

class ControllerExtensionPaymentBillmateInvoice extends ControllerPaymentBillmateInvoice{

}