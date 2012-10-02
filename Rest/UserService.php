<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/user.php');

class UserService extends RestService {

	public function getNearbyUsers($arguments){
		$user = new User();
		//echo "got - " . $user_id;
		$user->user_id = $arguments['user_id'];
		$users = $user->getUsers();
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("NearbyUsers" => $users));
		echo $json_msg->getMessage();
	}

	public function addUser($arguments){
		$user = new User();
		$user->add($arguments);
	}

}


?>
