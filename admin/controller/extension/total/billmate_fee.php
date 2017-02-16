<?php
/**
 * Created by PhpStorm.
 * User: Boxedsolutions
 * Date: 2017-02-16
 * Time: 14:07
 */
if (!defined('DIR_APPLICATION')) {
    die();
}
$controller = dirname(dirname(__DIR__)).'/total/billmate_fee.php';
require_once $controller;

class ControllerExtensionTotalBillmateFee extends ControllerTotalBillmateFee{

}