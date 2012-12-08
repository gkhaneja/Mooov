<?php
require_once("Test/user.php");
require_once("Test/request.php");

//test1();
test2();

function test2(){
 $request = new RequestTest();
 $request->get(39);
 
}

function test1(){
 $user = new UserTest();
 echo $user->addUser("test3") . "\n";

 $request = new RequestTest();
 $request->add(41,19.000,72.9,19.046,72.8937);
}


?>
