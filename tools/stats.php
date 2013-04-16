<?php

require_once('objects/dbclass.php');
//require_once('tools/statistics.php');


class Stats extends dbclass {

function Stats(){
	$dbobject = new dbclass();
	$dbobject->connect();
 $this->DailyStats();
}

function WriteData(){
 $sql = "select  u.user_id, u.firstname, u.lastname, u.fbid, count(r.user_id) from request as r, user_details as u where u.user_id=r.user_id group by u.fbid";
 $result = $dbobject->execute($sql);
}

function DailyStats(){
 $date1 = date('Y-m-d', time() - 7*24*60*60) . " 00:00:00";
 $date2 = date('Y-m-d', time() - 1*24860*60) . " 23:59:59";
 //$date = date('Y-m-d', time() - 24*60*60);
 
 $sql = "select * from user where lastupd>'$date1' and lastupd<'$date2'";
 $result = parent::execute($sql);
 $users = array();
 $users_str = "";
 $first=1;
 while(($row = $result->fetch_assoc())!=NULL){
  $users[$row['id']] = $row;
  if($first==1){
   $users_str = $row['id'];
   $first=0;
  }else{
   $users_str = $users_str . " ," . $row['id'];
  }
 }
 $fbcount=0;
 if(count($users)>0){
  $sql = "select user_id, fbid, firstname, lastname, hometown from user_details where user_id in ($users_str)";
  $result = parent::execute($sql);
  $fbcount = $result->num_rows; 
  $details = "";
  while(($row = $result->fetch_assoc())!=NULL){
   $hometown = unserialize($row['hometown']);
   $details .= $row['user_id'] . "\t" . $row['fbid']."\t".$row['firstname']."\t".$row['lastname']."\t".$hometown['name'].'\n';
  }  
 }

 $sql = "select count(*) from request where lastupd>'$date1' and lastupd<'$date2'";
 $result = parent::execute($sql);
 $requestcount = $result->num_rows;
 
 $sql = "select count(*) from carpool where lastupd>'$date1' and lastupd<'$date2'";
 $result = parent::execute($sql);
 $carpoolcount = $result->num_rows;

 $body = "A total of " . count($users) . " users installed the App, out of which $fbcount logged in through facebook";
 if($fbcount > 0) $body .= " as follows:\n" . $details;
 $body .= "\n\n";
 $body .= "Total Insta   Requests: $requestcount\n";
 $body .= "Total Carpool Requests: $carpoolcount\n";
 $body .= "\n\nAuto-generated email. Do not reply.\n";
 echo $body; 
 $subject = "Weekly Stats: $date1 - $date2";
 echo $subject . "\n";
}

function SendMail($subject, $body, $to = 'gourav.khaneja@gmail.com'){
}

 
}

new Stats();
?>
