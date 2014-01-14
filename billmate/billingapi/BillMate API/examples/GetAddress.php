<?php

require_once '../BillMate.php';

// Dependencies from http://phpxmlrpc.sourceforge.net/
include("../xmlrpc-2.2.2/lib/xmlrpc.inc");
include("../xmlrpc-2.2.2/lib/xmlrpcs.inc");

$eid = 7270;
$key = 508860625088; //508860625088 // 606250886062
$ssl = true;
$debug = true;


$k = new BillMate($eid,$key,$ssl,$debug);

try {
	//Test Company:
    //450708-2222
    
    //Test People:
    //556000-0753
    //556738-7914vwZg5Kh376
    //870624-0721
    
    //Rejected:
    //840203-4840
    
    //Personal number should be fit to luhn algorithm
    
    $addr = $k->GetAddress('002031-0132');
    echo strtolower($addr[0][4]) == strtolower($addr[0][4]) ? "ASDF": "DDS";;
    //Result:
//    print_r($addr);
    
} catch(Exception $e) {
    //Something went wrong
//    echo "{$e->getMessage()} (#{$e->getCode()})\n";
}


