<?php
class Db_Database
{
	protected $_type;
	
	protected $_options;
	
	protected static $_instance;
	
	protected function __construct($type, $opitons = array())
	{
		$this->_type = $type;
		
		switch ($this->_type)
		{
			case 'mysql':
				return new Db_Connector_Mysql($this->_options);
				break;
				
			default:
				throw new Exception('Unsupported type of db connector');
		}
	}
	
	public static function getInstance($type, $options = array())
	{
		if(self::$_instance === null)
		{
			self::$_instance = new Db_Database($type, $options);
		}
		return self::$_instance;
	}
}