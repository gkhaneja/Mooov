<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');

class UserService extends RestService {


	public function addUser($arguments){
		$user = new User();
		$user->add($arguments);
	}

}


?>
