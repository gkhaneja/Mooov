<?php
require_once("objects/cache.php");

//Cache::bootup();
/*Cache::init();

$obj = array();
$obj['lat'] = 100;
$obj['time'] = time();
$obj['response'] = "3,2,4";

Cache::setValueArray(1,($obj));
$val = Cache::getValueArray(2);

if(!empty($val))
 print_r($val) . "\n";
else
 echo "key not found\n";
*/

//$address = "Powai Plaza,Opp. Pizza Hut, Hiranandani Gardens, Central Ave, Mumbai, MH";
$address = "Regus, Trade Centre Bandra Kurla Complex, Ground & Level 1, Bandra Kurla Complex, Bandra (E), Mumbai, Maharashtra";
$add = urlencode($address);
echo $add . "\n";
$address = urldecode($add);
echo $address . "\n";
?>
