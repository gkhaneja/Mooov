<?php
//require_once("");
require_once('objects/field.php');
require_once("objects/logger.php");
require_once("objects/exception.php");
require_once("conf/db.inc");

class dbclass extends mysqli {

	public static $connection;
 public static $util_conn;

	function __construct(){
	}

	function connect(){
		$DB_HOST = DB_HOST;
		$DB_USER = DB_USER;
		$DB_PASS = DB_PASS;
		$DB_NAME = DB_NAME;
  $UTIL_NAME = UTIL_NAME;
		dbclass::$connection = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
		if (mysqli_connect_errno()) {
			Logger::do_log("DB Connection failed: " . mysqli_connect_error());
			throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
		}
		dbclass::$util_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $UTIL_NAME);
		if (mysqli_connect_errno()) {
			Logger::do_log("Util DB Connection failed: " . mysqli_connect_error());
			//throw new APIException(array("code" =>"1" ,'reference'=>Logger::$rid, 'error' => 'Internal Error'));
		}
		Logger::do_log("Connected to databse " . $DB_NAME);
	}	

	function execute($query){
//			$query  = dbclass::$connection->real_escape_string($query);
//			error_log($query);
		Logger::do_log($query);
		$result = dbclass::$connection->query($query);
		if (!$result) {
   Logger::do_log('Invalid query: ' . dbclass::$connection->error);
   //$err_code = dbclass::$connection->errno;
			throw new APIException(array("code" =>"4", 'reference'=>Logger::$rid, 'error' => "Internal Error"));
		}
		return $result;
	}

 function util_execute($query){
		Logger::do_log($query);
		$result = dbclass::$util_conn->query($query);
		if (!$result) {
   Logger::do_log('Invalid query: ' . dbclass::$util_conn->error);
		}
		return $result;
 }

	function update($table, $fields, $equal_fields = array('id' => 1)){
		$set_part = "";
		$first=1;
		foreach($fields as $field){
			if($field->readonly==0 && isset($field->value)){
				if($first==0){
					$set_part .= ", ";
				}else{
					$first=0;
				}
				$set_part .= $field->dbname . "=\"" . $field->value . "\"";
			}
		}	
		$where_part="";
		$first=1;
		foreach($equal_fields as $field => $value){
			if($first==1){
				$where_part .= "" . $field . "=\"" . $value . "\"";
				$first=0;
			}else{
				$where_part .= " AND " . $field . "=\"" . $value . "\"";
			}
		}
		$query = "UPDATE " . $table . " SET " . $set_part . " WHERE " . $where_part;
                error_log("=== $query ===");
		$this->execute($query);
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

	function select($table, $select_fields = array('*'), $equal_fields = array('id' => 1)){
		$select_part = "";
		$first=1;
		foreach($select_fields as $field){
			if($first==1){
				$select_part .= "" . $field;
				$first=0;
			}else{
				$select_part .= ", " . $field;
			}
		}
		$where_part="";
		$first=1;
		foreach($equal_fields as $field => $value){
			if($first==1){
				$where_part .= "" . $field . "=\"" . $value . "\"";
				$first=0;
			}else{
				$where_part .= " AND " . $field . "=\"" . $value . "\"";
			}
		}
		$query = "SELECT " . $select_part . " FROM " . $table . " WHERE " . $where_part;
		$result = $this->execute($query);
		$ret = array();
		while($row=$result->fetch_assoc()){
			$ret[] = $row;
		}
		return $ret;
	}

	
}

?>
