<?php
require_once('objects/dbclass.php');

class Mumbai extends dbclass {
	var $SOUTH = 19.23000000;
	var $NORTH = 18.90000000;
	var $EAST = 72.95500000;
	var $WEST = 72.81670000;

	function deleteRequest($user_id){
		$result = parent::select('request',array('*'),array('user_id' => $user_id));
		if(count($result)>0){
			$lat_src = $result[0]['src_latitude'];
			$lon_src = $result[0]['src_longitude'];
			$lat_dst = $result[0]['dst_latitude'];
			$lon_dst = $result[0]['dst_longitude'];
			if($lat_src > $this->SOUTH || $lat_src < $this->NORTH) return 1;
			if($lon_src > $this->EAST || $lon_src < $this->WEST) return 1;
			if($lat_dst > $this->SOUTH || $lat_dst < $this->NORTH) return 1;
			if($lon_dst > $this->EAST || $lon_dst < $this->WEST) return 1;
			$row_floor_src = floor(($lat_src - $this->NORTH)/0.001);
			$row_ceil_src = ceil(($lat_src - $this->NORTH)/0.001);
			$col_floor_src = floor(($lon_src - $this->WEST)/0.001);
			$col_ceil_src = ceil(($lon_src - $this->WEST)/0.001);
			$row_floor_dst = floor(($lat_dst - $this->NORTH)/0.001);
			$row_ceil_dst = ceil(($lat_dst - $this->NORTH)/0.001);
			$col_floor_dst = floor(($lon_dst - $this->WEST)/0.001);
			$col_ceil_dst = ceil(($lon_dst - $this->WEST)/0.001);
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_ceil_src));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_src SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_ceil_src . " AND col_id = " . $col_ceil_src;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_floor_src));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_src SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_ceil_src . " AND col_id = " . $col_floor_src;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_ceil_src));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_src SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_floor_src . " AND col_id = " . $col_ceil_src;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_floor_src));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_src SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_floor_src . " AND col_id = " . $col_floor_src;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_ceil_dst));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_dst SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_ceil_dst . " AND col_id = " . $col_ceil_dst;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_floor_dst));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_dst SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_ceil_dst . " AND col_id = " . $col_floor_dst;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_ceil_dst));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_dst SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_floor_dst . " AND col_id = " . $col_ceil_dst;
				parent::execute($sql);		
			}
  		$result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_floor_dst));
			if(count($result)>0){
				$pattern = "/," . $user_id . ",/";
				$replacement = ",";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/^" . $user_id . ",/";
				$replacement = "";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/," . $user_id . "$/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$pattern = "/" . $user_id . "/";
				$result[0]['users'] = preg_replace($pattern,$replacement,$result[0]['users']);
				$sql = "UPDATE mumbai_dst SET users = \"" . $result[0]['users'] . "\" WHERE row_id = " . $row_floor_dst . " AND col_id = " . $col_floor_dst;
				parent::execute($sql);		
			}
		}
		return 0;
	}

function addRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst){
	if($lat_src > $this->SOUTH || $lat_src < $this->NORTH) return 1;
	if($lon_src > $this->EAST || $lon_src < $this->WEST) return 1;
	if($lat_dst > $this->SOUTH || $lat_dst < $this->NORTH) return 1;
	if($lon_dst > $this->EAST || $lon_dst < $this->WEST) return 1;
	$this->deleteRequest($user_id);
	$row_floor_src = floor(($lat_src - $this->NORTH)/0.001);
	$row_ceil_src = ceil(($lat_src - $this->NORTH)/0.001);
	$col_floor_src = floor(($lon_src - $this->WEST)/0.001);
	$col_ceil_src = ceil(($lon_src - $this->WEST)/0.001);
	$row_floor_dst = floor(($lat_dst - $this->NORTH)/0.001);
	$row_ceil_dst = ceil(($lat_dst - $this->NORTH)/0.001);
	$col_floor_dst = floor(($lon_dst - $this->WEST)/0.001);
	$col_ceil_dst = ceil(($lon_dst - $this->WEST)/0.001);
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_ceil_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_src SET users = \"" . $users_str . "\" WHERE row_id = " . $row_ceil_src . " AND col_id = " . $col_ceil_src;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_src (row_id,col_id,users) VALUES (" . $row_ceil_src . "," . $col_ceil_src . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_floor_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_src SET users = \"" . $users_str . "\" WHERE row_id = " . $row_ceil_src . " AND col_id = " . $col_floor_src;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_src (row_id,col_id,users) VALUES (" . $row_ceil_src . "," . $col_floor_src . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_ceil_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_src SET users = \"" . $users_str . "\" WHERE row_id = " . $row_floor_src . " AND col_id = " . $col_ceil_src;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_src (row_id,col_id,users) VALUES (" . $row_floor_src . "," . $col_ceil_src . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_floor_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_src SET users = \"" . $users_str . "\" WHERE row_id = " . $row_floor_src . " AND col_id = " . $col_floor_src;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_src (row_id,col_id,users) VALUES (" . $row_floor_src . "," . $col_floor_src . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_ceil_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_dst SET users = \"" . $users_str . "\" WHERE row_id = " . $row_ceil_dst . " AND col_id = " . $col_ceil_dst;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_dst (row_id,col_id,users) VALUES (" . $row_ceil_dst . "," . $col_ceil_dst . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_floor_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_dst SET users = \"" . $users_str . "\" WHERE row_id = " . $row_ceil_dst . " AND col_id = " . $col_floor_dst;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_dst (row_id,col_id,users) VALUES (" . $row_ceil_dst . "," . $col_floor_dst . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_ceil_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_dst SET users = \"" . $users_str . "\" WHERE row_id = " . $row_floor_dst . " AND col_id = " . $col_ceil_dst;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_dst (row_id,col_id,users) VALUES (" . $row_floor_dst . "," . $col_ceil_dst . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_floor_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		if(!in_array($user_id,$users)){
			$users[] = $user_id;
			$users_str = implode(",", $users);
			$sql = "UPDATE mumbai_dst SET users = \"" . $users_str . "\" WHERE row_id = " . $row_floor_dst . " AND col_id = " . $col_floor_dst;
			parent::execute($sql);		
		}
	}else{
			$sql = "INSERT mumbai_dst (row_id,col_id,users) VALUES (" . $row_floor_dst . "," . $col_floor_dst . ",\"" . $user_id . "\")";
			parent::execute($sql);
	}
	return 0;
}

function matchRequest($user_id,$lat_src,$lon_src,$lat_dst,$lon_dst){
	$matches = array();
	if($lat_src > $this->SOUTH || $lat_src < $this->NORTH) return $matches;
	if($lon_src > $this->EAST || $lon_src < $this->WEST) return $matches;
	if($lat_dst > $this->SOUTH || $lat_dst < $this->NORTH) return $matches;
	if($lon_dst > $this->EAST || $lon_dst < $this->WEST) return $matches;
	$row_floor_src = floor(($lat_src - $this->NORTH)/0.001);
	$row_ceil_src = ceil(($lat_src - $this->NORTH)/0.001);
	$col_floor_src = floor(($lon_src - $this->WEST)/0.001);
	$col_ceil_src = ceil(($lon_src - $this->WEST)/0.001);
	$row_floor_dst = floor(($lat_dst - $this->NORTH)/0.001);
	$row_ceil_dst = ceil(($lat_dst - $this->NORTH)/0.001);
	$col_floor_dst = floor(($lon_dst - $this->WEST)/0.001);
	$col_ceil_dst = ceil(($lon_dst - $this->WEST)/0.001);
	$partial_matches = array();
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_ceil_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$partial_matches)){
				$partial_matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_ceil_src, 'col_id' => $col_floor_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$partial_matches)){
				$partial_matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_ceil_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$partial_matches)){
				$partial_matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_src',array('users'),array('row_id' => $row_floor_src, 'col_id' => $col_floor_src));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$partial_matches)){
				$partial_matches[] = $user;
			}
		}
	}

  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_ceil_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$matches) && in_array($user,$partial_matches)){
				$matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_ceil_dst, 'col_id' => $col_floor_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$matches) && in_array($user,$partial_matches)){
				$matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_ceil_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$matches) && in_array($user,$partial_matches)){
				$matches[] = $user;
			}
		}
	}
  $result = parent::select('mumbai_dst',array('users'),array('row_id' => $row_floor_dst, 'col_id' => $col_floor_dst));
	if(count($result)>0){
		$users = explode(",",$result[0]['users']);
		foreach($users as $user){
			if($user!=$user_id && !in_array($user,$matches) && in_array($user,$partial_matches)){
				$matches[] = $user;
			}
		}
	}
	return $matches;
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

?>
