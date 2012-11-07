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

	function getNearbyRequests($arguments){
		if(!isset($arguments['user_id']) && !isset($arguments['id'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		if(!isset($arguments['id'])){
			$result = parent::select('request','*',array('user_id' => $arguments['user_id']));
		}else{
			$result = parent::select('request','*',array('id' => $arguments['id']));
	  }	
		if(count($result)==0){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Request does not exist.'));
			echo $error_m->m_error->getMessage();
			return;
		}
	  	
		$sql = "select r1.user_id, from request as r1, request as r2 where r1.src_lattitude<r2.src_lattitude+1 and r1.src_lattitude>r2.src_lattitude-1 and r1.src_longitude<r2.src_longitude+1 and r1.src_longitude>r2.src_longitude-1 and r1.dst_lattitude<r2.dst_lattitude+1 and r1.dst_lattitude>r2.dst_lattitude-1 and r1.dst_longitude<r2.dst_longitude+1 and r1.dst_longitude>r2.dst_longitude-1 and r2.user_id=" . $this->user_id;
		$result = parent::execute($sql); 
		$ret = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$ret[] = array("id" => stripslashes($row['user_id']), "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));
			}
		}
		else {
			return $ret;
		}
		return $ret;
	}
	
	function add($arguments){
		if(!isset($arguments['user_id']) || !isset($arguments['src_lattitude']) || !isset($arguments['src_longitude']) || !isset($arguments['dst_lattitude']) || !isset($arguments['dst_longitude'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		$result = parent::select('user',array('id'),array('id' => $arguments['user_id']));
		if(!isset($result[0]['id'])){
			$error_m = new ExceptionHandler(array("code" =>"5" , 'error' => 'User id does not exist.'));
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
