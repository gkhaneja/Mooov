<?php
require_once('objects/dbclass.php');
require_once('objects/logger.php');

class Route extends dbclass {

	var $lat_src;
	var $lon_src;
	var $lat_dst;
	var $lon_dst;
	var $google_direction_api = "http://maps.googleapis.com/maps/api/directions/json";


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


	function matchRoute($route1, $route2){
		//First check in the route table. If entry exists, return the match.
		$path1 = $this->getPath($route1);
		$path2 = $this->getPath($route2);
		if($path1==NULL || $path2==NULL){
			 return 0;
		}
		
		//print_r($path1);
		//print_r($path2);
		
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
