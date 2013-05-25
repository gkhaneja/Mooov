<?php

//USAGE: php Test/tester.php api-url db-host db-user db-pass db-name fbtoken

require_once('Test/UserServiceTest.php');
require_once('Test/UserDetailsServiceTest.php');

class Tester extends mysqli {

var $domain;
var $connection;
var $fbtoken;

function tester($domain, $dbhost, $dbuser, $dbpass, $dbname, $fbtoken){
 $this->domain = $domain;
 $this->connect($dbhost, $dbuser, $dbpass, $dbname);
 $this->fbtoken = $fbtoken;
 echo "Start Testing Hopin API for domain $domain \n\n";
 new UserServiceTest($this);
 new UserDetailsServiceTest($this, $this->fbtoken);
}


function assertRow($sql, $assertions){ 
		$result = $this->connection->query($sql);
		if (!$result){ error_log("Invalid query:" . $this->connection->error); echo "0"; return 0;}
  if($result->num_rows!=1){ echo "0"; return 0;}
  $row = $result->fetch_assoc();
  foreach($assertions as $assertion=>$val){
   if($row[$assertion] != $val){ echo "0"; error_log("AssertRow failed for $assertion != $val but " . $row[$assertion]); return 0;}
  }
  echo "1"; return 1;
}

function Api($service, $method, $arguments){
 $url = $this->domain . "/$service/$method/?";
 $first=1;
 foreach($arguments as $argument => $val){
  if($first==1){
   $first=0;
   $url .= "$argument=$val";
  }else{
   $url .= "&$argument=$val";
  }
 }
 error_log("calling $url \n");
 $ch = curl_init();
 curl_setopt($ch,CURLOPT_URL,$url);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 $resp = curl_exec($ch);
 curl_close($ch);
 return $resp;
}


function connect($dbhost, $dbuser, $dbpass, $dbname){
		$this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if (mysqli_connect_errno()) {
			error_log("DB Connection failed: " . mysqli_connect_error());
   exit(1);
		}
  error_log("Connected to Database $dbname \n"); 
}	

function execute($query){
  error_log($query);
		$result = $this->connection->query($query);
		if (!$result) {
   error_log("Invalid query:" . $this->connection->error);
		}
		return $result;
}

}



new Tester($argv[1], $argv[2], $argv[3], $argv[4], $argv[5], $argv[6]);

?>
