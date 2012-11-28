<?php

require_once("objects/request.php");
require_once("objects/dbclass.php");
require_once("objects/logger.php");

		Logger::bootup();
		Logger::do_log("URL recieved: Testing Mumbai");
		$dbobject = new dbclass();
		$dbobject->connect();
$request = new Request();
//$arg = array('user_id'=>2, 'src_lattitude'=>19.001, 'src_longitude'=>72.90, 'dst_lattitude'=>19.005, 'dst_longitude'=>72.905);
$arg = array('user_id'=>1);
print_r($request->getNearbyRequests($arg));

?>
