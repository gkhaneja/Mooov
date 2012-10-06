<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");

class Logger {

	private static $file_handle;
	private $log_file="/tmp/mooov.log";
	private $level = 10;

	public static function bootup(){
		$logger = new Logger();
		Logger::$file_handle = fopen($logger->log_file,"a");
	}

	public static function do_log($logstr, $level=0){
		$logger = new Logger();
		if(!$logger->checkLevel($level)) return;
		if(!isset(Logger::$file_handle)) return;
		$datetime = date("Y-m-d H-m-s");
		$trace = debug_backtrace();
		$method	= $trace[1]['function'];
		$class = $trace[1]['class'];
		$log = "[" . $datetime . "][" . $class . "][" . $method . "] " . $logstr . "\n"; 
		fwrite(Logger::$file_handle, $log);
		return;
	}

	function checkLevel($level){
		if($level <= $this->level) 
			return true;
		else 
			return false;
	}

}

?>
