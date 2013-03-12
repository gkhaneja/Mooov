<?php

class Utils {


public static function checkParams($arguments, $params){
 foreach($params as $param){
  if(!isset($arguments[$param])){
   return 0;
  }
 }
 return 1;
}

public static function checkParams2($arguments, $params){
 foreach($params as $param){
  if(!isset($arguments[$param])){
			throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
  }
 }
}

public static function geo2distance($lat1, $lon1, $lat2, $lon2){
	$R = 6371000; 
	$lat1 = deg2rad($lat1);
	$lat2 = deg2rad($lat2);
	$lon1 = deg2rad($lon1);
	$lon2 = deg2rad($lon2);
	$d = acos(sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($lon2-$lon1)) * $R;
	return $d;
}

} 

?>
