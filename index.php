<?php
require '/home/gourav/Mooov/trunk/autoload.php';
//echo "Welcome\n";
$url = $_SERVER['REQUEST_URI'];
$serviceFactory = new ServiceFactory($url);
$serviceFactory->serve();
?>
