<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/field.php');
require_once('objects/JSONMessage.php');
require_once('objects/mumbai.php');
require_once('objects/location_info.php');
require_once('objects/facebook_info.php');
class Request extends dbclass {

	var $fields;

	function __construct(){
		$this->fields = array();
		$this->fields['id'] = new Field('id','id',1); 
		$this->fields['user_id'] = new Field('user_id','user_id',0); 
		$this->fields['src_latitude'] = new Field('src_latitude','src_latitude',0);
		$this->fields['src_longitude'] = new Field('src_longitude','src_longitude',0);
		$this->fields['dst_latitude'] = new Field('dst_latitude','dst_latitude',0);
		$this->fields['dst_longitude'] = new Field('dst_longitude','dst_longitude',0);
		$this->fields['src_locality'] = new Field('src_locality','src_locality',0);
		$this->fields['src_address'] = new Field('src_address','src_address',0);
		$this->fields['dst_locality'] = new Field('dst_locality','dst_locality',0);
		$this->fields['dst_address'] = new Field('dst_longitude','dst_address',0);
	}

	function getNearbyRequests($arguments){
                //print_r($arguments['id']);
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
		//getting from mumbai table	
		$mumbai = new Mumbai();
		$matches = $mumbai->matchRequest($result[0]['user_id'], $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude']);
		//getting from mumbai table	
	        error_log("=== Matches ======" . print_r($matches,true));	
		$ret = array();
		
	
		foreach($matches as $row) {
				error_log(print_r($row,true));
                                if($row == $arguments['user_id'])
                                      continue;
				$ret[] = $row;//array("id" => stripslashes($row['user_id']), "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));
			
		}
                
                $resp = array();
				error_log(print_r($ret,true));
                foreach($ret as $user)
                {
                        $fb_array;
                        $user_array;
			$sql = "select * from user where id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                 	 $user_array = array("user_id" => $user, "first_name" => stripslashes($row['first_name']), "last_name" => stripslashes($row['last_name']));             
                        }}                            
                        $sql = "select * from request where user_id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                         $locinfo_src = new LocationInfo('src',$row);
                         $locinfo_dst = new LocationInfo('dst',$row);
                         $loc_array = array("src_info" => $locinfo_src->get(), "dst_info" => $locinfo_dst->get());
			 }}

                        $merg_array = array_merge($user_array , $loc_array);
                        $sql = "select * from user_details where user_id = $user";
                        $result = parent::execute($sql);
                        if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                                   $fbinfo = new FBInfo($row);
                                   $fb_array = $fbinfo->getData();
                        }
	          }
                        $resp[] = array("loc_info" => $merg_array,  "fb_info" => $fb_array);
		
		}                
                $json_msg = new JSONMessage();
                $json_msg->setBody (array("NearbyUsers" => $resp)); 
		echo $json_msg->getMessage();
	}

	
	function add($arguments){
		if(!isset($arguments['user_id']) || !isset($arguments['src_latitude']) || !isset($arguments['src_longitude']) || !isset($arguments['dst_latitude']) || !isset($arguments['dst_longitude'])){
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
	//Inserting into mumbai table
	$mumbai = new Mumbai();
	$mumbai->addRequest($arguments['user_id'], $arguments['src_latitude'], $arguments['src_longitude'], $arguments['dst_latitude'], $arguments['dst_longitude']);
	//Inserting into mumbai table
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
