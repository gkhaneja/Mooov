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
 Logger::do_log("== $fburl ==");
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
 $education  =addslashes(serialize ($data['education'])); 
 $hometown  =  serialize($data['hometown']);
 $location =  serialize($data['location']);
 $query = "UPDATE user_details SET  workplace = '$workplace', firstname = '$fname' , lastname = '$lname' , username ='$uname', gender='$gender' , email='$email', location = '$location', hometown = '$hometown', education ='$education'  WHERE  user_id = $userid "; 
 //Logger::do_log($query);
 parent::execute($query);

?>
