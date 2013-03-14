<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");

class Logger {

	private static $file_handle;
	private $log_file="/tmp/mooov.log";
	private $level = 10;
 public static $rid;

	public static function bootup(){
		$logger = new Logger();
		Logger::$file_handle = fopen($logger->log_file,"a");
  Logger::$rid = time();
	}

	public static function do_log($logstr, $level=0){
		$logger = new Logger();
		if(strlen($logstr)>10000) $logstr = substr($logstr,0,1000) . "..."; 
		if(!$logger->checkLevel($level)) $level=10;
		if(!isset(Logger::$file_handle)) return;
		$datetime = date("Y-m-d H-m-s");
  $address = $logger->getAddress();
		$log = "[" . $datetime . "][rid=" . Logger::$rid . "][" . $address['class'] . "][" . $address['method'] . "] " . $logstr . "\n"; 
		fwrite(Logger::$file_handle, $log);
		return;
	}

 function getAddress(){
		$trace = debug_backtrace();
  $i=0;
  while($i<count($trace)){
		 if(isset($trace[$i]['function'])) $method	= $trace[$i]['function'];
	 	if(isset($trace[$i]['class'])) $class = $trace[$i]['class'];
			$cont=0;
			if($class=='Logger') $cont=1;
			if($class=='dbclass' && $method!='connect') $cont=1;
			if(!isset($class)) $cont=1;
			if($cont==0) break;
   $i++;
  }
		if(!isset($class)) $class = "unknown";
		if(!isset($method)) $method = "unknown";
  return array('class'=>$class, 'method'=>$method);
 }

	function checkLevel($level){
		if($level <= $this->level) 
			return true;
		else 
			return false;
	}

}

?>
