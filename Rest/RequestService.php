<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');

class RequestService extends RestService {

	public function addRequest($arguments){
		$request = new Request();
		$request->add($arguments);
	}

}


?>
