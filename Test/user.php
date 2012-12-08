<?php

class UserTest {

var $add_url = "http://www.mooov.co.in/api/UserService/addUser/?";

function addUser($uuid){
 $ch = curl_init($this->add_url . "uuid=" . $uuid);
 curl_setopt($ch,CURLOPT_HEADER, 0);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $data = curl_exec($ch);
 $data = json_decode($data,true);
 curl_close($ch);
 return $data['body']['user_id'];
}

}

?>
