<?php
include_once '../configuration.php';

$config = new Configuration();
$config = $config->inilialize();

$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'configuration';

$config_vars = $config->parse($path);
var_dump($config_vars);