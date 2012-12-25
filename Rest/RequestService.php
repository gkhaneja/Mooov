<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');
require_once('objects/request.php');
require_once('objects/decode_city.php');
require_once('conf/constants.inc');

class RequestService extends RestService {

	public function addRequest($arguments){
  $this->initializeRegion();
		$request = new Request();
		$request->add($arguments);
	}

	public function getNearbyRequests($arguments){
	        $this->initializeRegion($arguments);
		$request = new Request();
		$request->getNearbyRequests($arguments);
	}

	public function deleteRequest($arguments){
	        $this->initializeRegion($arguments);
		$request = new Request();
		$request->delete($arguments);
	}

 function initializeRegion($arguments){
  $region  = $this->detect_region($arguments);
  error_log("Region detected : $region ");
  Logger::do_log("Region detected as $region");
  $GLOBALS['city'] = $region;
  $GLOBALS['src_table'] = $region. '_src';
  $GLOBALS['dst_table'] = $region . '_dst';
  $GLOBALS['SOUTH'] = constant($region._SOUTH); 
  $GLOBALS['NORTH'] = constant($region . _NORTH);
  $GLOBALS['EAST'] = constant($region . _EAST);
  $GLOBALS['WEST'] = constant($region . _WEST);
  Logger::do_log("South - " . $GLOBALS['SOUTH'] . " " . mumbai_SOUTH);
  $GLOBALS['RADIUS'] = 112;
  $GLOBALS['DEGSTEP'] = 0.001;
  $GLOBALS['RADIUS_X'] = 112;
  $GLOBALS['RADIUS_Y'] = 105;
  $GLOBALS['THRESHOLD'] = 20;
 }
 
 function detect_region($arguments){
  $userid = $arguments['user_id'];
  $c =new UserCity();
  $city = $c->getCity($userid);
  if(!empty($city))
    return  strtolower($city);

  error_log("City not found");
  return 'mumbai';
 }

}


?>
