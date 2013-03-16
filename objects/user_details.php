<?php
require_once('objects/dbclass.php');
require_once('objects/field.php');
require_once('objects/exception.php');

class UserDetails extends dbclass{
	
	var $fields;
	
	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1);
		$this->fields['user_id'] = new Field('user_id','user_id',0); // Foreign key from user table
		$this->fields['firstname'] = new Field('firstname','firstname',0);
		$this->fields['lastname'] = new Field('lastname','lastname',0);
		$this->fields['username'] = new Field('username','username',0);
		$this->fields['fbid'] = new Field('fbid','fbid',0);
		$this->fields['fbtoken'] = new Field('fbtoken','fbtoken',0);
		$this->fields['birthday'] = new Field('birthday','birthday',0);
		$this->fields['email'] = new Field('email','email',0);
		$this->fields['workplace'] = new Field('workplace','workplace',0);
		$this->fields['phone'] = new Field('phone','phone',0);
		
	}

	function add($arguments){
		if(!isset($arguments['user_id'])){
			throw new APIException(array("code" =>"3", 'field'=>'user_id', 'error' => 'Field user_id is not set'));
		}
		$result = parent::select('user_details', array('id'),array('user_id' => $arguments['user_id'])); // check if incoming user if exists
		if(isset($result[0]['id'])){
   $toupdate  = 0;
   foreach($this->fields as $field){
    if($field->readonly == 0 && isset($arguments[$field->name])){
     $this->fields[$field->name]->value = $arguments[$field->name];
     $toupdate = 1;
    }
   }
   if($toupdate ==1 ) {
    parent::update('user_details',$this->fields,array('id' => $result[0]['id']));
		 }
   $json_msg = new JSONMessage();
   $json_msg->setBody(array("Status" => "Success"));
   echo $json_msg->getMessage();
   if($toupdate==1){
     Logger::do_log("Calling facebook_details.php");    
     $user_id = $arguments['user_id'];
     $fbid = $arguments['fbid'];
     $fbtoken = $arguments['fbtoken'];
     $cmd = "/usr/bin/php objects/facebook_details.php $user_id $fbid $fbtoken > /dev/null &";
     exec($cmd , &$output, &$ret);
     Logger::do_log("$cmd => Retured: $ret");
   }
   return;
		}
		foreach($this->fields as $field){
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		$id = parent::insert('user_details',$this->fields);
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("Status" => "Success"));
		echo $json_msg->getMessage();
     $user_id = $arguments['user_id'];
     $fbid = $arguments['fbid'];
     $fbtoken = $arguments['fbtoken'];
     $cmd = "/usr/bin/php objects/facebook_details.php $user_id $fbid $fbtoken > /dev/null &";
     exec($cmd , &$output, &$ret);
     Logger::do_log("$cmd => Retured: $ret");
	}

function get($arguments){
 if(!isset($arguments['user_id']) && !isset($arguments['fbid'])){
		throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
 }
 if(isset($arguments['user_id'])){
  $sql = "select * from user_details where user_id = " . $arguments['user_id'];
 }else{
  $sql = "select * from user_details where fbid = " . $arguments['fbid'];
 }
 $result = parent::execute($sql);
 $fb_array = array('fb_info_available' => 0);
 if($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
   $fbinfo = new FBInfo($row);
   $fb_array = $fbinfo->getData();
   $fb_array['fb_info_available'] = 1;
  }
	}
 $json_msg = new JSONMessage();
 $json_msg->setBody (array("fb_info" => $fb_array)); 
	echo $json_msg->getMessage();
}
	
}
