<?php


function get_data($url)
{
$ch = curl_init();
$timeout = 5;
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
$data = curl_exec($ch);
curl_close($ch);
//print $data;
return $data;
}


function getUserPresence($jid){

$cmd = "http://54.243.171.212:9090/plugins/presence/status?jid=" .  $jid . "@54.243.171.212&type=text";
$response  = get_data($cmd);

if(stristr($response ,'Unavailable') !== FALSE)
 return  0;
else 
 return 1;
}


