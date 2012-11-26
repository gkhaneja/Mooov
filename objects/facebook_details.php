<?php


function getFBquery($id,$token)
{
$fbURL = "https://graph.facebook.com/";
return $fbURL . $id . "?access_token=" . $token;
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

if(!isset($arguments['user_id'])| !isset($arguments['fbtoken'])  || !isset($arguments['fbid']))
   return;
 
 $userid =  $arguments['user_id'];
 $fbid =  $arguments['fbid'];
 $fbtoken =  $arguments['fbtoken'];
 $fburl  =   getFBquery($fbid,$fbtoken);
 error_log("== $fburl ==");
 $resp =  get_data($fburl);
 $data  = parseResponse($resp);
 if($data == "NULL") return;
 error_log("======");
 error_log(print_r($data,true)); 
 $workplace  = serialize($data['work']);
 $fname =  $data['first_name'];
 $lname = $data['last_name'];
 $uname = $data['username'];
 $gender = $data['gender'];
 $email = $data['email'];
 
 $query = "UPDATE user_details SET  workplace = '$workplace', firstname = '$fname' , lastname = '$lname' , username ='$uname', gender='$gender' , email='$email' WHERE  user_id = $userid "; 
 error_log($query);
 error_log( parent::execute($query));

?>
