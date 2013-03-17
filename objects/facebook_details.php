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

function getMutualFriends($id, $token, $id2){
 $fburl = "https://graph.facebook.com/";
 $fburl = $fburl . $id . "/mutualfriends/" . $id2 . "?access_token=" . $token;
 Logger::do_log("== $fburl ==");
 $resp =  get_data($fburl);
 $data  = parseResponse($resp);
 if($data == "NULL") return false;
 $friends = $data['data'];
 if(!isset($friends) || empty($friends)){
  Logger::do_log("Cannot fetch mutual friends for $id and $id2 " . print_r($data,true));
  return false;
 }
 return $friends;
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
 $friends = $data['friends']['data'];
 if(!isset($friends) || empty($friends)){
  Logger::do_log("Cannot fetch friends " . print_r($data,true));
  exit(0);
 }
 if(count($friends)==0) exit(0);
 //$dbobject->execute("Delete from friends");
 
 $sql = "Insert IGNORE into friends (fbid1, fbid2, name1, name2) values"; 
 $first=1;
 $friends_ids = array();
 foreach($friends as $friend){
  $fbid2 = $friend['id'];
  $name2 = $friend['name'];
  $name = $fname . " " . $lname;
  $friends_ids[] = $fbid2;
  if($first==1){
   $sql .= " ($fbid, $fbid2, '$name', '$name2')";
   $first=0; 
  }else{
   $sql .= ", ($fbid, $fbid2, '$name', '$name2')"; 
  }
 }
 if($first==0) $dbobject->execute($sql);
 
 $result = $dbobject->execute("Select distinct fbid from user_details");
 if($result->num_rows > 0){
  $conn_sql = "Insert IGNORE into connections (fbid1, fbid2, path, mutual_friends_count, friends) values";
  $conn_first=1;
  while($row = $result->fetch_assoc()){
   $fbid2 = $row['fbid'];
   if(!isset($fbid2) || $fbid==$fbid2) continue;
   $mutual_friends = getMutualFriends($fbid, $fbtoken, $row['fbid']);
   if($mutual_friends!==false){
    $path = serialize(array_slice($mutual_friends, 0 , 5));
    $mutual_friends_count = count($mutual_friends);
   }else{
    $path = serialize(array());
    $mutual_friends_count = 0;
   }
   $is_friends = 0;
   if(in_array($row['fbid'],$friends_ids)){
    $is_friends=1;
   }
   if($is_friends==1 || $mutual_friends!==false){
    if($conn_first==1){
     $conn_sql .= " ($fbid, $fbid2, '$path', $mutual_friends_count,$is_friends), ($fbid2, $fbid, '$path', $mutual_friends_count, $is_friends)";
     $conn_first=0; 
    }else{
     $conn_sql .= ",($fbid, $fbid2, '$path',$mutual_friends_count,$is_friends), ($fbid2, $fbid, '$path', $mutual_friends_count,$is_friends)"; 
    }
   }    
  }
  if($conn_first==0) $dbobject->execute($conn_sql);
 }




?>
