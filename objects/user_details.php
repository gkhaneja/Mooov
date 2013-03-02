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
		if(isset($result[0]['id']))
		{
                      $toupdate  = 0;
                      foreach($this->fields as $field){
                        if($field->readonly == 0 && isset($arguments[$field->name])){
                                $this->fields[$field->name]->value = $arguments[$field->name];
                                $toupdate = 1;
                             }
                          }
                        if($toupdate ==1 ) 
                        {
                        	parent::update('user_details',$this->fields,array('id' => $result[0]['id']));
                                 include('facebook_details.php');
		               
                        }
                        $json_msg = new JSONMessage();
                        $json_msg->setBody(array("Status" => "Success"));
                        echo $json_msg->getMessage();
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
                include('facebook_details.php');
               
	}
	
}
