<?php
 $rid=$argv[1];
 $fh = fopen('/tmp/mooov.log','r');
 if(!$fh){echo "Failed opening file\n"; exit(1);}
 $pattern = "[rid=$rid]";
 echo "Searching for pattern $pattern\n";

 $on=0;
 while(!feof($fh)){
  $line = fgets($fh);
  $match = preg_match($pattern, $line);
  $other = preg_match('/\[rid=/',$line);
  if($match){
   echo $line;
   $on=1;
  }else if($other){
   $on=0;
  }else if($on){
   echo $line;
  }
 }
?>
