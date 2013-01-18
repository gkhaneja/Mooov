<?php
require_once("objects/cache.php");
require_once("conf/constants.inc");

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
/*$address = "Regus, Trade Centre Bandra Kurla Complex, Ground & Level 1, Bandra Kurla Complex, Bandra (E), Mumbai, Maharashtra";
$add = urlencode($address);
echo $add . "\n";
$address = urldecode($add);
echo $address . "\n";*/

/*$region='mumbai';
$arguments['src_latitude']=19.00;
if(contains($arguments['src_latitude'],constant($region . '_NORTH'),constant($region . '_SOUTH'))){
    echo "region is " . $region . "\n";
}else{
    echo "no region \n";
}
 
function contains($x, $y, $z){
  echo "called as $x, $y, $z \n";
  if($y>$z){
   if($x>=$z && $x<=$y){
     return true;
   }else{
     return false;
   }
  }else{
   if($x>=$y && $x<=$z){
     return true;
   }else{
     return false;
   }
  }
  return false;
}*/

echo date('Y-m-d h:m:s', time()) . "\n";
echo strtotime(date('d-m-Y h:m:s', time())) . "\n";
echo strtotime('d-m-Y h:m:s') . "\n";
echo time() . "\n";
echo time("2013-01-21 00:00:00") . "\n";
?>
