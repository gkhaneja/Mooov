<?php

function __autoload($class_name) {
	if(preg_match('/Service/',$class_name)){
    	include 'Rest/' . $class_name . '.php';
	} else {
			$class_name = strtolower($class_name);
    	include 'objects/' . $class_name . '.php';
	}
}
?>
