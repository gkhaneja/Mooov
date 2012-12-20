<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');
require_once('objects/request.php');

class RequestService extends RestService {

	public function addRequest($arguments){
  $this->initializeRegion();
		$request = new Request();
		$request->add($arguments);
	}

	public function getNearbyRequests($arguments){
  $this->initializeRegion();
		$request = new Request();
		$request->getNearbyRequests($arguments);
	}

	public function deleteRequest($arguments){
  $this->initializeRegion();
		$request = new Request();
		$request->delete($arguments);
	}

 function initializeRegion(){
  Logger::do_log("Region detected as Mumbai");
  $GLOBALS['city'] = 'mumbai';
  $GLOBALS['src_table'] = 'mumbai_src';
  $GLOBALS['dst_table'] = 'mumbai_dst';
  $GLOBALS['SOUTH'] = 19.23000000;
  $GLOBALS['NORTH'] = 18.90000000;
  $GLOBALS['EAST'] = 72.95500000;
  $GLOBALS['WEST'] = 72.81670000;
  $GLOBALS['RADIUS'] = 112;
  $GLOBALS['DEGSTEP'] = 0.001;
  $GLOBALS['RADIUS_X'] = 112;
  $GLOBALS['RADIUS_Y'] = 105;
 }

}


?>
