<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');

class User extends dbclass {

	var $user_id;
	var $fields;

	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1); 
		$this->fields['first_name'] = new Field('first_name','first_name',0);
		$this->fields['last_name'] = new Field('last_name','last_name',0);
		$this->fields['username'] = new Field('username','username',0);
		$this->fields['uuid'] = new Field('uuid','uuid',0);
	}

	function add($arguments){
		if(!isset($arguments['uuid'])){
			throw new APIException(array("code" =>"3" ,'field'=>'uuid', 'error' => 'Field uuid is not set'));
		}
		$result = parent::select('user', array('id'),array('uuid' => $arguments['uuid']));
		if(isset($result[0]['id'])){
   if(isset($arguments['first_name']) || isset($arguments['last_name']) || isset($arguments['username'])){
    foreach($this->fields as $field){
     if($field->readonly == 0 && isset($arguments[$field->name])){
      $this->fields[$field->name]->value = $arguments[$field->name];
     }
		  }error_log("Updating");
    parent::update('user',$this->fields,array('id' => $result[0]['id']));
   }
			$json_msg = new JSONMessage();
			$json_msg->setBody(array("user_id" => $result[0]['id']));
			echo $json_msg->getMessage();
			return;
		}	
		foreach($this->fields as $field){
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		parent::insert('user',$this->fields);
		$result = parent::select('user', array('id'),array('uuid' => $arguments['uuid']));
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("user_id" => $result[0]['id']));
		echo $json_msg->getMessage();
	}	

 function get($arguments){
  if(!isset($arguments['uuid'])){
		 throw new APIException(array("code" =>"3" ,'field'=>'uuid', 'error' => 'Field uuid is not set'));
  }
  $result = parent::select('user', array('id'),array('uuid' => $arguments['uuid']));
  if(isset($result[0]['id'])){
   $json_msg = new JSONMessage();
   $json_msg->setBody(array("user_id" => $result[0]['id']));
   echo $json_msg->getMessage();
   return;
  }else{
		  throw new APIException(array("code" =>"5" ,'entity'=>'user', 'error' => 'User does not exist'));
  }


	}

}



?>
