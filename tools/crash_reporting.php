<?php

 $fh = fopen("/tmp/mooov.log",'r');
 while(1){
  //echo "position: " . ftell($fh) . "\n";
  if(feof($fh)){ 
   $pos = ftell($fh);
   fclose($fh);
   //sleep(10);
   $fh = fopen("/tmp/mooov.log",'r');
   fseek($fh,$pos);
   //echo "At the end\n"; continue;
  }
  if(($line = fgets($fh))){
   echo $line;
  }
 }
 

?>
