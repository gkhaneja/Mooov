<?php

class Field {

	var $dbname;
	var $name;
	var $mandatory;
	var $readonly;
  var $update_mandatory;
	var $value;

	function __construct($dbname, $name, $readonly){
		$this->dbname = $dbname;
		$this->name = $name;
		$this->readonly = $readonly;
	}

}

?>
