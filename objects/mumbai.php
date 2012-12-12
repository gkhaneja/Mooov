<?php
require_once('objects/dbclass.php');
require_once('objects/route.php');
require_once('objects/coordinate.php');
require_once("conf/constants.inc");

class Mumbai extends dbclass {
	var $SOUTH = 19.23000000;
	var $NORTH = 18.90000000;
	var $EAST = 72.95500000;
	var $WEST = 72.81670000;
  var $RADIUS = 0.001;

 function delete($row_id, $col_id, $user_id, $table_name){
  $result = parent::select($table_name,array('users'),array('row_id' => $row_id, 'col_id' => $col_id));
		if(count($result)>0){
   $users = explode($result[0]['users']);
   if(($key = array_search($user_id, $users)) != FALSE) {
    unset($users[$key]);
   }
   $user_str = implode($users);
			$sql = "UPDATE " . $table_name . " SET users = \"" . $user_str . "\" WHERE row_id = " . $row_id . " AND col_id = " . $col_id;
			parent::execute($sql);		
		}
  return;
 }

	function deleteRequest($user_id){
		$result = parent::select('request',array('*'),array('user_id' => $user_id));
		if(count($result)>0){
   $route = new Route($user_id, $result[0]['src_latitude'], $result[0]['src_longitude'], $result[0]['dst_latitude'], $result[0]['dst_longitude']);
   $this->delete($user_id, $route->row_ceil_src, $route->col_ceil_src, 'mumbai_src');
   $this->delete($user_id, $route->row_ceil_src, $route->col_floor_src, 'mumbai_src');
   $this->delete($user_id, $route->row_floor_src, $route->col_ceil_src, 'mumbai_src');
   $this->delete($user_id, $route->row_floor_src, $route->col_floor_src, 'mumbai_src');
   $this->delete($user_id, $route->row_ceil_dst, $route->col_ceil_dst, 'mumbai_dst');
   $this->delete($user_id, $route->row_ceil_dst, $route->col_floor_dst, 'mumbai_dst');
   $this->delete($user_id, $route->row_floor_dst, $route->col_ceil_dst, 'mumbai_dst');
   $this->delete($user_id, $route->row_floor_dst, $route->col_floor_dst, 'mumbai_dst');
   $route->delete();
		}
		return 0;
	}

function add($row_id, $col_id, $user_id, $table_name){
 $result = parent::select($table_name, array('users'),array('row_id' => $row_id, 'col_id' => $col_id));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE " . $table_name . " SET users = \"" . $users_str . "\" WHERE row_id = " . $row_id . " AND col_id = " . $col_id;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT " . $table_name . " (row_id,col_id,users) VALUES (" . $row_id . "," . $col_id . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
 return;
}

function addRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst){
 $route = new Route($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst);
	$this->deleteRequest($user_id);
 $this->add($route->row_ceil_src, $route->col_ceil_src, $user_id, 'mumbai_src');
 $this->add($route->row_ceil_src, $route->col_floor_src, $user_id, 'mumbai_src');
 $this->add($route->row_floor_src, $route->col_ceil_src, $user_id, 'mumbai_src');
 $this->add($route->row_floor_src, $route->col_floor_src, $user_id, 'mumbai_src');
 $this->add($route->row_ceil_dst, $route->col_ceil_dst, $user_id, 'mumbai_dst');
 $this->add($route->row_ceil_dst, $route->col_floor_dst, $user_id, 'mumbai_dst');
 $this->add($route->row_floor_dst, $route->col_ceil_dst, $user_id, 'mumbai_dst');
 $this->add($route->row_floor_dst, $route->col_floor_dst, $user_id, 'mumbai_dst');
 $route_id = $route->add();
	return $route_id;
}

function match($row_id, $col_id, $table_name){
 $users = array();
 $result = parent::select($table_name, array('users'),array('row_id' => $row_id, 'col_id' => $col_id));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
	}
 return $users;
}

function matchRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst){
 $route = new Route($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst);
	
 $matches_src = array();
 $matches_src = array_merge($matches_src, $this->match($route->row_ceil_src, $route->col_ceil_src, 'mumbai_src'));
 $matches_src = array_merge($matches_src, $this->match($route->row_ceil_src, $route->col_floor_src, 'mumbai_src'));
 $matches_src = array_merge($matches_src, $this->match($route->row_floor_src, $route->col_ceil_src, 'mumbai_src'));
 $matches_src = array_merge($matches_src, $this->match($route->row_floor_src, $route->col_floor_src, 'mumbai_src'));
 $matches_src = array_unique($matches_src);

 $matches_dst = array();
 $path = $route->getPath($route);
 if($path!=NULL){
  foreach($path as $step){
   $dst = array();
   $geo = new Coordinate($step['end_location']['lat'], $step['end_location']['lng']);
   $dst = array_merge($dst, $this->match($geo->row_ceil, $geo->col_ceil, 'mumbai_dst'));
   $dst = array_merge($dst, $this->match($geo->row_ceil, $geo->col_floor, 'mumbai_dst'));
   $dst = array_merge($dst, $this->match($geo->row_floor, $geo->col_ceil, 'mumbai_dst'));
   $dst = array_merge($dst, $this->match($geo->row_floor, $geo->col_floor, 'mumbai_dst')); 
   $dst = array_unique($dst);
   $matches_dst[] = $dst;
  }
 }
 $dst1 = array(); 
 $dst1 = array_merge($dst1, $this->match($route->row_ceil_dst, $route->col_ceil_dst, 'mumbai_dst'));
 $dst1 = array_merge($dst1, $this->match($route->row_ceil_dst, $route->col_floor_dst, 'mumbai_dst'));
 $dst1 = array_merge($dst1, $this->match($route->row_floor_dst, $route->col_ceil_dst, 'mumbai_dst'));
 $dst1 = array_merge($dst1, $this->match($route->row_floor_dst, $route->col_floor_dst, 'mumbai_dst')); 
 $dst1 = array_unique($dst1);
 $matches_dst[] = $dst1;
 //TODO: add start elements too

 $matches = array();
 foreach($matches_dst as $dst){
  $matches = array_merge($matches, array_intersect($matches_src,$dst));
 }
 $matches = array_unique($matches);
 if(($key = array_search($user_id, $matches)) != FALSE) {
  unset($matches[$key]);
 }
	return $matches;
}


}

?>
