<?php

class UserDetailsServiceTest {

var $tester;
var $fail;
var $pass;
var $token;

public function UserDetailsServiceTest($tester, $token) {
 echo "Testing UserDetailsService: \n";
 $this->tester = $tester;
 $this->token = $token;
 $this->fail=$this->pass=0;
 
 echo "saveFBInfo: "; ($this->saveFBInfoTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "getFBInfo: "; ($this->getFBInfoTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "getInfo: "; ($this->getInfoTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo $this->pass ."/". ($this->pass+$this->fail) . " tests passed\n";
 echo "\n";
}

function saveFBInfoTest() {
 $pass=1;
 $this->tester->execute("Insert IGNORE into user (id,uuid) values (15,'userdetailstest')");
 $resp = $this->tester->Api("UserDetailsService","saveFBInfo",array('user_id'=>15,'fbid'=>742258029,'fbtoken'=>$this->token,'uuid'=>"hopin"));
 $data = json_decode($resp,1);
 if(!isset($data['body']['Status']) || $data['body']['Status']!='Success'){ echo "0"; $pass=0;} else echo "1";
 $assertions = array(
                 'firstname'=>'Gourav', 'lastname'=>'Khaneja', 'email'=>"gourav.khaneja@gmail.com",'gender'=>"male"
               );
 sleep(1);
 $pass = $this->tester->assertRow("select * from user_details where user_id=15", $assertions);
 return $pass;
}

function getFBInfoTest(){
 $pass=1;
 $resp = $this->tester->Api("UserDetailsService","getFBInfo",array('user_id'=>15,'uuid'=>"hopin"));
 $data = json_decode($resp,1);
 if(!isset($data['body']['fb_info']['fbid']) || $data['body']['fb_info']['fbid']!=742258029){echo "0"; $pass=0;} else echo "1";
 $this->tester->execute("Delete from user_details where user_id=15");
 $resp = $this->tester->Api("UserDetailsService","getFBInfo",array('user_id'=>15,'uuid'=>"hopin"));
 $data = json_decode($resp,1);
 if(!isset($data['body']['fb_info']['fb_info_available']) || $data['body']['fb_info']['fb_info_available']!=0){echo "0"; $pass=0;} else echo "1";
 return $pass;
}

function getInfoTest(){
 $pass=1;

 $this->tester->execute("INSERT IGNORE INTO request (user_id, src_latitude, src_longitude, dst_latitude, dst_longitude, src_locality, src_address, dst_locality, dst_address, route_id, time, city, women, facebook) VALUES (\"15\", \"19.0\", \"72.9\", \"19.046\", \"72.8937\", \"Trombay\", \"xyz\", \"Chembur\", \"abc\", \"13\", \"2013-05-22 02:33:55\", \"mumbai\", \"0\", \"0\")");

 $resp = $this->tester->Api("UserDetailsService","getInfo",array('user_id'=>15, 'uuid'=>"hopin"));
 $data = json_decode($resp, 1);
 if(!isset($data['body']['NearbyUsers'][0]['other_info']['user_id']) || $data['body']['NearbyUsers'][0]['other_info']['user_id']!=15){ echo "0"; $pass=0;} else echo "1";

 $this->tester->execute("INSERT IGNORE INTO carpool (user_id, src_latitude, src_longitude, dst_latitude, dst_longitude, src_address, dst_address, time, src_locality, dst_locality, women, facebook) VALUES (15, 19.0, 72.9, 19.046, 72.8937, \"xyz\", \"abc\", \"2013-05-22 03:00:35\", \"Trombay\", \"Chembur\", 0, 0)"); 
 $resp = $this->tester->Api("UserDetailsService","getInfo",array('user_id'=>15, 'uuid'=>"hopin", 'insta'=>0));
 $data = json_decode($resp, 1);
 if(!isset($data['body']['NearbyUsers'][0]['other_info']['user_id']) || $data['body']['NearbyUsers'][0]['other_info']['user_id']!=15){ echo "0"; $pass=0;} else echo "1";

 return $pass; 
}

function saveFeedBackTest(){
 $pass=1;
 //$this->tester->Api("UserDetailsService", "saveFeedBack", array('user_id'=>15,'uuid'=>"hopin",'feedback'=>"Hot Regression baby."));
 
}

}

?>
