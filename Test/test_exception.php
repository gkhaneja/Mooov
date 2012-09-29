<?php
require_once '../objects/exception.php';

$error = array ('code' => '1' , 'error' => 'No results found');
$exception  = new ExceptionHandler($error);

echo  $exception->m_error->getMessage();