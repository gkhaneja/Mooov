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
                print_r($arguments['id']);
		if(!isset($arguments['user_id']) && !isset($arguments['id'])){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
			echo $error_m->m_error->getMessage();
			return;
		}
		if(!isset($arguments['id'])){
			$result = parent::select('request',array('*'),array('user_id' => $arguments['user_id']));
		}else{
			$result = parent::select('request',array('*'),array('id' => $arguments['id']));
	  }	
		if(count($result)==0){
			$error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Request does not exist.'));
			echo $error_m->m_error->getMessage();
			return;
		}
	  	
		$sql = "select r1.user_id from request as r1, request as r2 where r1.src_lattitude<r2.src_lattitude+1 and r1.src_lattitude>r2.src_lattitude-1 and r1.src_longitude<r2.src_longitude+1 and r1.src_longitude>r2.src_longitude-1 and r1.dst_lattitude<r2.dst_lattitude+1 and r1.dst_lattitude>r2.dst_lattitude-1 and r1.dst_longitude<r2.dst_longitude+1 and r1.dst_longitude>r2.dst_longitude-1 and r2.user_id=" . $arguments['user_id'];
		$result = parent::execute($sql); 
		$ret = array();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				error_log(print_r($row,true));
                                if($row['user_id'] == $arguments['user_id'])
                                      continue;
				$ret[] = stripslashes($row['user_id']);//array("id" => stripslashes($row['user_id']), "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));
			}
		}
                
                $resp = array();
                foreach($ret as $user)
                {
			$sql = "select * from user where id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                 	 $user_array = array("id" => stripslashes($row['id']), "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));             
                        }}                            
                        $sql = "select * from request where user_id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                         $loc_array = array("src_latitude" => stripslashes($row['src_lattitude']), "src_longitude" => stripslashes($row['src_longitude']), "dst_latitude" => $row['dst_lattitude'] , "dst_longitude" => $row['dst_longitude']);  
			 }}

                        array_merge($user_array , $loc_array);

                        $sql = "select * from user_details where user_id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                           $work_data =  unserialize($row['workplace']);
			   error_log(print_r($work_data,true));
                           $work_place  = $work_data[0]['employer']['name'];
                           $fb_array = array( "firstname" => stripslashes($row['firstname']), "lastname" => stripslashes($row['lastname']),
                              "works_at" => $work_place);  
                        }
	          }
                        $resp[] = array("loc_info" => $loc_array,  "fb_info" => $fb_array);
		}
                
                $json_msg = new JSONMessage();
                $json_msg->setBody (array("NearbyUsers" => $resp)); 
		echo $json_msg->getMessage();
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
