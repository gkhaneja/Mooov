<?php
require_once("objects/route.php");
  $GLOBALS['SOUTH'] = 19.23000000;
  $GLOBALS['NORTH'] = 18.90000000;
  $GLOBALS['EAST'] = 72.95500000;
  $GLOBALS['WEST'] = 72.81670000;
  $GLOBALS['RADIUS_DIST'] = 115;
  $GLOBALS['RADIUS'] = 0.001;
$route = new Route(0,0,0,0,0);
//$route1 = new Route(0,18.955,72.833,18.959,72.839);
//$route2 = new Route(0,18.955,72.833,18.959,72.839);
$route1 = new Route(39,19.000,72.9,19.046,72.8937);
$route2 = new Route(40,19.0009,72.9,19.046,72.8937);

$ans = $route->matchRoute($route1, $route2);  
echo "ans " . $ans . "\n";
?>
