<?php
require_once("Test/user.php");
require_once("Test/request.php");

test2();
//test3();

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
