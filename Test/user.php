<?php

class UserTest {

var $add_url = "http://www.mooov.co.in/api/UserService/addUser/?";
var $get_url = "http://www.mooov.co.in/api/UserService/getUserID/?";

function addUser($uuid){
 $ch = curl_init($this->add_url . "uuid=" . $uuid);
 curl_setopt($ch,CURLOPT_HEADER, 0);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $data = curl_exec($ch);
 $data = json_decode($data,true);
 curl_close($ch);
 return $data;
}

function getUserID($uuid){
 $ch = curl_init($this->get_url . "uuid=" . $uuid);
 curl_setopt($ch,CURLOPT_HEADER, 0);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $data = curl_exec($ch);
 $data = json_decode($data,true);
 curl_close($ch);
 return $data;
}

}

?>
