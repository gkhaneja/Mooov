<?php

new Monitor();

class Monitor {

var $sleeptime = 100;
var $filename = "/tmp/mooov.log";
var $exception_pattern = "/Internal Error/";
//var $exception_pattern = "/URL/";
//var $mailid = 'strangerbuddy@googlegroups.com';
var $mailid = 'gourav.khaneja@gmail.com';

function Monitor($seek = 0){
 $fh = fopen($this->filename,'r');
 fseek($fh,$seek, SEEK_END);
 while(1){
  if(feof($fh)){ 
   $pos = ftell($fh);
   fclose($fh);
   sleep($this->sleeptime);
   $fh = fopen($this->filename,'r');
   fseek($fh,$pos);
   continue;
  }
  if(($line = fgets($fh))){
   //echo $line;
   $this->analyze($line);
  }
 }
}

function analyze($line){
 if(preg_match($this->exception_pattern,$line)==1){
  $pattern = '/rid=([0-9]*)/';
  if(preg_match($pattern,$line,&$matches)==1){
   $rid = $matches[1];
   $log = $this->parseThreadLogs($rid);
   //echo "$log\n";
   mail($this->mailid,"Hopin API: Internal Error Reporting",$log);
  }
 }
}

function parseThreadLogs($rid){
 //$rid=$argv[1];
 $ret = "";
 $fh = fopen($this->filename,'r');
 if(!$fh){echo "Failed opening file\n"; exit(1);}
 $pattern = "[rid=$rid]";
 //echo "Searching for pattern $pattern\n";

 $on=0;
 while(!feof($fh)){
  $line = fgets($fh);
  $match = preg_match($pattern, $line);
  $other = preg_match('/\[rid=/',$line);
  if($match){
   $ret .= $line;
   $on=1;
  }/*else if($other){
   $on=0;
  }else if($on){
   $ret .= $line;
  }*/
 }
 return $ret;
}

}

?>
