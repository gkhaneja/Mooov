<?php

require_once('objects/dbclass.php');
require_once('objects/logger.php');

class GCM extends dbclass {

var $apiKey = "AIzaSyAuIjfQi0o2AALQ7SxaFlDWoUhZ6T0-WYM";
var $url = 'https://android.googleapis.com/gcm/send';

function GCM(){
}

function getRegID($user_ids){
 $reg_ids = array();
 $dbobj = new dbclass();
 $ids_str = implode(",",$user_ids);
 if(trim($ids_str)=="") return $reg_ids;
 $result = $dbobj->execute("select reg_id from user where id in ($ids_str)");
 if($result->num_rows == 0) return $reg_ids;
 while(($row = $result->fetch_assoc()) != NULL){
  $reg_ids[] = $row['reg_id'];
 }
 //$registrationIDs = array( "APA91bGbyjna16SdFBnGkL2s8XVSiC8t1fQbr-oVClraC7tB1J2oCUY68h6DyOvrmo3Uc8Ha7oxTFgls0uKNSi3KL-WwfLlcSB_cJDeXAQW7J_847H6NKOxxBUmzDDC5AWq2SPmT9flQwQHp1OMBSyD47Im3yNUTQQ" );
 return $reg_ids;
}

function sendMessage($user_ids, $message){
 //$message = "Test data for Hopin";
 $reg_ids = $this->getRegID($user_ids);
 if(empty($reg_ids)) return FALSE;

 $fields = array(
                'registration_ids'  => $reg_ids,
                'data'              => array( "message" => $message ),
                );
 $headers = array( 
                    'Authorization: key=' . $this->apiKey,
                    'Content-Type: application/json'
                );

 $ch = curl_init();
 curl_setopt( $ch, CURLOPT_URL, $this->url );
 curl_setopt( $ch, CURLOPT_POST, true );
 curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
 curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
 curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
 $result = curl_exec($ch);
 curl_close($ch);

 Logger::do_log($result);
 $resp = json_decode($result,true);
 if($resp['success']==1) return TRUE;
 return FALSE;
}


}
?>

