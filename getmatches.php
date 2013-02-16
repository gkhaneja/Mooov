<?php

$MANUAL = true;
//$MANUAL = false;

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "/var/www/html");
require_once('Rest/ServiceFactory.php');

$dbobject = new dbclass();
$dbobject->connect();

$isCarpool = false;
if(isset($_GET['iscarpool']) && $_GET['iscarpool'] == 1)
 $isCarpool = true;

if($MANUAL)
{
 $source = "(19.1196773,72.90508090000003)";
 $destination  = "(19.1297376,72.82915550000007)";
}
else
{
 $source = $_GET['source'];
 $destination  = $_GET['dest'];
}
error_log($source ."-" . $destination);
$source = str_replace('(', '', $source);
$source = str_replace(')', '', $source);
$source = str_replace(' ', '', $source);

$destination = str_replace('(', '', $destination);
$destination = str_replace(')', '', $destination);
$destination = str_replace(' ', '', $destination);

$src_array =  explode(',',$source);
$dst_array =  explode(',',$destination);

$location=array('user_id' => 1, 'site'=> 1,'src_latitude' => $src_array[0], 'src_longitude' => $src_array[1], 'dst_latitude' => $dst_array[0], 'dst_longitude' => $dst_array[1]);
$suffix='&user_id=1&src_latitude=' . $src_array[0] . '&src_longitude='.$src_array[1] . '&dst_latitude='. $dst_array[0] . '&dst_longitude'. $dst_array[1] ;
error_log(print_r($location,true));
//$request = new Request();
//$request->getNearbyRequests($location);
$SERVER  = 'http://www.hopin.co.in/';
if(!$isCarpool)
 $url = '/api/RequestService/getMatches/site/';
else
 $url = '/api/RequestService/getCarpoolMatches/site/';
 
echo $url;
$serviceFactory = new ServiceFactory($url);
$serviceFactory->serve($location);

?>
