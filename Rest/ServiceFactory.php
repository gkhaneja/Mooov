<?php
require_once("/home/gourav/Mooov/trunk/autoload.php");

class ServiceFactory {
	public $uri;
    
	public function __construct($uri) {
		$this->uri = $uri;
	}

	public function serve(){
		Logger::bootup();
		Logger::do_log("URL recieved: " . $this->uri);
		$parts=explode('/',$this->uri);
		if(count($parts)<3 || class_exists($parts[1],true)==false){
			echo "Error: Service " . $parts[1] . " Not Found.";
			return;
		}    
		$service = new $parts[1]();
		$method = $parts[2];
		if(method_exists($service,$method)==false){
			echo "Method " . $method . " Not Found.";
			return;
		}
		$arguments = array();
		for($i=3;$i<count($parts);$i++){
			$arguments[] = $parts[$i];
		}
		call_user_func_array(array($service,$method),$arguments);
		return;
	}

}


?>
