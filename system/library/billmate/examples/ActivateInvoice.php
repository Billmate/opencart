<?php

require_once '../BillMate.php';

// Dependencies from http://phpxmlrpc.sourceforge.net/
include("../xmlrpc-2.2.2/lib/xmlrpc.inc");
include("../xmlrpc-2.2.2/lib/xmlrpcs.inc");

$eid = 7270;
$key = 508860625088;
$ssl = true;
$debug = true;


$k = new BillMate($eid,$key,$ssl,$debug);

try {
	$invoiceNo = '2';
    
    $result = $k->ActivateInvoice($invoiceNo);
    
    //Result:
//    print_r($addr);
    
} catch(Exception $e) {
   //Something went wrong
   echo "{$e->getMessage()} (#{$e->getCode()})\n";
}
