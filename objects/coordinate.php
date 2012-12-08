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
  if($lat > SOUTH || $lat < NORTH){};
		if($lon > EAST || $lon < WEST) {};

  $this->lat = $lat;
  $this->lon = $lon;

			$this->row_floor = floor(($lat - NORTH)/RADIUS);
			$this->col_floor = floor(($lon - WEST)/RADIUS);

			$this->row_ceil = ceil(($lat - NORTH)/RADIUS);
			$this->col_ceil = ceil(($lon - WEST)/RADIUS);
  
 }

}


?>
