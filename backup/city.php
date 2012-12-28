<?php
require_once('objects/dbclass.php');
require_once('objects/route.php');
require_once('objects/coordinate.php');
require_once("conf/constants.inc");

class City extends dbclass {

 function delete($user_id, $row_id, $col_id, $table_name){
  $result = parent::select($table_name,array('users'),array('row_id' => $row_id, 'col_id' => $col_id));
		if(count($result)>0){
   $users = explode($result[0]['users']);
   if(($key = array_search($user_id, $users)) !== FALSE) {
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

function match($coords, $table_name){
 $users = array();
 if(empty($coords)) return $users;
 $sql = "select users from $table_name where";
 $first=1;
 foreach($coords as $coord){
  if($first==1){
   $sql = $sql . " (row_id = " . $coord['row_id'] . " AND col_id = " . $coord['col_id'] . ")";
   $first=0;
  }else{
   $sql = $sql . " OR (row_id = " . $coord['row_id'] . " AND col_id = " . $coord['col_id'] . ")";
  }
 }
 $results = parent::execute($sql);
 while($row = $results->fetch_assoc()) {
		$users = array_unique(array_merge($users,explode(",",$row['users'])));
 }
 return $users;
}

function checkRow($row_id){
 $upper = ($GLOBALS['NORTH']-$GLOBALS['SOUTH'])/$GLOBALS['DEGSTEP'];
 if($row_id>=0 && $row_id<=$upper){
  return true;
 }
 return false;
}

function checkCol($col_id){
 $upper = ($GLOBALS['EAST']-$GLOBALS['WEST'])/$GLOBALS['DEGSTEP'];
 if($col_id>=0 && $col_id<=$upper){
  return true;
 }
 return false;
}

function getSearchCoords($route){
 $coords = array();
 $steps = ($GLOBALS['RADIUS_X']>$GLOBALS['RADIUS_Y']) ? $GLOBALS['RADIUS']/$GLOBALS['RADIUS_Y'] : $GLOBALS['RADIUS']/$GLOBALS['RADIUS_X'];
 for($i=0;$i<$steps;$i++){
  $row_id = $route->row_floor_src - $i;
  if(!$this->checkRow($row_id)) continue;
  for($j=0;$j<$steps;$j++){
   $col_id = $route->col_floor_src - $j;
   if(!$this->checkCol($col_id)) continue;
   $coords[] = array('row_id'=>$row_id, 'col_id'=>$col_id);  
  }
 } 
 for($i=1;$i<$steps;$i++){
  $row_id = $route->row_floor_src - $i;
  if(!$this->checkRow($row_id)) continue;
  for($j=1;$j<$steps;$j++){
   $col_id = $route->col_floor_src + $j;
   if(!$this->checkCol($col_id)) continue;
   $coords[] = array('row_id'=>$row_id, 'col_id'=>$col_id);  
  }
 } 
 for($i=1;$i<$steps;$i++){
  $row_id = $route->row_floor_src + $i;
  if(!$this->checkRow($row_id)) continue;
  for($j=1;$j<$steps;$j++){
   $col_id = $route->col_floor_src + $j;
   if(!$this->checkCol($col_id)) continue;
   $coords[] = array('row_id'=>$row_id, 'col_id'=>$col_id);  
  }
 } 
 for($i=1;$i<$steps;$i++){
  $row_id = $route->row_floor_src + $i;
  if(!$this->checkRow($row_id)) continue;
  for($j=1;$j<$steps;$j++){
   $col_id = $route->col_floor_src - $j;
   if(!$this->checkCol($col_id)) continue;
   $coords[] = array('row_id'=>$row_id, 'col_id'=>$col_id);  
  }
 } 
 return $coords;
 //return array(array('row_id' => $route->row_ceil_src, 'col_id'=>$route->col_ceil_src), array('row_id' => $route->row_ceil_src, 'col_id'=>$route->col_floor_src), array('row_id' => $route->row_floor_src, 'col_id'=>$route->col_ceil_src), array('row_id' => $route->row_floor_src, 'col_id'=>$route->col_floor_src));
}

function matchRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst){
 $route = new Route($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst);
 //$coords = $this->getSearchCoords($route);	
 $step_x = $GLOBALS['RADIUS']/$GLOBALS['RADIUS_X'];
 $step_y = $GLOBALS['RADIUS']/$GLOBALS['RADIUS_Y'];
 $x1 = floor($route->row_floor_src - $step_x);
 $x2 = ceil($route->row_floor_src + $step_x);
 $y1 = floor($route->col_floor_src - $step_y);
 $y2 = ceil($route->col_floor_src + $step_y);
 $matches = array();
 $sql = "select users from " . $GLOBALS['src_table'] . " where row_id>=$x1 AND row_id<=$x2 AND col_id>=$y1 AND col_id<=$y2";
 $results = parent::execute($sql);
 while($row = $results->fetch_assoc()) {
		$matches = array_unique(array_merge($matches,explode(",",$row['users'])));
 }
 //$matches = $this->match($coords, $GLOBALS['src_table']);

 //$matches = array_unique($matches);
 if(($key = array_search($user_id, $matches)) !== FALSE) {
  unset($matches[$key]);
 }
 $ret = array();
 foreach($matches as $match){
  if(empty($match)) continue;
  $sql = "select * from request where user_id = $match";
  $result = parent::execute($sql);
  if($result->num_rows > 0) {
   while($row = $result->fetch_assoc()) {
    $route2 = new Route($match, $row['src_latitude'], $row['src_longitude'], $row['dst_latitude'], $row['dst_longitude']);
    $percent = $route->matchRoute($route,$route2);
    if($percent > $GLOBALS['THRESHOLD']){
     $ret[] = array('user_id' => $match, 'percent' => $percent);
    }
		 }
  }
 }
	return $ret;
}


}

?>
