<pre><?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require_once dirname(dirname(__FILE__)).'/BillMate.php';

// Dependencies from http://phpxmlrpc.sourceforge.net/
include("../xmlrpc-2.2.2/lib/xmlrpc.inc");
include("../xmlrpc-2.2.2/lib/xmlrpcs.inc");

$eid = 7270;//7320;
$key = 606250886062;//511461125114;
$ssl = true;
$debug = true;


$k = new BillMate($eid,$key,$ssl,$debug);


$additionalinfo = array(
	"currency"=>0,//SEK
	"country"=>209,//Sweden
	"language"=>125,//Swedish
);

try {

    $result = $k->FetchCampaigns($additionalinfo);
    
    //Result:
//    print_r($addr);
    
} catch(Exception $e) {
    //Something went wrong
//    echo "{$e->getMessage()} (#{$e->getCode()})\n";
}
