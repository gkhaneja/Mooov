<?php
require_once("objects/cache.php");
require_once("conf/constants.inc");
require_once("objects/utils.php");

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


/*$d = date('Y-m-d h:m:s', time()) . "\n";
$t = strtotime($d) . "\n";
$d2 = date('Y-m-d h:m:s', $t) . "\n";
echo $d . "\n";
echo "$d2\n";*/
/*$hh="02";
$mm="58";
echo date('Y-m-d', time()) . " " . $hh . ":" . $mm  . ":00" . "\n";*/

//$utils = new Util();
/*$res = Utils::checkParams(array('user_id' => '34', 'id' => '2'), array('user_id', 'id'));
print_r($res);
echo "\n";*/


/*$pattern = '/rid=([0-9]*)/';
$subject = "[2013-02-04 22-02-15][rid=1359997330][RequestService][setRegionVariables] Setting up region as";
echo preg_match($pattern,$subject,&$matches) . "\n";
print_r($matches);*/

echo mail('gourav.khaneja@gmail.com',"Test","Test") . "\n";
?>
