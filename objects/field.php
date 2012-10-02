<?php

class Field {

	var $dbname;
	var $name;
	var $mandatory;
	var $readonly;
	var $value;

	function __construct($dbname, $name, $mandatory, $readonly){
		$this->dbname = $dbname;
		$this->name = $name;
		$this->mandatory = $mandatory;	
		$this->readonly = $readonly;
	}

}

?>
