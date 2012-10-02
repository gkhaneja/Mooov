<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');

class Request extends dbclass {

	var $fields;

	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',0,1); 
		$this->fields['user_id'] = new Field('user_id','user_id',1,0); 
		$this->fields['src_lattitude'] = new Field('src_lattitude','src_lattitude',0,0);
		$this->fields['src_longitude'] = new Field('src_longitude','src_longitude',0,0);
		$this->fields['dst_lattitude'] = new Field('dst_lattitude','dst_lattitude',0,0);
		$this->fields['dst_longitude'] = new Field('dst_longitude','dst_longitude',0,0);
	}
	
	function add($arguments){
		foreach($this->fields as $field){
			if($field->mandatory == 1 && !isset($arguments[$field->name])){
				$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Field ' . $field->name . ' is not set.'));
				echo $error_m->m_error->getMessage();
				return;
			}
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		parent::insert('request',$this->fields);
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("request_id" => array()));
		echo $json_msg->getMessage();
	}	

}



?>
