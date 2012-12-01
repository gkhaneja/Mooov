<?php

require_once("objects/request.php");
require_once("objects/dbclass.php");
require_once("objects/logger.php");

		Logger::bootup();
		Logger::do_log("URL recieved: Testing Mumbai");
		$dbobject = new dbclass();
		$dbobject->connect();
$request = new Request();
$arg = array('user_id'=>3, 'src_latitude'=>19.008, 'src_longitude'=>72.908, 'dst_latitude'=>19.008, 'dst_longitude'=>72.908);
$request->add($arg);

?>
