<?php
require_once("JSONMessage.php");

class ExceptionHandler {
	public $m_error; 
	
	public  function __construct($error)
	{
		$this->m_error = new JSONMessage();
	    $this->m_error->setError($error);
	}
	 
}

class APIException extends Exception {
 public $exception;
 
 public function __construct($exception){
  $this->exception=$exception;
 }
}

?>
