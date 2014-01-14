<pre><?php

require_once '../BillMate.php';

// Dependencies from http://phpxmlrpc.sourceforge.net/
include("../xmlrpc-2.2.2/lib/xmlrpc.inc");
include("../xmlrpc-2.2.2/lib/xmlrpcs.inc");

$eid = 6882;
$key = 453856744538;
$ssl = true;
$debug = true;


$k = new BillMate($eid,$key,$ssl,$debug);


$additionalinfo = array(
	"currency"=>0,//SEK
	"country"=>209,//Sweden
	"language"=>138,//Swedish
);

try {

    $result = $k->FetchCampaigns($additionalinfo);
    
    //Result:
//    print_r($addr);
    
} catch(Exception $e) {
    //Something went wrong
//    echo "{$e->getMessage()} (#{$e->getCode()})\n";
}
