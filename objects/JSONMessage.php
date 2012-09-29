
<?php
/*

This class will define the message sent in reply to API calls
It is a Json array with 3 parts
1. Header
2. Error  // Present only in case of error
3. Body

*/

class JSONMessage{
	
	private $m_header;
	private $m_error;
	private $m_body;   
	public function __construct()
	{
		$this->m_header = array();
		$this->m_body = array();
	} 
	
	public function setError($error)
	{
		$this->m_error = $error;
	}
	
	public function setBody($body)
	{
		$this->m_body = $body;
	}
	
	public function setHeader($header)
	{
		$this->m_header = $header;
	}
	
	public function getMessage()
	{
		if(isset($this->m_error) && !empty($this->m_error))
		{
			return json_encode(array('header' => $this->m_header ,'error' => $this->m_error));
		}
		
		return json_encode(array('header' => $this->m_header ,'body' => $this->m_body));
	}
}