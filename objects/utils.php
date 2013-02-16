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

} 

?>
