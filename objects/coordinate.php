<?php
require_once("objects/dbclass.php");
 require_once("conf/constants.inc");

class Coordinate extends dbclass {
 var $lat;
 var $lon;

	var $row_ceil;
	var $col_ceil;

	var $row_floor;
	var $col_floor;

 function Coordinate($lat,$lon){
  if($lat > $GLOBALS['SOUTH'] || $lat < $GLOBALS['NORTH']){};
		if($lon > $GLOBALS['EAST'] || $lon < $GLOBALS['WEST']) {};

  $this->lat = $lat;
  $this->lon = $lon;

			$this->row_floor = floor(($lat - $GLOBALS['NORTH'])/$GLOBALS['DEGSTEP']);
			$this->col_floor = floor(($lon - $GLOBALS['WEST'])/$GLOBALS['DEGSTEP']);

			$this->row_ceil = ceil(($lat - $GLOBALS['NORTH'])/$GLOBALS['DEGSTEP']);
			$this->col_ceil = ceil(($lon - $GLOBALS['WEST'])/$GLOBALS['DEGSTEP']);
  
 }

 function __toString(){
  $str = "(" . $this->lat . ", " . $this->lon . ")";
  return $str;
 }

}


?>
