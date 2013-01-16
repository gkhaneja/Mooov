<?php
require_once('utils/revgeo.php');
require_once('objects/dbclass.php');

class UserCity extends  dbclass {

 private  $city;

 public function getCity($id){
 	$query =  "select * from request where user_id = $id";
  $result = parent::execute($query);
  if($result->num_rows > 0) {
	  $row = $result->fetch_assoc();                           
   $lat = $row['src_latitude'];
   $lng = $row['src_longitude'];
   $city = $row['city']; 
  }
  if(isset($city))
   return $city;
    
	 $gc = new GeoCoding();
	 $r = $gc->getCity($lat,$lng);      
  $query = "update request set city = '$r' where user_id = $id";
  parent::execute($query);
  return $r;
 }
 
 public function setCity($id, $city){  
  $sql = "update request set city='$city' where user_id = $id";
  parent::execute($sql);
 }

}
/*
$c =new UserCity();
$a = $c->getCity(1);

echo " city $a ";
*/
