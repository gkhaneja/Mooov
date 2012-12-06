<?php

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "/var/www/html");
require_once('objects/site_request.php');
require_once('objects/dbclass.php');


$dbobject = new dbclass();
$dbobject->connect();


$source = $_GET['source'];
$destination  = $_GET['dest'];
error_log($source ."-" . $destination);
$source = str_replace('(', '', $source);
$source = str_replace(')', '', $source);
$source = str_replace(' ', '', $source);

$destination = str_replace('(', '', $destination);
$destination = str_replace(')', '', $destination);
$destination = str_replace(' ', '', $destination);

$src_array =  explode(',',$source);
$dst_array =  explode(',',$destination);

$location=array('src_latitude' => $src_array[0], 'src_longitude' => $src_array[1], 'dst_latitude' => $dst_array[0], 'dst_longitude' => $dst_array[1]);
error_log(print_r($location,true));
$request = new Request();
$request->getNearbyRequests($location);
?>
