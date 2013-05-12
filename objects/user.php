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
		$this->fields['username'] = new Field('username','username',0);
		$this->fields['uuid'] = new Field('uuid','uuid',0);
		$this->fields['fbid'] = new Field('fbid','fbid',0);
	}

	function add($arguments){
  $primary = 'uuid';
		if(!isset($arguments['uuid']) && !isset($arguments['fbid'])){
			throw new APIException(array("code" =>"3" ,'field'=>'uuid/fbid', 'error' => 'Field uuid is not set'));
		}
		if(!isset($arguments['uuid']) && isset($arguments['fbid'])){
   $primary = 'fbid'; 
		}
		if(isset($arguments['uuid']) && !isset($arguments['fbid'])){
   $primary = 'uuid';
  }
		if(isset($arguments['uuid']) && isset($arguments['fbid'])){
		 //$uuid = parent::select('user', array('id'),array('uuid' => $arguments['uuid']));
		 //$fbid = parent::select('user', array('id'),array('fbid' => $arguments['fbid']));
			throw new APIException(array("code" =>"3" ,'field'=>'uuid and fbid', 'error' => 'Too many fields set :P'));
  }

		$result = parent::select('user', array('id'),array($primary => $arguments[$primary]));
		if(isset($result[0]['id'])){
			$json_msg = new JSONMessage();
			$json_msg->setBody(array("user_id" => $result[0]['id']));
			echo $json_msg->getMessage();
		}else{	
		 foreach($this->fields as $field){
			 if($field->readonly == 0 && isset($arguments[$field->name])){
				 $this->fields[$field->name]->value = $arguments[$field->name];
			 }
		 }
		 parent::insert('user',$this->fields);
		 $result = parent::select('user', array('id'),array($primary => $arguments[$primary]));
		 $json_msg = new JSONMessage();
		 $json_msg->setBody(array("user_id" => $result[0]['id']));
		 echo $json_msg->getMessage();
  }
  return;
	}	

 function get($arguments){
  $primary='uuid';
  if(!isset($arguments['uuid']) && !isset($arguments['fbid'])){
		 throw new APIException(array("code" =>"3" ,'field'=>'uuid/fbid', 'error' => 'Field uuid/fbid is not set'));
  }
		if(!isset($arguments['uuid']) && isset($arguments['fbid'])){
   $primary = 'fbid'; 
		}
		if(isset($arguments['uuid']) && !isset($arguments['fbid'])){
   $primary = 'uuid';
  }
  $result = parent::select('user', array('id'),array($primary => $arguments[$primary]));
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
