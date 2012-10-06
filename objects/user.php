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
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Field uuid is not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		foreach($this->fields as $field){
			if($field->readonly == 0 && isset($arguments[$field->name])){
				$this->fields[$field->name]->value = $arguments[$field->name];
			}
		}
		$id = parent::insert('user',$this->fields);
		$json_msg = new JSONMessage();
		$json_msg->setBody(array("user_id" => $id));
		echo $json_msg->getMessage();
	}	

	function getUsers(){
		$sql = "select r1.user_id, u.first_name, u.last_name from request as r1, request as r2, user as u where r1.src_lattitude<r2.src_lattitude+1 and r1.src_lattitude>r2.src_lattitude-1 and r1.src_longitude<r2.src_longitude+1 and r1.src_longitude>r2.src_longitude-1 and r1.dst_lattitude<r2.dst_lattitude+1 and r1.dst_lattitude>r2.dst_lattitude-1 and r1.dst_longitude<r2.dst_longitude+1 and r1.dst_longitude>r2.dst_longitude-1 and r1.user_id!=" . $this->user_id . " and r2.user_id=" . $this->user_id . " and r1.user_id=u.id";
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


}



?>
