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
require_once('Rest/RequestService.php');

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
		$this->fields['women'] = new Field('women','women',0);
		$this->fields['facebook'] = new Field('facebook','facebook',0);
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
  if($ntry>=1){
   return true;
  }
  if(count($matches)<5){
   $GLOBALS['RADIUS'] = $GLOBALS['RADIUS'] + 100;
   $GLOBALS['RADIUS2'] = $GLOBALS['RADIUS2'] + 0.01;
   $GLOBALS['TIME_THRESHOLD'] = $GLOBALS['TIME_THRESHOLD'] + 900*$ntry;
   return false;
  }
  return true;
 }

function matchRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $women, $facebook, $users = array()){
 $route = new Route($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst,strtotime($ttime));
 $routes = array();
 $matches = $this->matchAnyRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $women, $facebook, $users, 'request');
 foreach($matches as $match => $match_row){
   $route2=new Route($match,$match_row['request']['src_latitude'],$match_row['request']['src_longitude'],$match_row['request']['dst_latitude'],$match_row['request']['dst_longitude'],strtotime($match_row['request']['time']));
   $routes[] = $route2; 
 }  
 
 $percents = $route->matchRoutes($route,$routes);
 $ret = array();
 foreach($percents as $percent){
  $ret[] = array('match' => $percent['user_id'], 'percent' => $percent['percent'], 'request'=>$matches[$percent['user_id']]['request'], 'details' => $matches[$percent['user_id']]['details']);
 }
	return $ret;
}

function apply_filters($details, $filters, $details2, $filters2){
 $ret=1;
 if(isset($filters)){
  foreach($filters as $filter => $value){
   if(!isset($details2) || !isset($details2[$filter]) || $details2[$filter]!=trim($value)){
    Logger::do_log("Removing on $filter " . $details2[$filter] . "== " . $value);
    $ret=0;
   }
  }
 }
 if(isset($filters2)){
  foreach($filters2 as $filter => $value){
   if(!isset($details) || !isset($details[$filter]) || $details[$filter]!=trim($value)){
    Logger::do_log("Removing2 on $filter " . $details[$filter] . "== " . $value);
    $ret=0;
   }
  }
 }
 return $ret; 
}

function setFilters($arguments, $table = "request_filters"){
 Utils::checkParams2($arguments, array('user_id'));
 $filters = array();
 $user_id = $arguments['user_id'];
 foreach($arguments as $argument => $value){
  if(trim($argument)=='user_id' || trim($argument)=='url') continue;
  $filters[$argument] = $value;
 }
 $filter_str = serialize($filters);
 $result = mysqli_fetch_assoc(parent::execute("Select id from $table where user_id = $user_id"));
 if(isset($result) && isset($result['id'])){
  parent::execute("Update $table set filters = '$filter_str' where user_id=$user_id");
 }else{ 
  parent::execute("Insert INTO $table (user_id, filters) VALUES ($user_id, '$filter_str')");
 }
}

function getHopheads($geos,$user_id, $request = 'carpool',$women=0){
 $service = new RequestService();
 $region = $service->detect_region($geos);
 $matches = array();
 $hopheads = array();
 $result = parent::execute("select * from user_details where fbid=649462845 limit 1");
 if($result->num_rows>0){
  $hopheads['arpit'] = $result->fetch_assoc();
  $hopheads['arpit']['firstname'] = 'Hopin';
  $hopheads['arpit']['lastname'] = 'Admin';
 }
 /*$result = parent::execute("select * from user_details where fbid=1134022647 limit 1");
 if($result->num_rows>0){
  $hopheads['abhijeet'] = $result->fetch_assoc();
 }*/
 $result = parent::execute("select * from $request where user_id = $user_id");
 if($result->num_rows>0){
  $request = $result->fetch_assoc();
 }else{
  return $matches;
 }
 if((!isset($hopheads['abhijeet']) && !isset($hopheads['arpit'])) || $women==1){
  return $matches;
 }
  
 if($region=='mumbai' && isset($hopheads['arpit'])){
  $hopid = $hopheads['arpit']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['arpit']); 
 }
 /*if($region=='mumbai' && !isset($hopheads['arpit'])){
  $hopid = $hopheads['abhijeet']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['abhijeet']); 
 }*/
 if($region=='bangalore' && isset($hopheads['abhijeet'])){
  $hopid = $hopheads['abhijeet']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['abhijeet']); 
 }
 if($region=='bangalore' && !isset($hopheads['abhijeet'])){
  $hopid = $hopheads['arpit']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['arpit']); 
 }
 if($region!='bangalore' && $region!='mumbai'){
  $hopid = $hopheads['arpit']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['arpit']); 
  /*$hopid = $hopheads['abhijeet']['user_id'];
  $request['user_id'] = $hopid;
  $matches[$hopid]=array('match'=>$hopid,'percent'=>100,'request'=>$request,'details'=>$hopheads['abhijeet']); */
 }
 return $matches;
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
   $women = $result[0]['women'];
   $facebook = $result[0]['facebook'];
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
   $women =(isset($arguments['women'])) ? $arguments['women'] : 0;
   $facebook =(isset($arguments['facebook'])) ? $arguments['facebook'] : 0;
  }
  $ntry=0;
  $matches = array();
  $match_ids = array();
  $matches_new = array();
  while($this->satisfaction($matches,$ntry)==false){
		 $matches_new = ($this->matchRequest($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, $type, $time, $women, $facebook, $match_ids));
	  $match_ids = array_merge($match_ids, array_keys($matches_new));
   $matches = array_merge($matches, $matches_new);
   $ntry++;
  }
  $fbid=0;
  $result = parent::execute("select fbid from user_details where user_id = $user_id");
  if($result->num_rows>0){
   $row = $result->fetch_assoc();
   $fbid = $row['fbid'];
  }
  if(count($matches)==0){
   $geos = array('src_latitude'=>$src_lat,'src_longitude'=>$src_lon,'dst_latitude'=>$dst_lat,'dst_longitude'=>$dst_lon);
   $matches = $this->getHopheads($geos,$user_id,'request',$women);
  }
  $resp = $this->showMatches($matches,$fbid,0);
  if($GLOBALS['site']==0){
   Logger::do_log("Caching the result, key $user_id");
   $cache_arr = array('user_id' => $user_id, 'resp' => $resp, 'time' => time());
   Cache::setValueArray($user_id, $cache_arr);
  }
 }


function showMatchesOld($matches,$fbid=0){ 
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
     $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get(), "time_info" => $row['time']);
			 }
   }
   $merg_array = array_merge($user_array, $other_info);
   $sql = "select * from user_details where user_id = " . $match['user_id'];
   $result = parent::execute($sql);
   if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
     $fbinfo = new FBInfo($row);
     $fb_array = $fbinfo->getData();
     $fb_array['fb_info_available'] = 1;
     $fbid2 = $row['fbid'];
     $friends = parent::execute("select * from connections where (fbid1=$fbid AND fbid2=$fbid2)");
     if($friends->num_rows > 0){
      $row = $friends->fetch_assoc();
      $fb_array['is_friend']=$row['friends'];
      $fb_array['mutual_friends'] = unserialize($row['path']);
      $fb_array['mutual_friends_count'] = $row['mutual_friends_count'];
     }else{
      $fb_array['is_friend']=0;
      $fb_array['mutual_friends'] = array();
      $fb_array['mutual_friends_count'] = 0;
     }
    }
	  }
   if(!isset($fb_array['fb_info_available'])){
     $fb_array['fb_info_available'] = 0; 
   }
   $resp[] = array("loc_info" => $loc_array,  "fb_info" => $fb_array, "other_info" => $merg_array);
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
	
  if(isset($arguments['insta']) && $arguments['insta']==0){
		 $result = parent::select('carpool',array('*'),array('user_id' => $arguments['user_id']));
		 if(count($result)>0){
			 $sql = "DELETE FROM carpool WHERE user_id = " . $arguments['user_id'];
			 parent::execute($sql);
   }
  }else{	
		 $result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		 if(count($result)>0){
    $route = new Route($user_id, $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude'], strtotime($result[0]['time']));
    $route->delete();
			 $sql = "DELETE FROM request WHERE user_id = " . $arguments['user_id'];
			 parent::execute($sql);
		 }
  }

		$json_msg = new JSONMessage();
		$json_msg->setBody("status:0");
		echo $json_msg->getMessage();
	}

	
	function add($arguments, $unrecognized=0){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3" , 'field'=>'user_id' ,'error' => 'Required Fields are not set'));
		}
  //CHANGE: Coordinates are no longer comulsary. If not present, user reverse geo coding.
		/*if(!isset($arguments['src_latitude'])){
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
		}*/
		if(!isset($arguments['src_address']) && (!isset($arguments['src_latitude']) || !isset($arguments['src_longitude']))){
			throw new APIException(array("code" =>"3" , 'field'=>'src_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_address']) && (!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude']))){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['time'])){
   $arguments['time'] = date('Y-m-d H:i:s',time());
		}else{
   $arguments['time'] = $arguments['time'];
  }
  if(!isset($arguments['women'])){
   $arguments['women']=0;
  }
  if(!isset($arguments['facebook'])){
   $arguments['facebook']=0;
  }
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5",'entity'=>'user', 'error' => 'User does not exist'));
		}
  $geocoding = new GeoCoding();
  if(!isset($arguments['src_latitude']) || !isset($arguments['src_longitude'])){
   $src_coord = $geocoding->geocode($arguments['src_address']); 
   if($src_coord != false){
    $arguments['src_latitude']=$src_coord['lat']; $arguments['src_longitude']=$src_coord['lon']; 
    if(isset($src_coord['sublocality'])) $arguments['src_locality']=$src_coord['sublocality'];
   }else{
    throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
   }
  }
  if(!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
   $dst_coord = $geocoding->geocode($arguments['dst_address']); 
   //Logger::do_log("Calculating dst lat lon: " . print_r($dst_coord,true));
   if($dst_coord != false){
    $arguments['dst_latitude']=$dst_coord['lat']; $arguments['dst_longitude']=$dst_coord['lon'];
    if(isset($dst_coord['sublocality'])) $arguments['dst_locality']=$dst_coord['sublocality'];
   }else{
    throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
   }
  }
  if(!isset($arguments['src_address']) || empty($arguments['src_address'])){
   $address = $geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']);
   //Logger::do_log("Calculating src address: " . print_r($address,true));
   if($address != false){
    //Logger::do_log(print_r($geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']),true));
    $arguments['src_address'] = $address['formatted_address'];
    $arguments['src_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['dst_address']) || empty($arguments['dst_address'])){
   $address = $geocoding->reverseGeo($arguments['dst_latitude'],$arguments['dst_longitude']);
   //Logger::do_log("Calculating dst address: " . print_r($address,true));
   if($address != false){
    $arguments['dst_address'] = $address['formatted_address'];
    $arguments['dst_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['src_locality'])){
   $address = $geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']);
   //Logger::do_log("Calculating src locality: " . print_r($address,true));
   if($address != false){
    $arguments['src_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['dst_locality'])){
   $address = $geocoding->reverseGeo($arguments['dst_latitude'],$arguments['dst_longitude']);
   //Logger::do_log("Calculating dst locality: " . print_r($address,true));
   if($address != false){
    $arguments['dst_locality'] = $address['sublocality'];
   }
  }
  //Logger::do_log(print_r($geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']),true));

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
		$result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		$json_msg = new JSONMessage();
		$json_msg->setBody($result[0]);
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
		if(!isset($arguments['src_address']) && (!isset($arguments['src_latitude']) || !isset($arguments['src_longitude']))){
			throw new APIException(array("code" =>"3" , 'field'=>'src_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['dst_address']) && (!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude']))){
			throw new APIException(array("code" =>"3" , 'field'=>'dst_address' ,'error' => 'Required Fields are not set'));
		}
		if(!isset($arguments['time'])){
   $arguments['time'] = date('Y-m-d H:i:s',time());
		}else{
   $arguments['time'] = $arguments['time'];
  }
  if(!isset($arguments['women'])){
   $arguments['women'] = 0;
  }
  if(!isset($arguments['facebook'])){
   $arguments['facebook'] = 0;
  }
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			throw new APIException(array("code" =>"5",'entity'=>'user', 'error' => 'User does not exist'));
		}

  $geocoding = new GeoCoding();
  if(!isset($arguments['src_latitude']) || !isset($arguments['src_longitude'])){
   $src_coord = $geocoding->geocode($arguments['src_address']); 
   if($src_coord != false){
    $arguments['src_latitude']=$src_coord['lat']; $arguments['src_longitude']=$src_coord['lon']; 
    if(isset($src_coord['sublocality'])) $arguments['src_locality']=$src_coord['sublocality'];
   }else{
    throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
   }
  }
  if(!isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
   $dst_coord = $geocoding->geocode($arguments['dst_address']); 
   if($dst_coord != false){
    $arguments['dst_latitude']=$dst_coord['lat']; $arguments['dst_longitude']=$dst_coord['lon'];
    if(isset($dst_coord['sublocality'])) $arguments['dst_locality']=$dst_coord['sublocality'];
   }else{
    throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
   }
  }
  if(!isset($arguments['src_address']) || empty($arguments['src_address'])){
   $address = $geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']);
   if($address != false){
    //Logger::do_log(print_r($geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']),true));
    $arguments['src_address'] = $address['formatted_address'];
    $arguments['src_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['dst_address']) || empty($arguments['dst_address'])){
   $address = $geocoding->reverseGeo($arguments['dst_latitude'],$arguments['dst_longitude']);
   if($address != false){
    $arguments['dst_address'] = $address['formatted_address'];
    $arguments['dst_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['src_locality'])){
   $address = $geocoding->reverseGeo($arguments['src_latitude'],$arguments['src_longitude']);
   if($address != false){
    $arguments['src_locality'] = $address['sublocality'];
   }
  }
  if(!isset($arguments['dst_locality'])){
   $address = $geocoding->reverseGeo($arguments['dst_latitude'],$arguments['dst_longitude']);
   if($address != false){
    $arguments['dst_locality'] = $address['sublocality'];
   }
  }
  $src_lat=$arguments['src_latitude']; $src_lon=$arguments['src_longitude']; 
  $src_address=$arguments['src_address'];$src_locality=$arguments['src_locality'];
  $dst_lat=$arguments['dst_latitude']; $dst_lon=$arguments['dst_longitude']; 
  $dst_address=$arguments['dst_address'];$dst_locality=$arguments['dst_locality'];
		
  $result = parent::select('carpool',array('id'),array('user_id' => $arguments['user_id']));
  $user_id=$arguments['user_id']; $src_add=$arguments['src_address']; $dst_add=$arguments['dst_address']; $ttime=$arguments['time'];
  $women = $arguments['women']; $facebook = $arguments['facebook'];
  if(isset($result[0]['id'])){
   $sql = "UPDATE carpool SET src_latitude=$src_lat, src_longitude=$src_lon, dst_latitude=$dst_lat, dst_longitude=$dst_lon, src_address=\"$src_add\", dst_address=\"$dst_add\", time=\"$ttime\", src_locality=\"$src_locality\", dst_locality=\"$dst_locality\", women=$women, facebook=$facebook WHERE user_id=$user_id";
  }else{
   $sql = "INSERT INTO carpool (user_id, src_latitude, src_longitude, dst_latitude, dst_longitude, src_address, dst_address, time, src_locality, dst_locality, women, facebook) VALUES ($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, \"$src_add\", \"$dst_add\", \"$ttime\", \"$src_locality\", \"$dst_locality\", $women, $facebook)";
  }
  parent::execute($sql);
		$result = parent::select('carpool',array('*'),array('user_id' => $arguments['user_id']));
		$json_msg = new JSONMessage();
		$json_msg->setBody($result[0]);
		echo $json_msg->getMessage();
 }

function matchAnyRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $women, $facebook, $users, $request_type = 'request'){
 $gender='dunno';
 $fbid='nofbid';
 $user_details = parent::execute("select * from user_details where user_id = $user_id");
 if($user_details->num_rows > 0){
  $detail_row = $user_details->fetch_assoc();
  if(isset($detail_row['gender']) && $detail_row['gender']!=NULL){
   $gender=$detail_row['gender'];
  }
  if(isset($detail_row['fbid']) && $detail_row['fbid']!=NULL){
   $fbid=$detail_row['fbid'];
  }
 }
 //$coords = $this->getSearchCoords($route);	
 $step_x = $GLOBALS['RADIUS2'];
 $step_y = $GLOBALS['RADIUS2'];
 $x1 = ($lat_src - $step_x);
 $x2 = ($lat_src + $step_x);
 $y1 = ($lon_src - $step_y);
 $y2 = ($lon_src + $step_y);
 $matches = array();
 $requests = array(); 
 $sql = "select * from $request_type where src_latitude>=$x1 AND src_latitude<=$x2 AND src_longitude>=$y1 AND src_longitude<=$y2";
 $results = parent::execute($sql);
 while($row = $results->fetch_assoc()) {
		$matches[]=$row['user_id'];
  $requests[$row['user_id']] = $row;
 }

 $matches_str = implode(",",$matches);
 $details = array();
 if(count($matches)>0){
  $sql = "select * from user_details where user_id in ($matches_str)";
  $result = parent::execute($sql);
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    $details[$row['user_id']] = $row;
   }
  }
 }

 $unique = array();
 foreach($matches as $match){
  if(isset($details[$match]['fbid']) && !empty($details[$match]['fbid'])){
    $fbid2 = $details[$match]['fbid'];
    if(array_key_exists($fbid2, $unique)){
     if(strtotime($unique[$fbid2]['lastupd']) < strtotime($requests[$match]['lastupd'])){
      $unique[$fbid2]=array('match'=>$match,'lastupd'=>$requests[$match]['lastupd'],'request'=>$requests[$match],'details'=>$details[$match]);
     }
    }else{
     $unique[$fbid2]=array('match'=>$match,'lastupd'=>$requests[$match]['lastupd'],'request'=>$requests[$match],'details'=>$details[$match]);
    }
  }else{
   $unique['nofbid-'.$match]=array('match'=>$match,'lastupd'=>$requests[$match]['lastupd'],'request'=>$requests[$match],'details'=>$details[$match]);
  }
 }

 $matches = array_unique($matches);
 if(($key = array_search($user_id, $matches)) !== FALSE) {
  unset($matches[$key]);
 }
 foreach($users as $user){
  if(($key = array_search($user, $matches)) !== FALSE){
   unset($matches[$key]);
  }
 }

 $ret=array();
 foreach($unique as $fbid2 => $uniq){
  $match = $uniq['match'];
  if(($key = array_search($match, $matches)) === FALSE) {
   continue;
  }
  if(empty($match)) continue;
  $req_match=1;
  $women_match=1; 
  $facebook_match=1;
   if($this->checkTypeCompatibility($type,$uniq['request']['type'])==TRUE && 
      $this->checkTimeCompatibility(strtotime($ttime),strtotime($uniq['request']['time']))==TRUE) { 
    $req_match = 1;
   }
   if($women==1){
    $gender2='dunno';
    if(!isset($uniq['details']['gender']) && $uniq['details']['gender']!=NULL){
     $gender2=$uniq['details']['gender'];
    }
    if($gender2!='female'){
     $women_match=0;
    }
   }
   if($uniq['request']['women']==1 && $gender!='female'){
    $women_match=0;
   }
   if($facebook==1 || $uniq['request']['facebook']==1){
    $facebook_match=0;
    if($fbid!='nofbid' && substr($fbid2,0,6)!='nofbid'){
     $friends = parent::execute("select * from connections where (fbid1='$fbid' AND fbid2='$fbid2')");
     if($friends->num_rows > 0){
      $row2 = $friends->fetch_assoc();
      if($row2['friends']==1 || $row2['mutual_friends_count']>0){
       $facebook_match=1;
      }
     }
    }
   }
 
  if($req_match==1 && $women_match==1 && $facebook_match==1){
   $ret[$match] = $uniq;
  }  
 }
	return $ret;
}

function matchCarpoolRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $women, $facebook, $users = array()){
 return $this->matchAnyRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst, $type, $ttime, $women, $facebook, $users, 'carpool');
}

 function getCarpoolMatches($arguments){
  if($GLOBALS['site']==1 || !isset($arguments['user_id'])){
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
   $type = (isset($arguments['type'])) ? $arguments['type'] : 0;
   $time = (isset($arguments['time'])) ? date('Y-m-d', time()) . " " . $arguments['time']  . ":00" : date('Y-m-d H:i:s', time());    
   $women= (isset($arguments['women'])) ? $arguments['women'] : 0;
   $facebook = (isset($arguments['facebook'])) ? $arguments['facebook'] : 0;
  }else{
		 if(!isset($arguments['user_id'])){
			 throw new APIException(array("code" =>"3" , 'field'=>'user_id' ,'error' => 'Required Fields are not set'));
		 }   
			$result = parent::select('carpool',array('*'),array('user_id' => $arguments['user_id']));
		 if(count($result)==0){
		  throw new APIException(array("code" =>"5" , 'error' => 'Request does not exist.'));
		 }
   $user_id = $result[0]['user_id'];
   $src_lat = $result[0]['src_latitude'];
   $src_lon = $result[0]['src_longitude'];
   $dst_lat = $result[0]['dst_latitude'];
   $dst_lon = $result[0]['dst_longitude'];
   $time = $result[0]['time'];
   $type = $result[0]['type'];
   $women=$result[0]['women'];
   $facebook = $result[0]['facebook'];
  }
  $ntry=0;
  $match_ids = array();
  $matches = array();
  $matches_new = array();
  while($this->satisfaction($matches,$ntry)==false){
		 $matches_new = ($this->matchCarpoolRequest($user_id, $src_lat, $src_lon, $dst_lat, $dst_lon, $type, $time, $women, $facebook, $match_ids));
   $match_ids = array_merge($match_ids, array_keys($matches_new));	
   $matches = array_merge($matches, $matches_new);
   $ntry++;
  }
  $fbid=0;
  $result = parent::execute("select fbid from user_details where user_id = $user_id");
  if($result->num_rows>0){
   $row = $result->fetch_assoc();
   $fbid = $row['fbid'];
  }
  if(count($matches)==0){
   $geos = array('src_latitude'=>$src_lat,'src_longitude'=>$src_lon,'dst_latitude'=>$dst_lat,'dst_longitude'=>$dst_lon);
   $matches = $this->getHopheads($geos,$user_id,'carpool',$women);
  }
  $this->showMatches($matches,$fbid,1);
}

function showMatches($matches,$fbid=0,$carpool = 1){
  $match_str="";
  $match_ids = array();
  foreach($matches as $match){
   if($carpool ==1){
    $match_str .= $match['match'] . ", ";
   }else{
    $match_str .= $match['match'] . "(" . $match['percent'] . "),";
   }
   $match_ids[] = $match['match'];
  } 
  Logger::do_log("Matches: $match_str");	
  $users = array();
 if(count($match_ids)>0){
  $matches_str = implode(",",$match_ids);
  $sql = "select * from user where id in ($matches_str)";
  $result = parent::execute($sql);
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    $users[$row['id']] = $row;
   }
  }
 }
  $resp = array();
  foreach($matches as $match_row){
   $match = $match_row['match'];
   $fb_array = array();
   $user_array = array();
   $other_info = array();
   $user_array = array("user_id" => $match, "username" => stripslashes($users[$match]['username']));    
   $locinfo_src = new LocationInfo('src',$match_row['request']);
   $locinfo_dst = new LocationInfo('dst',$match_row['request']);
		 $type= $match_row['request']['type'];
   $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get(), "time_info" => $match_row['request']['time']);
   if($carpool==1){
    $other_array = array_merge($user_array, array('type' => $type));
   }else{
     $other_array = array_merge($user_array ,array('type' => $type, 'percent_match' => $match_row['percent']));
   }
   if(isset($match_row['details'])){
     $fbinfo = new FBInfo($match_row['details']);
     $fb_array = $fbinfo->getData();
     $fb_array['fb_info_available'] = 1;
     $fbid2 = $match_row['details']['fbid'];
     if(isset($fbid2)){
      $friends = parent::execute("select * from connections where (fbid1=$fbid AND fbid2=$fbid2)");
      if($friends->num_rows > 0){
       $row = $friends->fetch_assoc();
       $fb_array['is_friend']=$row['friends'];
       $fb_array['mutual_friends'] = unserialize($row['path']);
       $fb_array['mutual_friends_count'] = $row['mutual_friends_count'];
      }else{
       $fb_array['is_friend']=0;
       $fb_array['mutual_friends'] = array();
       $fb_array['mutual_friends_count'] = 0;
      }
     }else{
      $fb_array['is_friend']=0;
      $fb_array['mutual_friends'] = array();
      $fb_array['mutual_friends_count'] = 0;
     }
   }
   if(!isset($fb_array['fb_info_available'])){
     $fb_array['fb_info_available'] = 0; 
   }

   $resp[] = array("loc_info" => $loc_array,  "fb_info" => $fb_array, "other_info" => $other_array);
		}                
  $json_msg = new JSONMessage();
  $json_msg->setBody (array("NearbyUsers" => $resp)); 
		echo $json_msg->getMessage();
 }

}



?>
