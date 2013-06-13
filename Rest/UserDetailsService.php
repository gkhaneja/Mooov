<?php
require_once('RestService.php');
require_once('objects/user_details.php');
require_once('objects/request.php');
require_once('objects/feedback.php');
require_once('objects/dbclass.php');

class UserDetailsService extends RestService
{
	
	public function saveFBInfo($arguments)
	{
		$user_details = new UserDetails();
		$user_details->add($arguments);
	}

 public function getFBInfo($arguments){
		$user_details = new UserDetails();
		$user_details->get($arguments); 
 }

 public function getInfo($arguments){
  $requestobj = new Request();
  if(!isset($arguments['target_user_id'])){
   throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
  }
  $dbobject = new dbclass();
  $sql = "select * from user_details where user_id = " . $arguments['target_user_id'];
  $result = $dbobject->execute($sql);
  if($result->num_rows > 0){
   $details = $result->fetch_assoc();
  }else{
   $details = array();
  }
  if(isset($arguments['insta']) && $arguments['insta'] == 0){
   $sql = "select * from carpool where user_id = " . $arguments['target_user_id'];
   $result = $dbobject->execute($sql);
   if($result->num_rows > 0){
    $request = $result->fetch_assoc();
   }else{
    throw new APIException(array("code" =>"2" , 'error' => 'No carpool requests exists'));
   }
   $requestobj->showMatches(array(array('match' => $arguments['target_user_id'], 'details' => $details, 'request' => $request)), 0, 1);
  }else{
   $sql = "select * from request where user_id = " . $arguments['target_user_id'];
   $result = $dbobject->execute($sql);
   if($result->num_rows > 0){
    $request = $result->fetch_assoc();
   }else{
    throw new APIException(array("code" =>"2" , 'error' => 'No Insta requests exists'));
   }
   $requestobj->showMatches(array(array('match' => $arguments['target_user_id'], 'percent'=>100,'details' => $details, 'request' => $request)), 0, 0);
  }
 }

 public function saveFeedBack($arguments)
 {
  $feedback = new Feedback();
  $feedback->savefeedback($arguments);

 }	
}
