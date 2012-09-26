<?php
require '/home/gourav/server/trunk/Server/autoload.php';
//echo "Welcome\n";
$url = $_SERVER['REQUEST_URI'];
$serviceFactory = new ServiceFactory($url);
$serviceFactory->serve();
?>
