<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');
require_once('objects/JSONMessage.php');
require_once('objects/city.php');
require_once('objects/utils.php');
require_once('objects/location_info.php');
require_once('objects/facebook_info.php');
 require_once("conf/constants.inc");
require_once("utils/revgeo.php");
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
		$this->fields['dst_address'] = new Field('dst_address','dst_address',0);
		$this->fields['route_id'] = new Field('route_id','route_id',0);
		$this->fields['type'] = new Field('type','type',0);
		$this->fields['time'] = new Field('time','time',0);
		$this->fields['city'] = new Field('city','city',0);
	}

 function checkTypeCompatibility($type1, $type2){
  //HACK: Do not filter results based on 'type' for time being (management is crazy, isn't it?). Always return true
  return TRUE;
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

 function checkTimeCompatibility($time1, $time2){
  if(abs($time1-$time2) <= $GLOBALS['TIME_THRESHOLD']) return true;
  return false;
 }

 function satisfaction($matches, $ntry){
  if($ntry==0){
   return false;
  }
  if($ntry>1){
   return true;
  }
  if(count($matches)<5){
   $GLOBALS['RADIUS'] = $GLOBALS['RADIUS'] + 100;
   $GLOBALS['RADIUS2'] = $GLOBALS['RADIUS2'] + 0.001;
   $GLOBALS['TIME_THRESHOLD'] = $GLOBALS['TIME_THRESHOLD'] + 900*$ntry;
   return false;
  }
  return true;
 }

function matchRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $users = array()){
 $route = new Route($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst,strtotime($ttime));
 //$coords = $this->getSearchCoords($route);	
 $step_x = $GLOBALS['RADIUS2'];
 $step_y = $GLOBALS['RADIUS2'];
 $x1 = ($route->lat_src - $step_x);
 $x2 = ($route->lat_src + $step_x);
 $y1 = ($route->lon_src - $step_y);
 $y2 = ($route->lon_src + $step_y);
 $matches = array();
 $details = array(); 
 $sql = "select * from request where src_latitude>=$x1 AND src_latitude<=$x2 AND src_longitude>=$y1 AND src_longitude<=$y2";
 $results = parent::execute($sql);
 while($row = $results->fetch_assoc()) {
		$matches[]=$row['user_id'];
  $details[$row['user_id']] = $row;
 }
 //$matches = $this->match($coords, $GLOBALS['src_table']);

 $fbarray = array();
 foreach($matches as $match){
  if(empty($match)) continue;
  $sql = "select fbid from user_details where user_id = $match";
  $result = parent::execute($sql);
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    if(empty($row['fbid']) || $row['fbid']==NULL){
     $fbarray['nofbid-' . $match] = array('match' => $match, 'lastupd' => $details[$match]['lastupd']);
     Logger::do_log("No fbid for $match");
     continue;
    }
    if(array_key_exists($row['fbid'], $fbarray)){
     Logger::do_log("comparing time- " . strtotime($fbarray[$row['fbid']]['lastupd']) . " and " . strtotime($details[$match]['lastupd']));
     Logger::do_log("comparing time- " . $fbarray[$row['fbid']]['lastupd'] . " and " . $details[$match]['lastupd']);
     if(strtotime($fbarray[$row['fbid']]['lastupd']) < strtotime($details[$match]['lastupd'])){
      Logger::do_log("Updating  " . $fbarray[$row['fbid']]['match'] . " to $match for fbid " . $row['fbid']);
      $fbarray[$row['fbid']] = array('match' => $match, 'lastupd' => $details[$match]['lastupd']);
     }else{
      Logger::do_log("NOT updating  " . $fbarray[$row['fbid']]['match'] . " to $match for fbid " . $row['fbid']);
     }
    }else{
     $fbarray[$row['fbid']] = array('match' => $match, 'lastupd' => $details[$match]['lastupd']);
     Logger::do_log("Got new user_id, fbid as $match, " . $row['fbid']);
    }
   }
  }else{
   $fbarray['nofbid-' . $match] = array('match' => $match, 'lastupd' => $details[$match]['lastupd']);
   Logger::do_log("No fbid for $match");
  }
 }

 $matches = array_unique($matches);
 if(($key = array_search($user_id, $matches)) !== FALSE) {
  unset($matches[$key]);
 }
 foreach($users as $user){
  if(($key = array_search($user['user_id'], $matches)) !== FALSE){
   unset($matches[$key]);
  }
 }

 $routes=array();
 foreach($fbarray as $fbrow){
  $match = $fbrow['match'];
  if(($key = array_search($match, $matches)) === FALSE) {
   continue;
  }
  if(empty($match)) continue;
  $sql = "select * from request where user_id = $match";
  $result = parent::execute($sql);
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    if($this->checkTypeCompatibility($type,$row['type'])==FALSE || $this->checkTimeCompatibility(strtotime($ttime),strtotime($row['time']))==FALSE){
     continue;
    }
    $route2 = new Route($match, $row['src_latitude'], $row['src_longitude'], $row['dst_latitude'], $row['dst_longitude'],strtotime($row['time']));
    $routes[] = $route2;
   }
  }  
 }
 $ret = $route->matchRoutes($route,$routes);
	return $ret;
}

 function getRandomMatches($arguments){
  if($GLOBALS['site']==0){
   if(!isset($arguments['user_id']) && !isset($arguments['id'])){
			 throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
		 }
		 if(!isset($arguments['id'])){
			 $result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		 }else{
    $result = parent::select('request',array('*'),array('id' => $arguments['id']));
	  }	
		 if(count($result)==0){
		  throw new APIException(array("code" =>"5" , 'error' => 'Request does not exist.'));
		 }
   $user_id = $result[0]['user_id'];
   $src_lat = $result[0]['src_latitude'];
   $src_lon = $result[0]['src_longitude'];
   $dst_lat = $result[0]['dst_latitude'];
   $dst_lon = $result[0]['dst_longitude'];
   $type = $result[0]['type'];
   $time = $result[0]['time'];
  }else{
   if(Utils::checkParams($arguments, array('src_latitude','src_longitude','dst_latitude','dst_longitude'))==0){
			 throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
   }
   $user_id = 0;
   $src_lat = $arguments['src_latitude'];
   $src_lon = $arguments['src_longitude'];
   $dst_lat = $arguments['dst_latitude'];
   $dst_lon = $arguments['dst_longitude'];
   $type = (isset($arguments['type'])) ? $arguments['type'] : 0;
   $time = (isset($arguments['time'])) ? date('Y-m-d', time()) . " " . $arguments['time']  . ":00" : date('Y-m-d H:i:s', time()); 
  }
  $ntry=0;
  $matches = array();
  while($this->satisfaction($matches,$ntry)==false){
		 $matches = array_merge($matches, $this->matchRequest($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, $type, $time, $matches));	
   $ntry++;
  }
  $resp = $this->showMatches($matches);
  if($GLOBALS['site']==0){
   Logger::do_log("Caching the result, key $user_id");
   $cache_arr = array('user_id' => $user_id, 'resp' => $resp, 'time' => time());
   Cache::setValueArray($user_id, $cache_arr);
  }
 }

	function getMatches($arguments){
  if($GLOBALS['site']==0){
   if(!isset($arguments['user_id']) && !isset($arguments['id'])){
			 throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
		 }
		 if(!isset($arguments['id'])){
			 $result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		 }else{
    $result = parent::select('request',array('*'),array('id' => $arguments['id']));
	  }	
		 if(count($result)==0){
		  throw new APIException(array("code" =>"5" , 'error' => 'Request does not exist.'));
		 }
   $user_id = $result[0]['user_id'];
   $src_lat = $result[0]['src_latitude'];
   $src_lon = $result[0]['src_longitude'];
   $dst_lat = $result[0]['dst_latitude'];
   $dst_lon = $result[0]['dst_longitude'];
   $type = $result[0]['type'];
   $time = $result[0]['time'];
  }else{
   if(Utils::checkParams($arguments, array('src_latitude','src_longitude','dst_latitude','dst_longitude'))==0){
			 throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
   }
   $user_id = 0;
   $src_lat = $arguments['src_latitude'];
   $src_lon = $arguments['src_longitude'];
   $dst_lat = $arguments['dst_latitude'];
   $dst_lon = $arguments['dst_longitude'];
   $type = (isset($arguments['type'])) ? $arguments['type'] : 0;
   $time = (isset($arguments['time'])) ? date('Y-m-d', time()) . " " . $arguments['time']  . ":00" : date('Y-m-d H:i:s', time());
   //Logger::do_log("Time: $time");
  }
		$city = new City();
  $ntry=0;
  $matches = array();
  while($this->satisfaction($matches,$ntry)==false){
   //Logger::do_log("Calling: matchRequest($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, $type, $time, $matches)");
		 $matches = array_merge($matches, $city->matchRequest($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, $type, $time, $matches));	
   $ntry++;
  }
  $resp = $this->showMatches($matches);
  if($GLOBALS['site']==0){
   Logger::do_log("Caching the result, key $user_id");
   $cache_arr = array('user_id' => $user_id, 'resp' => $resp, 'time' => time());
   Cache::setValueArray($user_id, $cache_arr);
  }
}


function showMatches($matches){ 
  $match_str="";
  foreach($matches as $match){
   $match_str .= $match['user_id'] . "(" . $match['percent'] . "),";
  } 
  Logger::do_log("Matches: $match_str");	
  
  $resp = array();
  foreach($matches as $match){
   $fb_array = array();
   $user_array = array();
   $other_info = array();
			$sql = "select * from user where id =" . $match['user_id'];
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $user_array = array("user_id" => $match['user_id'], "username" => stripslashes($row['username']));    
    }
   }                            
   $sql = "select * from request where user_id =" . $match['user_id'];
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $locinfo_src = new LocationInfo('src',$row);
     $locinfo_dst = new LocationInfo('dst',$row);
		   $type= $row['type'];
     $other_info = array('type' => $type, 'percent_match' => $match['percent']);
     $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get());
			 }
   }
   $merg_array = array_merge($user_array , $loc_array);
   $sql = "select * from user_details where user_id = " . $match['user_id'];
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $fbinfo = new FBInfo($row);
     $fb_array = $fbinfo->getData();
     $fb_array['fb_info_available'] = 1;
    }
	  }
   if(!isset($fb_array['fb_info_available'])){
     $fb_array['fb_info_available'] = 0; 
   }
   $resp[] = array("loc_info" => $merg_array,  "fb_info" => $fb_array, "other_info" => $other_info);
		}                
  $json_msg = new JSONMessage();
  $json_msg->setBody (array("NearbyUsers" => $resp)); 
		echo $json_msg->getMessage();
  return $resp;
	}

 function deleteRandom($arguments){
 }

	function delete($arguments, $unrecognized=0){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set', 'field'=>'user_id'));
		}
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5" , 'entity'=>'user' ,'error' => 'User does not exist'));
		}
  if($unrecognized==0){
		 $city = new City();
		 $city->deleteRequest($arguments['user_id']);
  }
		
		$result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		if(count($result)>0){
   $route = new Route($user_id, $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude'], strtotime($result[0]['time']));
   $route->delete();
			$sql = "DELETE FROM request WHERE user_id = " . $arguments['user_id'];
			parent::execute($sql);
		}

		$json_msg = new JSONMessage();
		$json_msg->setBody("status:0");
		echo $json_msg->getMessage();
	}

	
	function add($arguments, $unrecognized=0){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3" , 'field'=>'user_id' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['src_latitude'])){
			throw new APIException(array("code" =>"3" , 'field'=>'src_latitude' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['src_longitude'])){
			throw new APIException(array("code" =>"3" , 'field'=>'src_longitude' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_latitude'])){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_latitude' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_longitude'])){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_longitude' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['src_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'src_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['time'])){
   $arguments['time'] = date('Y-m-d H:i:s',time());
		}else{
   $arguments['time'] = $arguments['time'];
  }
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5",'entity'=>'user', 'error' => 'User does not exist'));
		}

  if($unrecognized == 0){
 	 $city = new City();
   $city->addRequest($arguments['user_id'], $arguments['src_latitude'], $arguments['src_longitude'], $arguments['dst_latitude'], $arguments['dst_longitude'], $arguments['time']);
  }

  $route = new Route($arguments['user_id'], $arguments['src_latitude'], $arguments['src_longitude'], $arguments['dst_latitude'], $arguments['dst_longitude'], strtotime($arguments['time']));
	 $arguments['route_id'] = $route->add();
  $arguments['city'] = $GLOBALS['city'];
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

 function get($arguments){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3" , 'field'=>'user_id', 'error' => 'Required Fields are not set'));
		}
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5" ,'entity'=>'user', 'error' => 'User does not exist'));
		}
		$result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		if(isset($result[0]['id'])){
		 $json_msg = new JSONMessage();
		 $json_msg->setBody($result[0]);
		 echo $json_msg->getMessage();
  }else{
			throw APIException(array("code" =>"5" , 'entity'=>'request', 'error' => 'Request does not exist.'));
  }
 }

 function addCarpoolRequest($arguments){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3" , 'field'=>'user_id' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['src_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'src_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['time'])){
   $arguments['time'] = date('Y-m-d H:i:s',time());
		}else{
   $arguments['time'] = $arguments['time'];
  }
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5",'entity'=>'user', 'error' => 'User does not exist'));
		}

  $geocoding = new GeoCoding();
  if(!isset($arguments['src_latitude']) || !isset($arguments['src_longitude'])){
   $src_coord = $geocoding->geocode($arguments['src_address']); 
   $src_lat=$src_coord['lat']; $src_lon=$src_coord['lon'];  
  }else{
   $src_lat=$arguments['src_latitude']; $src_lon=$arguments['src_longitude'];  
  }
  if(!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
   $dst_coord = $geocoding->geocode($arguments['dst_address']); 
   $dst_lat=$dst_coord['lat']; $dst_lon=$dst_coord['lon'];
  }else{
   $dst_lat=$arguments['dst_latitude']; $dst_lon=$arguments['dst_longitude'];  
  }

		$result = parent::select('carpool',array('id'),array('user_id' => $arguments['user_id']));
  $user_id=$arguments['user_id']; $src_add=$arguments['src_address']; $dst_add=$arguments['dst_address']; $ttime=$arguments['time'];
  if(isset($result[0]['id'])){
   $sql = "UPDATE carpool SET src_latitude=$src_lat, src_longitude=$src_lon, dst_latitude=$dst_lat, dst_longitude=$dst_lon, src_address=\"$src_add\", dst_address=\"$dst_add\", time=\"$ttime\" WHERE user_id=$user_id";
  }else{
   $sql = "INSERT INTO carpool (user_id, src_latitude, src_longitude, dst_latitude, dst_longitude, src_address, dst_address, time) VALUES ($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, \"$src_add\", \"$dst_add\", \"$ttime\")";
  }
  parent::execute($sql);
		$result = parent::select('carpool',array('id'),array('user_id' => $arguments['user_id']));
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("request_id" => $result[0]['id']));
		echo $json_msg->getMessage();
 }

 function getCarpoolMatches($arguments){
		if(!isset($arguments['src_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'src_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_address'])){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_address' ,'error' => 'Required Fields are not set'));
		}
  $geocoding = new GeoCoding();
  if(!isset($arguments['src_latitude']) || !isset($arguments['src_longitude'])){
   $src_coord = $geocoding->geocode($arguments['src_address']); $src_lat=$src_coord['lat']; $src_lon=$src_coord['lon'];
  }else{
   $src_lat=$arguments['src_latitude']; $src_lon=$arguments['src_longitude'];  
  }
  if(!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
   $dst_coord = $geocoding->geocode($arguments['dst_address']); $dst_lat=$dst_coord['lat']; $dst_lon=$dst_coord['lon'];
  }else{
   $dst_lat=$arguments['dst_latitude']; $dst_lon=$arguments['dst_longitude'];  
  }
  $sql = "SELECT user_id from carpool WHERE ABS(src_latitude-$src_lat)<0.004 AND ABS(src_longitude-$src_lon)<0.004 AND ABS(dst_latitude-$dst_lat)<0.004 AND (dst_longitude-$dst_lon)<0.004";
  $result = parent::execute($sql);
  $matches=array();
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    $matches[]=$row['user_id'];
   }
  }
  $this->showCarpoolMatches($matches);
}

function showCarpoolMatches($matches){
  $match_str="";
  foreach($matches as $match){
   $match_str .= $match . ", ";
  } 
  Logger::do_log("Matches: $match_str");	

  $resp = array();
  foreach($matches as $match){
   $fb_array = array();
   $user_array = array();
   $other_info = array();
			$sql = "select * from user where id =" . $match;
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $user_array = array("user_id" => $match, "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));    }
   }                            
   $sql = "select * from carpool where user_id =" . $match;
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $locinfo_src = new LocationInfo('src',$row);
     $locinfo_dst = new LocationInfo('dst',$row);
		   $type= $row['type'];
     $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get());
			 }
   }
   $merg_array = array_merge($user_array , $loc_array);
   $sql = "select * from user_details where user_id = " . $match;
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

}



?>
