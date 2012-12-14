<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');
require_once('objects/JSONMessage.php');
require_once('objects/city.php');
require_once('objects/location_info.php');
require_once('objects/facebook_info.php');
 require_once("conf/constants.inc");
class Request extends dbclass {

	var $fields;

	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1); 
		$this->fields['user_id'] = new Field('user_id','user_id',0); 
		$this->fields['src_latitude'] = new Field('src_latitude','src_latitude',0);
		$this->fields['src_longitude'] = new Field('src_longitude','src_longitude',0);
		$this->fields['dst_latitude'] = new Field('dst_latitude','dst_latitude',0);
		$this->fields['dst_longitude'] = new Field('dst_longitude','dst_longitude',0);
		$this->fields['src_locality'] = new Field('src_locality','src_locality',0);
		$this->fields['src_address'] = new Field('src_address','src_address',0);
		$this->fields['dst_locality'] = new Field('dst_locality','dst_locality',0);
		$this->fields['dst_address'] = new Field('dst_longitude','dst_address',0);
		$this->fields['route_id'] = new Field('route_id','route_id',0);
		$this->fields['type'] = new Field('type','type',0);
	}

 function checkTypeCompatibility($type1, $type2){
  $ret = FALSE;
  switch($type1){
   case 0: 
    if($type2==0 || $type2==1) $ret = TRUE;
    break;
   case 1:
    if($type2==0) $ret = TRUE;
    break;
   default:
    $ret = TRUE;
  }
  return $ret;
 }

	function getNearbyRequests($arguments){
  if(!isset($arguments['user_id']) && !isset($arguments['id'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		if(!isset($arguments['id'])){
			$result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		}else{
   $result = parent::select('request',array('*'),array('id' => $arguments['id']));
	 }	
		if(count($result)==0){
		 $error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Request does not exist.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		$city = new City();
  //TODO: call match request with incrementing Radius
		$matches = $city->matchRequest($result[0]['user_id'], $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude']);	
  Logger::do_log("=== Matches ======" . print_r($matches,true));	
 
  //TODO: Rank the results
 	$ret = array();
  $route1 = new Route($result[0]['user_id'], $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude']);
		foreach($matches as $match) {
   $sql = "select * from request where user_id = $match";
   $res = parent::execute($sql);
   if($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if($this->checkTypeCompatibility($result[0]['type'],$row['type'])==TRUE){
 			 $ret[] = $match;
    }
   }
		}
                
  $resp = array();
  foreach($ret as $user){
   $fb_array;
   $user_array;
			$sql = "select * from user where id = $user";
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $user_array = array("user_id" => $user, "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));    }
   }                            
   $sql = "select * from request where user_id = $user";
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $locinfo_src = new LocationInfo('src',$row);
     $locinfo_dst = new LocationInfo('dst',$row);
		   $type= $row['type'];
     $route2 = new Route($user, $row['src_latitude'], $row['src_longitude'], $row['dst_latitude'], $row['dst_longitude']);
     $percent = $route1->matchRoute($route1,$route2);
     $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get(), "type" => $type, "percent_match" => $percent);
			 }
   }
   $merg_array = array_merge($user_array , $loc_array);
   $sql = "select * from user_details where user_id = $user";
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $fbinfo = new FBInfo($row);
     $fb_array = $fbinfo->getData();
    }
	  }
   $resp[] = array("loc_info" => $merg_array,  "fb_info" => $fb_array);
		}                
  $json_msg = new JSONMessage();
  $json_msg->setBody (array("NearbyUsers" => $resp)); 
		echo $json_msg->getMessage();
	}

	function delete($arguments){
		if(!isset($arguments['user_id'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			$error_m = new ExceptionHandler(array("code" =>"5" , 'error' => 'User id does not exist.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		$city = new City();
		$city->deleteRequest($arguments['user_id']);
		$result = parent::select('request',array('id'),array('user_id' => $arguments['user_id']));
		if(isset($result[0]['id'])){
			$sql = "DELETE FROM request WHERE user_id = " . $arguments['user_id'];
			parent::execute($sql);
		}
		$json_msg = new JSONMessage();
		$json_msg->setBody("status:0");
		echo $json_msg->getMessage();
	}
	
	function add($arguments){
		if(!isset($arguments['user_id']) || !isset($arguments['src_latitude']) || !isset($arguments['src_longitude']) || !isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			$error_m = new ExceptionHandler(array("code" =>"5" , 'error' => 'User id does not exist.'));
			echo $error_m->m_error->getMessage();
			return;
		}
	$city = new City();
	$arguments['route_id'] = $city->addRequest($arguments['user_id'], $arguments['src_latitude'], $arguments['src_longitude'], $arguments['dst_latitude'], $arguments['dst_longitude']);
		foreach($this->fields as $field){
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		$result = parent::select('request',array('id'),array('user_id' => $arguments['user_id']));
		if(isset($result[0]['id'])){
			parent::update('request',$this->fields,array('user_id' => $arguments['user_id']));
		}else{
			parent::insert('request',$this->fields);
		}
		$result = parent::select('request',array('id'),array('user_id' => $arguments['user_id']));
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("request_id" => $result[0]['id']));
		echo $json_msg->getMessage();
	}	

}



?>
