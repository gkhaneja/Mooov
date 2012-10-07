<?php
require_once('RestService.php');
require_once('objects/user_details.php');

class UserDetailsService extends RestService
{
	
	public function saveFBInfo($arguments)
	{
		$user_details = new UserDetails();
		$user_details->add($arguments);
	}
	
	
	
}