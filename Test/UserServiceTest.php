<?php

class UserServiceTest {

var $tester;
var $fail;
var $pass;

public function UserServiceTest($tester) {
 echo "Testing UserService: \n";
 $this->tester = $tester;
 echo "Add User: "; ($this->addUserTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo $this->pass ."/". ($this->pass+$this->fail) . " tests passed\n";
 echo "\n";
}

function addUserTest(){
 $pass=1;
 $resp = $this->tester->Api('UserService', 'addUser', array('uuid' => 'test'));
 $data = json_decode($resp,1);
 if(isset($data['body']['user_id'])){
  echo "1";
  $id = $data['body']['user_id'];
  $pass = $this->tester->assertRow("select * from user where id=$id", array('uuid'=>"test", 'id'=>$id));
 }else{
  echo "1"; $pass=0;
 }
 return $pass;
}

}

?>
