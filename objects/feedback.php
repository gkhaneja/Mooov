<?php
require_once('objects/dbclass.php');
require_once('objects/field.php');
require_once('objects/exception.php');

class Feedback  extends dbclass{
	
	var $fields;
	
	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1);
		$this->fields['userid'] = new Field('userid','userid',0); // Foreign key from user table
		$this->fields['username'] = new Field('username','username',0);
		$this->fields['email'] = new Field('email','email',0);
		$this->fields['feedback'] = new Field('feedback','feedback',0);
		
	}

	function savefeedback($arguments){
		if(!isset($arguments['userid']) && !isset($arguments['username']) && !isset($arguments['email'])){
			throw new APIException(array("code" =>"3", 'field'=>'user_id', 'error' => 'Any of the required fields not set'));
		}
	 
  $feedback = $arguments['feedback'];
  $userid = $arguments['userid'];
  $username = $arguments['username'];
  $email = $arguments['email'];
 
  if(isset($userid)) 
   $query = "insert into feedback (userid,username,email,feedback) values ( $userid, '$username' , '$email' , '$feedback')";
  else
   $query = "insert into feedback (username,email,feedback) values (  '$username' , '$email' , '$feedback')";
   

  parent::execute($query); 
                 $json_msg = new JSONMessage();
                 $json_msg->setBody(array("code" =>"0" , 'Status' => 'Success'));
                 echo $json_msg->getMessage();

}
}
