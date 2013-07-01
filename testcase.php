<?php
require_once('objects/dbclass.php');
require_once('objects/logger.php');

	Logger::bootup();
	if(!isset($_POST['testcase']) || !isset($_POST['name']) || !isset($_POST['email'])){
		echo "Not Cool";
	}
	error_log(print_r($_POST,true));
	$case = $_POST['testcase'];
	$name = $_POST['name'];
	$email = $_POST['email'];

	$dbobj = new dbclass();
	$dbobj->connect();
	$dbobj->util_execute("Insert INTO testcase (name, email, tcase) values ('$name', '$email', '$case')");
	
	echo "Cool";
?>
