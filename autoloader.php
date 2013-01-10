<?php

function autoload($class)
{
	$paths = explode(PATH_SEPARATOR, get_include_path());
	
	$flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE;
	$file = strtolower(str_replace("_", DIRECTORY_SEPARATOR, trim($class, "\\"))) . '.php';
	
	foreach ($paths as $path)
	{
		$combined = $path . DIRECTORY_SEPARATOR . $file;
		echo $combined.'<br>';
		if(file_exists($combined))
		{
			echo $combined.'<br>';
			include_once $combined;
			return;
		}
	}
	
	throw new Exception("$class not found");
}

class Autoloader
{
	public static function autoload($class)
	{
		autoload($class);
	}
}

spl_autoload_register('autoload');
spl_autoload_register(array('autoloader', 'autoload'));
