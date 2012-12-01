<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');
require_once('objects/request.php');

class RequestService extends RestService {

	public function addRequest($arguments){
		$request = new Request();
		$request->add($arguments);
	}

	public function getNearbyRequests($arguments){
		$request = new Request();
		$request->getNearbyRequests($arguments);
	}

	public function deleteRequest($arguments){
		$request = new Request();
		$request->delete($arguments);
	}

}


?>
