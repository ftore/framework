<?php
include_once 'configuration/ini.php';

class Configuration
{
	protected $_type;
	
	public function __construct($type = 'ini')
	{
		$this->_type = $type;
	}
	
	public function inilialize()
	{
		if(!$this->_type)
		{
			throw new Exception('Invalid type');
		}
		
		switch($this->_type)
		{
			case 'ini':
				return new Configuration_Ini();
				break;
			default:
				throw new Exception('Unsupported type');
		}
	}
}