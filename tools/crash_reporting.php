<?php

 $fh = fopen("/tmp/mooov.log",'r');
 while(1){
  if(feof($fh)) continue;
  if(($line = fgets($fh))){
   echo $line;
  }
 }
 

?>
