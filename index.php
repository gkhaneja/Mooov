<?php
//require '/home/gourav/Mooov/trunk/autoload.php';
require("Rest/ServiceFactory.php");
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "/home/gourav/Mooov/trunk");
//echo "Welcome\n";
$url = $_SERVER['REQUEST_URI'];
$serviceFactory = new ServiceFactory($url);
$serviceFactory->serve();
?>
