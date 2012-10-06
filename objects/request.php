<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');

class Request extends dbclass {

	var $fields;

	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1); 
		$this->fields['user_id'] = new Field('user_id','user_id',0); 
		$this->fields['src_lattitude'] = new Field('src_lattitude','src_lattitude',0);
		$this->fields['src_longitude'] = new Field('src_longitude','src_longitude',0);
		$this->fields['dst_lattitude'] = new Field('dst_lattitude','dst_lattitude',0);
		$this->fields['dst_longitude'] = new Field('dst_longitude','dst_longitude',0);
	}
	
	function add($arguments){
		if(!isset($arguments['user_id']) || !isset($arguments['src_lattitude']) || !isset($arguments['src_longitude']) || !isset($arguments['dst_lattitude']) || !isset($arguments['dst_longitude'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		foreach($this->fields as $field){
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		$result = parent::select('request',array('id'),array('user_id' => $arguments['user_id']));
		if(isset($result[0]['id'])){
			parent::update('request',$this->fields,array('user_id' => $arguments['user_id']));
		}else{
			parent::insert('request',$this->fields);
		}
		$result = parent::select('request',array('id'),array('user_id' => $arguments['user_id']));
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("request_id" => $result[0]['id']));
		echo $json_msg->getMessage();
	}	

}



?>
