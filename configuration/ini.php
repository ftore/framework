<?php
class Configuration_Ini
{
	protected function _pair($config, $key, $value)
	{
		if (strstr($key, "."))
		{
			$parts = explode(".", $key, 2);
	
			if (empty($config[$parts[0]]))
			{
				$config[$parts[0]] = array();
			}
	
			$config[$parts[0]] = $this->_pair($config[$parts[0]], $parts[1], $value);
		}
		else
		{
			$config[$key] = $value;
		}
	
		return $config;
	}
	
	public function parse($path)
	{
		if (empty($path))
		{
			throw new Exception("\$path argument is not valid");
		}
		
		$config = array();
		
		ob_start();
		include("{$path}.ini");
		$string = ob_get_contents();
		ob_end_clean();
		
		$pairs = parse_ini_string($string);
		
		if ($pairs == false)
		{
			throw new Exception("Could not parse configuration file");
		}
		
		foreach ($pairs as $key => $value)
		{
			$config = $this->_pair($config, $key, $value);
		}
		
		return $config;
	}
}