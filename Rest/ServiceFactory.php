<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once("objects/logger.php");
require_once("Rest/UserService.php");
require_once("Rest/UserDetailsService.php");
require_once("objects/exception.php");
require_once("Rest/RequestService.php");


class ServiceFactory {
	public $uri;
    
	public function __construct($uri) {
		$this->uri = $uri;
	}

	public function serve(){
		Logger::bootup();
		Logger::do_log("URL recieved: " . $this->uri);
		$dbobject = new dbclass();
		$dbobject->connect();
		$parts=explode('/',$this->uri);
		if(count($parts)<3 || class_exists($parts[1],true)==false){
			$error_m = new ExceptionHandler(array("code" =>"1" , 'error' => 'Service not Found'));
			echo $error_m->m_error->getMessage();
			return;
		}    
		$service = new $parts[1]();
		$function = $parts[2];
		if(method_exists($service,$function)==false){
			Logger::do_log("Method not found: " . $function);
			$error_m = new ExceptionHandler(array("code" =>"2" , 'error' => 'Method not Found'));
			echo $error_m->m_error->getMessage();
			return;
		}
// 		$arguments = array();
// 		for($i=3;$i<count($parts);$i++){
// 			$arguments[] = $parts[$i];
// 		}
		
		$method = $_SERVER['REQUEST_METHOD'];
		switch ($method) {
			case 'GET':
			case 'HEAD':
				$arguments = $_GET;
				break;
			case 'POST':
				$arguments = $this->getPostArguments();
				break;
			case 'PUT':
			case 'DELETE':
				parse_str(file_get_contents('php://input'), $arguments);
		}

		call_user_func(array($service,$function),$arguments);
		return;
	}
	
	function getPostArguments()
	{
		$request_type =  $_SERVER['CONTENT_TYPE'];
		
		if(preg_match('/json/', $request_type) != 0)
		{ 
                        $req = file_get_contents('php://input');
			error_log($req);	
			return get_object_vars(json_decode($req));
		}
		else  
			return $_POST;
	}
}


?>
