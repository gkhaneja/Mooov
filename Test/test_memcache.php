<?php

$memcache_obj = memcache_connect("localhost", 11211);

/* procedural API */
//memcache_add($memcache_obj, 'var_key', 'test variable', false, 30);

/* OO API */
$memcache_obj->add('var_key', 'test variable', false, 30);


?>
