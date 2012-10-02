<?php
//require_once("");
require_once('objects/field.php');
require_once("objects/logger.php");

class dbclass extends mysqli {

	public static $connection;

	function __construct(){
	}

	function select_where($select_fields, $where_fields, $where_ops){
		
	}

	function connect(){
		$DB_HOST = "localhost";
		$DB_USER = "root";
		$DB_PASS = "rock";
		$DB_NAME = "stranger";
		dbclass::$connection = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		if (mysqli_connect_errno()) {
			printf("Connect failed:( %s\n", mysqli_connect_error());
			exit();
		}
	}	

	function execute($query){
		Logger::do_log($query);
		$result = dbclass::$connection->query($query) or die(dbclass::$connection->error.__LINE__);
		return $result;
	}

	function insert($table, $fields){
		$field_part = "(";
		$value_part = "(";
		$first=1;
		foreach($fields as $field){
			if($field->readonly==0 && isset($field->value)){
				if($first==0){
					$field_part .= ", ";
					$value_part .= ", ";
				}else{
					$first=0;
				}
				$field_part .= $field->dbname;
				$value_part .= "\"" . $field->value . "\"";
			}
		}
		$field_part .= ")";
		$value_part .= ")";
		$query = "INSERT INTO " . $table . " " . $field_part . " VALUES " . $value_part;
		$this->execute($query);
	}
}

?>
