<?php
ini_set('display_errors', 1 );
error_reporting(E_ALL);
require_once '../BillMate.php';

// Dependencies from http://phpxmlrpc.sourceforge.net/
include("../xmlrpc-2.2.2/lib/xmlrpc.inc");
include("../xmlrpc-2.2.2/lib/xmlrpcs.inc");

$eid = 6882;
$key = 453856744538;
$ssl = true;
$debug = true;


$k = new BillMate($eid,$key,$ssl,$debug);

try {
	//Test Company:
    //450708-2222
    
    //Test People:
    //556000-0753
    //556738-7914
    //870624-0721
    
    //Rejected:
    //840203-4840
    
    //Personal number should be fit to luhn algorithm
    
    $result = $k->CreditCheck('556738-7914',3000,"test@test.com","0760123456");
    
} catch(Exception $e) {
    //Something went wrong
//    echo "{$e->getMessage()} (#{$e->getCode()})\n";
}
