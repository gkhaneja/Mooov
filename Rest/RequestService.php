<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');
require_once('objects/request.php');
require_once('objects/decode_city.php');
require_once('conf/constants.inc');

class RequestService extends RestService {

	public function addRequest($arguments){
  Logger::do_log("Deleting from cache, key " . $arguments['user_id']);
  Cache::deleteKey($arguments['user_id']);
  $this->initializeRegion($arguments);
		$request = new Request();
  if($GLOBALS['city']=='unrecognized_region'){
   $request->add($arguments,1);
  }else{
 		$request->add($arguments);
  }
	}

 public function addCarpoolRequest($arguments){
		$request = new Request();
		$request->addCarpoolRequest($arguments);
 }

 public function getCarpoolMatches($arguments){
  if(isset($arguments['site']) && $arguments['site']==1){
   $GLOBALS['site']=1;
  }else{
   $GLOBALS['site']=0;
  }
		$request = new Request();
		$request->getCarpoolMatches($arguments);
 }

	public function getMatches($arguments){
  if(isset($arguments['site']) && $arguments['site']==1){
   $GLOBALS['site']=1;
   $this->initializeRegion($arguments);
		$request = new Request();
  }else{
   $GLOBALS['site']=0;
   $val = Cache::getValueArray($arguments['user_id']);
   if(!empty($val) && constant('ENABLE_CACHING')==1){
    if((time() - $val['time'] <= constant('CACHE_EXPIRY'))){
     Logger::do_log("Sending the cached results, key " . $arguments['user_id']);
     $json_msg = new JSONMessage();
     $json_msg->setBody (array("NearbyUsers" => $val['resp'])); 
		   echo $json_msg->getMessage();
     return;
    }
   }
	  $this->setRegion($arguments);
  }
		$request = new Request();
  if($GLOBALS['city']=='unrecognized_region'){
   $request->getRandomMatches($arguments);
		}else{
   $request->getRandomMatches($arguments);
  }
	}

	public function deleteRequest($arguments){
	 $this->setRegion($arguments);
		$request = new Request();
  if($GLOBALS['city']=='unrecognized_region'){
   $request->delete($arguments,1);
		}else{
   $request->delete($arguments);
  }
	}
	
 public function getRequest($arguments){
	 //$this->initializeRegion($arguments);
		$request = new Request();
		$request->get($arguments);
}

 function setRegion($arguments){
  $userid = $arguments['user_id'];
  $c =new UserCity();
  $city = $c->getCity($userid);
  $this->setRegionVariables($city);
 }

 function initializeRegion($arguments){
  $region  = $this->detect_region($arguments);
  $this->setRegionVariables($region);
  $userid = $arguments['user_id'];
  //$c =new UserCity();
  //$city = $c->setCity($userid,$region);
 }

 function setRegionVariables($region){
  Logger::do_log("Setting up region as $region");
  $GLOBALS['city'] = $region;
  $GLOBALS['src_table'] = $region. '_src';
  $GLOBALS['dst_table'] = $region . '_dst';
  $GLOBALS['SOUTH'] = constant($region. '_SOUTH'); 
  $GLOBALS['NORTH'] = constant($region . '_NORTH');
  $GLOBALS['EAST'] = constant($region . '_EAST');
  $GLOBALS['WEST'] = constant($region . '_WEST');
  $GLOBALS['RADIUS'] = 500;
  $GLOBALS['RADIUS2'] = 0.005;
  $GLOBALS['DEGSTEP'] = 0.001;
  $GLOBALS['RADIUS_X'] = 112;
  $GLOBALS['RADIUS_Y'] = 105;
  $GLOBALS['THRESHOLD'] = 20;
  $GLOBALS['TIME_THRESHOLD'] = 3600*10;
 }
 
 function detect_region($arguments){
  if(!isset($arguments['src_latitude']) || !isset($arguments['src_longitude']) || !isset($arguments['src_latitude']) || !isset($arguments['src_longitude'])){
   return 'unrecognized_region';
  }
  $regions = explode(",",constant('RECOGNIZED_CITIES'));
  foreach($regions as $region){
   if($this->contains($arguments['src_latitude'],constant($region . '_NORTH'),constant($region . '_SOUTH')) && $this->contains($arguments['dst_latitude'],constant($region . '_NORTH'),constant($region . '_SOUTH'))  && $this->contains($arguments['src_longitude'],constant($region . '_EAST'),constant($region . '_WEST')) && $this->contains($arguments['dst_longitude'],constant($region . '_EAST'),constant($region . '_WEST'))){
    return $region;
   }
  }
  return 'unrecognized_region';
 }

 function contains($x, $y, $z){
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
 }

}


?>
