<?php

require_once('objects/logger.php');
require_once('objects/dbclass.php');


function getFBquery($id,$token)
{
$fbURL = "https://graph.facebook.com/";
return $fbURL . $id . "?access_token=" . $token;
}

function getFriendsquery($id, $token){
 $fbURL = "https://graph.facebook.com/";
 return $fbURL . $id . "?fields=friends&access_token=" . $token;
}

function get_data($url)
{
$ch = curl_init();
$timeout = 5;
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
$data = curl_exec($ch);
curl_close($ch);
return $data;
}

function parseResponse($json)
{
$array = json_decode($json,true);// parse the associative array
//var_dump($array);
if(empty($array) || (!empty($array) && !empty($array->error)))
  return "NULL";
else
  return $array;
}
		Logger::bootup();
		Logger::do_log("facebook details: ");
		$dbobject = new dbclass();
		$dbobject->connect();
  //Logger::do_log( getcwd() . "exiting"); exit(0);
  //Logger::do_log(print_r($argv,true)); exit(0);
//while(1){Logger::do_log("waiting.."); sleep(5);}
 $userid = $argv[1];
 $fbid = $argv[2];
 $fbtoken = $argv[3];

if(!isset($userid) || !isset($fbtoken)  || !isset($fbid)){
   Logger::do_log("Not enough params. Returning.");
   exit(0);
}

 
 $fburl  =   getFBquery($fbid,$fbtoken);
 Logger::do_log("== $fburl ==");
 $resp =  get_data($fburl);
 $data  = parseResponse($resp);
 if($data == "NULL") return;
 if(!isset($data['first_name'])){
  Logger::do_log("Cannot fetch FB Info " . print_r($data,true));
  exit(0);
 }
 //error_log("======");
 //Logger::do_log(print_r($data,true)); 
 $workplace  = serialize($data['work']);
 $fname =  $data['first_name'];
 $lname = $data['last_name'];
 $uname = $data['username'];
 $gender = $data['gender'];
 $email = $data['email'];
 $education  =addslashes(serialize ($data['education'])); 
 $hometown  =  serialize($data['hometown']);
 $location =  serialize($data['location']);
 $query = "UPDATE user_details SET  workplace = '$workplace', firstname = '$fname' , lastname = '$lname' , username ='$uname', gender='$gender' , email='$email', location = '$location', hometown = '$hometown', education ='$education'  WHERE  user_id = $userid "; 
 //Logger::do_log($query);
 $dbobject->execute($query);

 $fburl  =   getFriendsquery($fbid,$fbtoken);
 Logger::do_log("== $fburl ==");
 $resp =  get_data($fburl);
 $data  = parseResponse($resp);
 if($data == "NULL") return;
 //error_log("======");
 //Logger::do_log(print_r($data,true)); 
 $friends = $data['friends']['data'];
 if(!isset($friends) || empty($friends)){
  Logger::do_log("Cannot fetch friends " . print_r($data,true));
  exit(0);
 }
 if(count($friends)==0) exit(0);
 //$dbobject->execute("Delete from friends");
 $sql = "Insert IGNORE into friends (fbid1, fbid2, name1, name2) values"; 
 $first=1;
 $detail_sql = "Select distinct fbid from user_details where fbid in (";
 $detail_first = 1;
 foreach($friends as $friend){
  $fbid2 = $friend['id'];
  $name2 = $friend['name'];
  $name = $fname . " " . $lname;

  if($first==1){
   $sql .= " ($fbid, $fbid2, '$name', '$name2')";
   $first=0; 
  }else{
   $sql .= ", ($fbid, $fbid2, '$name', '$name2')"; 
  }

  if($detail_first==1){
   $detail_sql .= "$fbid2";
   $detail_first=0; 
  }else{
   $detail_sql .= ", $fbid2"; 
  }
 }
 if($first==0) $dbobject->execute($sql);

 if($detail_first==0){
  $detail_sql .= ")";
  $result = $dbobject->execute($detail_sql);
  if($result->num_rows > 0){
   $conn_sql = "Insert IGNORE into connections (fbid1, fbid2, path) values";
   $conn_first=1;
   while($row = $result->fetch_assoc()){
    $path1 ="r"; 
    $path2 ="r"; 
    $fbid2 = $row['fbid'];
    if($conn_first==1){
     $conn_sql .= " ($fbid, $fbid2, '$path1'), ($fbid2, $fbid, '$path2')";
     $conn_first=0; 
    }else{
     $conn_sql .= ", ($fbid, $fbid2, '$path1'), ($fbid2, $fbid, '$path2')"; 
    }
   }
   if($conn_first==0) $dbobject->execute($conn_sql);
  }
 }

?>
