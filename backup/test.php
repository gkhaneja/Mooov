<?php
require_once("Test/user.php");
require_once("Test/request.php");

$tests = array(
         array('uuid' => "test1", 'lat_src' => 19.000, 'lon_src'=>72.9, 'lat_src' => 19.046, 'lon_dst' => 72.8937, 'result' => "test2,test3"),
         array('uuid' => "test2", 'lat_src' => 19.0009, 'lon_src'=>72.9, 'lat_src' => 19.046, 'lon_dst' => 72.8937, 'result' => "test1,test3"),
         array('uuid' => "test3", 'lat_src' => 19.002, 'lon_src'=>72.9, 'lat_src' => 19.046, 'lon_dst' => 72.8937, 'result' => "test1,test2")
);

addUsers();

function addUsers(){
 print_r($tests);
}

function test3(){
 $request = new RequestTest();
 $request->get(39);
  
}

function test1(){
 $user = new UserTest();
 echo $user->addUser("test3") . "\n";
}

function test2(){
 $request = new RequestTest();
 $request->add(39,19.000,72.9,19.046,72.8937);
 $request->add(40,19.0009,72.9,19.046,72.8937);
 $request->add(41,19.002,72.9,19.046,72.8937);
}


?>
