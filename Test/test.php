<?php
require_once("Test/user.php");
require_once("Test/request.php");
require_once("objects/route.php");
require_once("conf/constants.inc");

/*$tests = array(
              array('enable' => 1, 'uuid' => "test1", 'lat_src' => 19.000, 'lon_src'=>72.9, 'lat_dst' => 19.046, 'lon_dst' => 72.8937),
              array('enable' => 1, 'uuid' => "test2", 'lat_src' => 19.0009, 'lon_src'=>72.9, 'lat_dst' => 19.046, 'lon_dst' => 72.8937),
              array('enable' => 1, 'uuid' => "test3", 'lat_src' => 19.002, 'lon_src'=>72.9, 'lat_dst' => 19.046, 'lon_dst' => 72.8937)
);*/


initialize();
$tests = createTestCases(5);
$errors = array();
addUsers($tests, $errors);
addRequests($tests, $errors);
setExpectations($tests);
matchRequests($tests, $errors);
analyze($tests, $errors);
//print_r($tests);
//print_r($errors);

function analyze($tests, $errors){
 $code1=0;
 $code3=0;
 $code4=0;
 $code5=0;
 $mismatch=0;
 foreach($errors as $error){
  if($error['code']==1) $code1++;
  if($error['code']==3) $code3++;
  if($error['code']==4) $code4++;
  if($error['code']==5) $code5++;
 }
 foreach($tests as $test){
  if($test['enable']==0 || $test['result']==0) $mismatch++;
 }
 echo "Result:\n";
 echo "Failures: $mismatch/" . count($tests) . "\n";
 echo "Code 1 errors: $code1\n";
 echo "Code 3 errors: $code3\n";
 echo "Code 4 errors: $code4\n";
 echo "Code 5 errors: $code5\n";
}


function initialize(){
		Logger::bootup();
		$dbobject = new dbclass();
		$dbobject->connect();
  $region='mumbai';
  $GLOBALS['city'] = $region;
  $GLOBALS['src_table'] = $region. '_src';
  $GLOBALS['dst_table'] = $region . '_dst';
  $GLOBALS['SOUTH'] = constant($region.'_SOUTH'); 
  $GLOBALS['NORTH'] = constant($region . '_NORTH');
  $GLOBALS['EAST'] = constant($region . '_EAST');
  $GLOBALS['WEST'] = constant($region . '_WEST');
  $GLOBALS['RADIUS'] = 500;
  $GLOBALS['DEGSTEP'] = 0.001;
  $GLOBALS['RADIUS_X'] = 112;
  $GLOBALS['RADIUS_Y'] = 105;
  $GLOBALS['THRESHOLD'] = 20;
}

function addUsers(&$tests, &$errors){
 $user = new UserTest();
 for($i=0;$i<count($tests);$i++){
  if($tests[$i]['enable']==0) continue;
  $data = $user->addUser($tests[$i]['uuid']);
  if(isset($data['error'])){
   $errors[] = $data['error'];
   $tests[$i]['enable']=0;
  }else{
   $tests[$i]['user_id'] = $data['body']['user_id'];
  }
 }
 /*for($i=0;$i<count($tests);$i++){
  if($tests[$i]['enable']==0) continue;
  $expected = explode(",",$tests[$i]['expected']);
  $expected_ids=array();
  foreach($expected as $uuid){
   $data = $user->getUserID($uuid);
   if(isset($data['error'])){
    $errors[] = $data['error'];
   }else{
    $expected_ids[] = $data['body']['user_id'];
   }
  }
  $tests[$i]['expected_ids'] = implode(",", $expected_ids);
 }*/ 
}

function putMatch(&$top5, $user_id, $percent){
 for($i=0;$i<count($top5);$i++){
  if($percent <= $top5[$i]['percent']) break;
 }
 if($i-1>=0){
  for($j=0;$j<=$i-2;$j++){
   $top5[$j]['percent'] = $top5[$j+1]['percent'];
   $top5[$j]['user_id'] = $top5[$j+1]['user_id'];
  }
  $top5[$i-1]['percent']=$percent;
  $top5[$i-1]['user_id']=$user_id;
 }
 return;
}


function setExpectations(&$tests){
 for($i=0;$i<count($tests);$i++){
  $top5=array(array('user_id'=>0, 'percent'=>0),array('user_id'=>0, 'percent'=>0),array('user_id'=>0, 'percent'=>0),array('user_id'=>0, 'percent'=>0),array('user_id'=>0, 'percent'=>0));
  for($j=0;$j<count($tests);$j++){
   if($i==$j) continue;
   $route1 = new Route($tests[$i]['user_id'],$tests[$i]['lat_src'],$tests[$i]['lon_src'],$tests[$i]['lat_dst'],$tests[$i]['lon_dst']);
   $route2 = new Route($tests[$j]['user_id'],$tests[$j]['lat_src'],$tests[$j]['lon_src'],$tests[$j]['lat_dst'],$tests[$j]['lon_dst']);
   $percent = $route1->matchRoute($route1,$route2);
   putMatch($top5,$tests[$j]['user_id'],$percent);
  }
  $real_top5=array();
  foreach($top5 as $top){
   if($top['percent']==0) continue;
   $real_top5[]=$top['user_id'];
  }
  $tests[$i]['expected_ids'] = implode(",",$real_top5);
 }
}

function matchRequests(&$tests, &$errors){
 $request = new RequestTest();
 for($i=0;$i<count($tests);$i++){ 
  if($tests[$i]['enable']==0) continue;
  $data = $request->match($tests[$i]['user_id']);
  if(isset($data['error'])){
   $errors[] = $data['error'];
   $tests[$i]['enable']=0;
  }else{
   $expected = explode(",",$tests[$i]['expected_ids']);
   $got=array();
   foreach($data['body']['NearbyUsers'] as $user){
    $got[] = $user['loc_info']['user_id'];
   }
   $tests[$i]['got'] = implode(",",$got);
   if(sort($got) == sort($expected)){
    $tests[$i]['result'] = 1;
   }else{
    $tests[$i]['result'] = 0;    
   }
  }
 }
}


function addRequests(&$tests, &$errors){
 $request = new RequestTest();
 for($i=0;$i<count($tests);$i++){ 
  if($tests[$i]['enable']==0) continue;
  $data = $request->add($tests[$i]['user_id'],$tests[$i]['lat_src'],$tests[$i]['lon_src'],$tests[$i]['lat_dst'],$tests[$i]['lon_dst']);
  if(isset($data['error'])){
   $errors[] = $data['error'];
   $tests[$i]['enable']=0;
  }
 }
}

function createTestCases($N){
 $tests=array();
 for($i=1;$i<=$N;$i++){
  $uuid = 'test'.$i;
  $lat_src = getRandomLat(19.23000000, 18.90000000); $lon_src = getRandomLon(72.95500000, 72.81670000);
  $lat_dst = getRandomLat(19.23000000, 18.90000000); $lon_dst = getRandomLon(72.95500000, 72.81670000);
  $tests[] = array('enable'=>1, 'uuid'=>$uuid, 'lat_src'=>$lat_src, 'lon_src'=>$lon_src, 'lat_dst'=>$lat_dst, 'lon_dst'=>$lon_dst);
 }
 return $tests;
}


function getRandomLat($north, $south){
 $max = ($north-$south)/0.001;
 $random = rand(0,$max);
 $lat = $south + 0.001*$random;
 return $lat;
}

function getRandomLon($east, $west){
 $max = ($east-$west)/0.001;
 $random = rand(0,$max);
 $lon = $west + 0.001*$random;
 return $lon;
}

?>
