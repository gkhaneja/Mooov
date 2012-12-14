<?php
require_once('objects/dbclass.php');
require_once('objects/logger.php');
require_once('objects/coordinate.php');
 require_once("conf/constants.inc");

class Route extends dbclass {

 var $id;
 var $user_id;

	var $lat_src;
	var $lon_src;
	var $lat_dst;
	var $lon_dst;
 
	var $row_ceil_src;
	var $col_ceil_src;
	var $row_ceil_dst;
	var $col_ceil_dst;

	var $row_floor_src;
	var $col_floor_src;
	var $row_floor_dst;
	var $col_floor_dst;

	var $google_direction_api = "http://maps.googleapis.com/maps/api/directions/json";

 function Route($user_id, $lat_src,$lon_src,$lat_dst,$lon_dst){
  if($lat_src > $GLOBALS['SOUTH'] || $lat_src < $GLOBALS['NORTH']){};
		if($lon_src > $GLOBALS['EAST'] || $lon_src < $GLOBALS['WEST']) {};
		if($lat_dst > $GLOBALS['SOUTH'] || $lat_dst < $GLOBALS['NORTH']) {};
		if($lon_dst > $GLOBALS['EAST'] || $lon_dst < $GLOBALS['WEST']) {};

  $this->user_id = $user_id;
  $this->lat_src = $lat_src;
  $this->lon_src = $lon_src;
  $this->lat_dst = $lat_dst;
  $this->lon_dst = $lon_dst;

			$this->row_floor_src = floor(($lat_src - $GLOBALS['NORTH'])/$GLOBALS['RADIUS']);
			$this->col_floor_src = floor(($lon_src - $GLOBALS['WEST'])/$GLOBALS['RADIUS']);
			$this->row_floor_dst = floor(($lat_dst - $GLOBALS['NORTH'])/$GLOBALS['RADIUS']);
			$this->col_floor_dst = floor(($lon_dst - $GLOBALS['WEST'])/$GLOBALS['RADIUS']);

			$this->row_ceil_src = ceil(($lat_src - $GLOBALS['NORTH'])/$GLOBALS['RADIUS']);
			$this->col_ceil_src = ceil(($lon_src - $GLOBALS['WEST'])/$GLOBALS['RADIUS']);
			$this->row_ceil_dst = ceil(($lat_dst - $GLOBALS['NORTH'])/$GLOBALS['RADIUS']);
			$this->col_ceil_dst = ceil(($lon_dst - $GLOBALS['WEST'])/$GLOBALS['RADIUS']);
  
 }

 function delete(){
  
 }

 function add(){
  $path = $this->getPath($this);
  $path2[0]['lat'] = $this->lat_src;
  $path2[0]['lon'] = $this->lon_src;
  if($path != NULL){
   $size = 1;
   foreach($path as $step){
     $path2[$size]['lat'] = $step['end_location']['lat'];
     $path2[$size]['lon'] = $step['end_location']['lng'];
     $size++;
   }
  }
  $path_str = mysql_real_escape_string(serialize($path2));
		$result = parent::select('route',array('id'),array('user_id' => $this->user_id));
		if(isset($result[0]['id'])){
   $sql = "UPDATE route SET src_latitude=" . $this->user_id . ", src_longitude=" . $this->lon_src . ", dst_latitude=" . $this->lat_dst . ", dst_longitude=" . $this->lon_dst . ", path=\"" . $path_str . "\" WHERE user_id=" . $this->user_id;
		}else{
   $sql = "INSERT INTO route (user_id, src_latitude, src_longitude, dst_latitude, dst_longitude, path) VALUES (" . $this->user_id . "," .$this->lat_src . "," . $this->lon_src . "," . $this->lat_dst . "," . $this->lon_dst . ",\"" . $path_str . "\")";
		}
  parent::execute($sql);
		$result = parent::select('route',array('id'),array('user_id' => $this->user_id));
  if(count($result)>0){
   return $result[0]['id'];
  }
  return NULL;
 }

	function getPath($route){
		$ch = curl_init($this->google_direction_api . "?origin=" . $route->lat_src . "," . $route->lon_src . "&destination=" . $route->lat_dst . "," . $route->lon_dst . "&sensor=false&alternatives=true");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
		$data = curl_exec($ch);
		$path = json_decode($data,true);
		curl_close($ch);
		if($path['status']=="OK"){
			return $path['routes'][0]['legs'][0]['steps'];
		}else{
			Logger::do_log("Google direction API call failed for coordinates - " . $route);
			return NULL;
		}
	}

 function equal($coord1, $coord2){
  if($this->geo2distance($coord1->lat, $coord1->lon, $coord2->lat, $coord2->lon) <= $GLOBALS['RADIUS_DIST']){
   return true;
  }else{
   return false;
  }  
 }

/**********************************
Given two routes r1 and r2,
returns the percentge of the r1
matching r2
***********************************/
	function matchRoute($route1, $route2){
		//TODO: First check in the route table. If entry exists, return the match.
		$path1 = $this->getPath($route1);
		$path2 = $this->getPath($route2);
		if($path1==NULL || $path2==NULL){
			 return 0;
		}
  //print_r($path1);
  //print_r($path2);
  if(count($path1)==0) return 100;
  if(count($path2)==0) return 0;
  $coord1 = new Coordinate($path1[0]['start_location']['lat'], $path1[0]['start_location']['lng']);
  $coord2 = new Coordinate($path2[0]['start_location']['lat'], $path2[0]['start_location']['lng']);
  if(!$this->equal($coord1, $coord2)){
   return 0;
  }
  $M = array();
  for($i=0;$i<count($path1);$i++){
   $M[$i]=array();
   for($j=0;$j<count($path2);$j++){
    $M[$i][$j] = -1;
   }
  }
  $nmatch = $this->matchRecursion($path1, $path2, 0, 0, $M);
  $match =0; $total=0;
		for($i=0;$i<count($path1);$i++){
   if($i<$nmatch){
    $match += $path1[$i]['distance']['value']; 
   }
   $total += $path1[$i]['distance']['value'];
  }
  if($total == 0) return 100;
		return $match*100/$total;
	}

 function matchRecursion($path1, $path2, $p1, $p2, &$M){
  //echo "Recursion: checking for point " . $p1 . ", " . $p2 . "\n";
  if(count($path1)==$p1){
   //echo "Returning 0 because p1=count\n";
   return 0;
  }
  if(count($path2)==$p2){
   //echo "Returning 0 because p2=count\n";
   return 0;
  }
  if($M[$p1][$p2]!=-1){
   //echo "Returning M=" . $M[$p1][$p2] . "because it is not -1\n"; 
   return $M[$p1][$p2];
  }
  $coord1 = new Coordinate($path1[$p1]['end_location']['lat'], $path1[$p1]['end_location']['lng']);
  $coord2 = new Coordinate($path2[$p2]['end_location']['lat'], $path2[$p2]['end_location']['lng']);
  if(!$this->equal($coord1,$coord2)){
   $M[$p1][$p2] = 0;
   //echo "Returning 0 because coord " . $coord1 . ", " . $coord2 . "are not equal\n"; 
   return $M[$p1][$p2];
  } 
  $a = $this->matchRecursion($path1,$path2, $p1+1, $p2, $M);
  $b = $this->matchRecursion($path1,$path2, $p1, $p2+1, $M);
  $c = $this->matchRecursion($path1,$path2, $p1+1, $p2+1, $M);
  if($a>$b && $a>$c){
   $M[$p1][$p2] = $a+1;
  }elseif($b>$c){
   $M[$p1][$p2] = $b;
  }else{
   $M[$p1][$p2] = $c+1;
  }
  //echo "Returning M=" . $M[$p1][$p2] . "\n"; 
  return $M[$p1][$p2];
 }

	function __toString(){
		$str =  "(" . $this->lat_src .  "," . $this->lon_src . ")-(" . $this->lat_dst . "," . $this->lon_dst . ")";
		return $str;
	}

function geo2distance($lat1, $lon1, $lat2, $lon2){
	$R = 6371000; 
	$lat1 = deg2rad($lat1);
	$lat2 = deg2rad($lat2);
	$lon1 = deg2rad($lon1);
	$lon2 = deg2rad($lon2);
	$d = acos(sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($lon2-$lon1)) * $R;
	return $d;
}

}
