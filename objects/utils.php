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


} 

?>
