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

?>
