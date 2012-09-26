<?php
require_once("/home/gourav/server/trunk/Server/autoload.php");

class UserService extends RestService {

	public function getNearbyUsers($user_id){
		$user = new user();
		//echo "got - " . $user_id;
		$user->user_id = $user_id;
		$users = $user->getUsers();
		$ret = json_encode($users);
		echo $ret;
	}

}


?>
