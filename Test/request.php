<?php

class RequestTest {

var $get_url = "http://www.mooov.co.in/api/RequestService/getNearbyRequests/?";
var $add_url = "http://www.mooov.co.in/api/RequestService/addRequest/?";
var $delete_url = "http://www.mooov.co.in/api/RequestService/deleteRequest/?";

function add($user_id, $lat_src, $lon_src, $lat_dst, $lon_dst){
 $ch = curl_init($this->add_url . "user_id=" . $user_id . "&src_latitude=" . $lat_src . "&src_longitude=" . $lon_src . "&dst_latitude=" . $lat_dst . "&dst_longitude=" . $lon_dst . "&type=0");
 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $data = curl_exec($ch);
 $data = json_decode($data,true);
 curl_close($ch);
 print_r($data);
}

function get($user_id){
 $ch = curl_init($this->get_url . "user_id=" . $user_id);
 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $data = curl_exec($ch);
 $data = json_decode($data,true);
 curl_close($ch);
 print_r($data);
}

}
?>
