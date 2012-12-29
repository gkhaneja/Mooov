<?php

require_once('conf/chat.conf');
require_once('objects/dbclass.php');
require_once('objects/JSONMessage.php');

class Chat extends dbclass {

 function __construct(){
                $this->fields = array();
                $this->fields['user_id'] = new Field('user_id','user_id',0);
                $this->fields['username'] = new Field('username','username',0);
                $this->fields['password'] = new Field('password','password',0);
}

function getURL()
{
return CHAT_SERVER_URL ;
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
 
public function generatePassword($length=6)
{     
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	return substr(str_shuffle($chars),0,$length);
}


public function  createUser($arguments){

$userid = $arguments['user_id'];
$username = $arguments['username'];

if(!isset($userid) || !isset($username))
{
//Throw exception
	  $error_m = new ExceptionHandler(array("code" =>"3" , 'error' => 'Required Fields are not set.'));
          echo $error_m->m_error->getMessage();

 	 return;
}

$result = parent::select('chat',array('userid','username','password'),array('userid' => $userid ));
if(isset($result[0]['userid'])){
                 $username = $result[0]['username'];
                 $password  = $result[0]['password'];
		 $json_msg = new JSONMessage();
                 $json_msg->setBody (array('user_id' => $userid, 'username'=> $username , 'password' => $password));
                 echo $json_msg->getMessage();
  return;
}

$password  = $this->generatePassword();
//$group = $arguments['city'];


$url = CHAT_SERVER_URL . "plugins/userService/userservice?type=add" . "&secret=". CHAT_SERVER_SECRET . "&username=" . $username ."&password=".$password;
$response = $this->get_data($url);

error_log(print_r($response,true));

$oXML = new SimpleXMLElement($response);

// get the root element 
$code = $oXML[0];
error_log($code);
if($code  ==  'ok')
{
		// Create user in database
		parent::execute("insert into chat (userid,username,password) values ('$userid','$username','$password')");                     

		 $json_msg = new JSONMessage();
                 $json_msg->setBody (array('user_id' => $userid, 'username'=> $username , 'password' => $password));
                 echo $json_msg->getMessage();


}
else
{
	  $error_m = new ExceptionHandler(array("code" =>"3" , 'error' => $code));
          echo $error_m->m_error->getMessage();
          return;

}

}

public function deleteUser($arguments){

$username = $arguments['username'];
$userid = $arguments['user_id'];
$url = CHAT_SERVER_URL . "plugins/userService/userservice?type=delete" . "&secret=". CHAT_SERVER_SECRET . "&username=" . $username;
$response = $this->get_data($url);

$oXML = new SimpleXMLElement($response);

// get the root element 
$code = $oXML[0];
error_log($code);
if($code  ==  'ok')
{
                // Create user in database
                parent::execute("delete from chat where username ='$username' or userid='$userid'");

                 $json_msg = new JSONMessage();
                 $json_msg->setBody(array("code" =>"0" , 'Status' => 'Success'));
                 echo $json_msg->getMessage();


}
else
{
          $error_m = new ExceptionHandler(array("code" =>"3" , 'error' => $code));
          echo $error_m->m_error->getMessage();
          return;

}


}

}



