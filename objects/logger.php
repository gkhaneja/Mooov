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
		$method	= $trace[2]['function'];
		$class = $trace[2]['class'];
  $i=3;
  while(($class=="dbclass" || $class=="logger") && $i<=4){
		 $method	= $trace[$i]['function'];
	 	$class = $trace[$i]['class'];
   $i++;
  }
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
