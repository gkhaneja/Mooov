<?php
require_once('RestService.php');
require_once('objects/user_details.php');
require_once('objects/request.php');

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
  $request = new Request();
  if(!isset($arguments['user_id'])){
   throw new APIException(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
  }
  if(isset($arguments['carpool']) && $arguments['carpool']==1){
   $request->showCarpoolMatches(array($arguments['user_id']));
  }else{
   $request->showMatches(array(array('user_id' => $arguments['user_id'], 'percent' => 0)));
  }
 }
	
}
