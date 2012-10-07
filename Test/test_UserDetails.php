<?php 
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "/home/rahul/Documents/Mooov/trunk");
require_once ('Rest/UserDetailsService.php');
require_once('objects/dbclass.php');

Logger::bootup();
$dbobject = new dbclass();
$dbobject->connect();

$arguments = array(
		'user_id' => 1,
		'firstname' => 'John',
		'lastname' =>'Doe',
		'email' => 'john.doe@gmail.com',
		'fbid' =>'567445',
		'fbtoken' => 'dhljlsf379397bsvkjhkdfsc',
		'birthday' => date('Y-m-d'),
		'workplace' => 'TOP',
		'username' => ''
		);
$service = new UserDetailsService();
$service->saveFBInfo($arguments);
?>