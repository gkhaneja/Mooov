<?php
require_once '../objects/JSONMessage.php';

$users =  array(array('userid' => 1 , 'lat' => 55), array('userid' => 2, 'lat' => 56));
$body = array('NearbyUsers' => $users);
$error = array ('code' => '1' , 'error' => 'No results found');
$header = array();

$jsonm = new JSONMessage();
$jsonm->setBody($body);
$jsonm->setError($error);

echo $jsonm->getMessage();

$jsonm1 = new JSONMessage();
$jsonm1->setBody($body);


echo $jsonm1->getMessage();

?>