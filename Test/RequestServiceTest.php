<?php

class RequestServiceTest {

var $tester;
var $fail;
var $pass;

public function RequestServiceTest($tester) {
 echo "Testing RequestService: \n";
 $this->tester = $tester;
 $this->pass=$this->fail=0;
 echo "Add Request: "; ($this->addRequestTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "Add Carpool: "; ($this->addCarpoolRequestTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "Get Carpool: "; ($this->getCarpoolMatchesTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "Get Mathces: "; ($this->getMatchesTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo "Del Request: "; ($this->deleteRequestTest() == 1) ? $this->pass++ : $this->fail++; echo "\n";
 echo $this->pass ."/". ($this->pass+$this->fail) . " tests passed\n";
 echo "\n";
}

function addRequestTest(){
 $pass=1;
 $this->tester->execute("DELETE from request where user_id = 15");
 $arguments = array (
                       'user_id'=>15,
                       'src_latitude'=>19.0,
                       'src_longitude'=>72.9,
                       'dst_latitude'=>19.046,
                       'dst_longitude'=>72.8937,
                       'uuid'=>"hopin"
              );
 $resp = $this->tester->Api("RequestService","addRequest",$arguments);
 $data = json_decode($resp,1);
 if(!isset($data['body']['user_id']) || $data['body']['user_id']!=15){ echo "0"; $pass=0;} else echo "1";
 if(!isset($data['body']['dst_locality']) || $data['body']['dst_locality']!="Chembur"){ echo "0"; $pass=0;} else echo "1";
 if(!isset($data['body']['src_locality']) || $data['body']['src_locality']!="Trombay"){ echo "0"; $pass=0;} else echo "1";

 $assertions = array( 
                       'user_id'=>15,
                       'src_latitude'=>19.0,
                       'src_longitude'=>72.9,
                       'dst_latitude'=>19.046,
                       'dst_longitude'=>72.8937
              );
 $pass = $this->tester->assertRow("select * from request where user_id=15", $assertions);
 return $pass;
}

function addCarpoolRequestTest(){
 $pass=1;
 $this->tester->execute("DELETE from carpool where user_id = 15");
 $arguments = array (
                       'user_id'=>15,
                       'src_latitude'=>19.0,
                       'src_longitude'=>72.9,
                       'dst_latitude'=>19.046,
                       'dst_longitude'=>72.8937,
                       'uuid'=>"hopin"
              );
 $resp = $this->tester->Api("RequestService","addCarpoolRequest",$arguments);
 $data = json_decode($resp,1);
 if(!isset($data['body']['user_id']) || $data['body']['user_id']!=15){ echo "0"; $pass=0;} else echo "1";
 if(!isset($data['body']['dst_locality']) || $data['body']['dst_locality']!="Chembur"){ echo "0"; $pass=0;} else echo "1";
 if(!isset($data['body']['src_locality']) || $data['body']['src_locality']!="Trombay"){ echo "0"; $pass=0;} else echo "1";
 $assertions = array( 
                       'user_id'=>15,
                       'src_latitude'=>19.0,
                       'src_longitude'=>72.9,
                       'dst_latitude'=>19.046,
                       'dst_longitude'=>72.8937
              );
 $pass = $this->tester->assertRow("select * from carpool where user_id=15", $assertions);
 return $pass;
}

function getCarpoolMatchesTest() {
 $pass=1;
 $arguments = array (
                   'site'=>1, 'uuid'=>"hopin",
                   'src_address' => "Central Avenue Road, Trombay, Mumbai, Maharashtra 400071, India",
                   'dst_address' => "Inlaks Hospital Road, Chembur, Mumbai, Maharashtra 400071, India"
              );
 $resp = $this->tester->Api("RequestService", "getCarpoolMatches", $arguments);
 $data = json_decode($resp, 1);
 if(!isset($data['body']['NearbyUsers']) || count($data['body']['NearbyUsers']) == 0 ) { echo "0"; $pass=0;} else echo "1";
 $found=0;
 foreach($data['body']['NearbyUsers'] as $match){
  if(isset($match['other_info']['user_id']) && $match['other_info']['user_id']==15) $found=1;
 }
 if($found == 0 ) { echo "0"; $pass=0;} else echo "1";
 return $pass;
}

function getMatchesTest() {
 $pass=1;
 $arguments = array (
                   'site'=>1, 'uuid'=>"hopin",
                   'src_latitude'=>19.0,
                   'src_longitude'=>72.9,
                   'dst_latitude'=>19.046,
                   'dst_longitude'=>72.8937
              );
 $resp = $this->tester->Api("RequestService", "getMatches", $arguments);
 $data = json_decode($resp, 1);
 if(!isset($data['body']['NearbyUsers']) || count($data['body']['NearbyUsers']) == 0 ) { echo "0"; $pass=0;} else echo "1";
 $found=0;
 $percent=0;
 foreach($data['body']['NearbyUsers'] as $match){
  if(isset($match['other_info']['user_id']) && $match['other_info']['user_id']==15){$found=1;$percent=$match['other_info']['percent_match'];}
 }
 if($found == 0 ) { echo "0"; $pass=0;} else echo "1";
 if($percent != 100 ) { echo "0"; $pass=0;} else echo "1";
 return $pass;
}

function deleteRequestTest(){
 $pass=1;
 $resp = $this->tester->Api("RequestService", "deleteRequest", array('user_id'=>15, 'uuid'=>"hopin", 'insta' => 0));
 $data = json_decode($resp, 1);
 if(!isset($data['body']['status']) || $data['body']['status'] != 0 ) { echo "0"; $pass=0;} else echo "1";
 $pass = $this->tester->assertRow("select count(*) as Cnt from carpool where user_id=15", array('Cnt' => 0));
 
 $resp = $this->tester->Api("RequestService", "deleteRequest", array('user_id'=>15, 'uuid'=>"hopin"));
 $data = json_decode($resp, 1);
 if(!isset($data['body']['status']) || $data['body']['status'] != 0 ) { echo "0"; $pass=0;} else echo "1";
 $pass = $this->tester->assertRow("select count(*) as Cnt from request where user_id=15", array('Cnt' => 0));
 return $pass;
}

}

?>
