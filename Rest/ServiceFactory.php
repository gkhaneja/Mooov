<?php
//require_once("/home/gourav/Mooov/trunk/autoload.php");
require_once('objects/dbclass.php');
require_once('objects/cache.php');
require_once("objects/logger.php");
require_once("Rest/UserService.php");
require_once("Rest/ChatService.php");
require_once("Rest/UserDetailsService.php");
require_once("objects/exception.php");
require_once("Rest/RequestService.php");
require_once('conf/service.conf');

class ServiceFactory {
	public $uri;
	public function __construct($uri) {
		$this->uri = $uri;
	}

	public function serve($sitearguments = null){
  global $response;
  $start_time = microtime(true);
		Logger::bootup();
  Cache::init();
		Logger::do_log("URL recieved: " . $this->uri);
		$dbobject = new dbclass();
		$dbobject->connect();
		$parts=explode('/',$this->uri);
		if(count($parts)<4 || class_exists($parts[2],true)==false){
			$error_m = new ExceptionHandler(array("code" =>"1" , 'error' => 'Service not Found'));
			echo $error_m->m_error->getMessage();
   $response .= $error_m->m_error->getMessage();
   $this->exitService($start_time, $parts[2]);
			return;
		}    
		$service = new $parts[2]();
  $service_str = $parts[2];
		$function = $parts[3];
		if(method_exists($service,$function)==false){
			Logger::do_log("Method not found: " . $function);
			$error_m = new ExceptionHandler(array("code" =>"2" , 'error' => 'Method not Found'));
			echo $error_m->m_error->getMessage();
   $response .= $error_m->m_error->getMessage();
   $this->exitService($start_time, $service_str, $function);
			return;
		}
		
  if(isset($parts[4]) && $parts[4] == 'site') {
     $arguments =  $sitearguments;
			  Logger::do_log("arguments for site " . print_r($arguments,true));
  } else {
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
  }
  $arg_str="";
  foreach($arguments as $arg => $val){
   $arg_str .= "&$arg=$val";
  }
  Logger::do_log($arg_str);
  if(!$this->authenticateRequest($function, $arguments)){
			Logger::do_log("Could not authenticate " . $function);
   $error_m = new ExceptionHandler(array("code" =>"2" , 'error' => 'Access Denied'));
   echo $error_m->m_error->getMessage();
   $response .= $error_m->m_error->getMessage();
   $this->exitService($start_time, $service_str, $function, $arg_str);
   return;
  }		
  try{
   dbclass::$connection->autocommit(false);
		 call_user_func(array($service,$function),$arguments);
   dbclass::$connection->commit();
  }catch(APIException $e){
   Logger::do_log("Terminating Request. Exception: " . $e->exception['error']);
   dbclass::$connection->rollback();
		 $m_error = new JSONMessage();
	  $m_error->setError($e->exception);
			echo $m_error->getMessage();
   $response .= $m_error->getMessage();
  }
  $this->exitService($start_time, $service_str, $function, $arg_str);
		return;
	}

 function exitService($start_time, $service_str="No service", $function="No method", $arg_str="No args"){
  global $response;
  $end_time = microtime(true);
  $serve_time = ($end_time-$start_time)*1000;
  $dbobj = new dbclass();
  $url = $this->uri;
  $dbobj->util_execute("Insert into rlog (service, method, arguments, url, response, serve_time) values ('$service_str', '$function', '$arg_str', '$url', '$response', $serve_time)");
  Logger::do_log("Serve Time: $serve_time milliseconds");
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
       function authenticateRequest($function,$arguments)
       {

         $uuid = $arguments['uuid'];
         $userid = $arguments['user_id'];
         error_log("UUID recieved : $function  $uuid -" . MASTER_UUID);
         if( $function == 'addUser'  || $function == 'createUser' || $uuid == MASTER_UUID)
          return true;	
         Logger::do_log("Debug: userid - $userid");
         $query  = "select id from user where uuid ='$uuid'";
         $db = new dbclass();
         $result = $db->execute($query);
         $ret = array();
                while($row=$result->fetch_assoc()){
                        $ret[] = $row;
                }

	 if(isset($ret[0]['id']) && $ret[0]['id'] == $userid)
            return true;

        return false;
      }
}


?>
