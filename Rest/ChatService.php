<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('RestService.php');
require_once('objects/chat.php');

class ChatService extends RestService {


	public function createUser($arguments){
		$user = new Chat();
		$user->createUser($arguments);
	}

	 public function deleteUser($arguments){
                $user = new Chat();
                $user->deleteUser($arguments);
        }
        
}


?>
