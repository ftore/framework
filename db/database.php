<?php
include_once dirname(__FILE__) . '/connector/mysql.php';

class Db_Database
{
	protected $_type;
	
	protected $_options;
	
	protected static $_instance;
	
	protected function __construct($type, $opitons = array())
	{
		$this->_type = $type;
		$this->_options = $opitons;
	}
	
	/**
	 * Get single instance of the class
	 * @param string $type
	 * @param array $options
	 * @return Db_Database
	 */
	
	public static function getInstance($type, $options = array())
	{
		if(self::$_instance === null)
		{
			self::$_instance = new Db_Database($type, $options);
		}
		return self::$_instance;
	}
	/**
	 * Gets the proper db instance connected
	 * @throws Exception
	 */
	public function getHandler()
	{
		switch ($this->_type)
		{
			case 'mysql':
				return new Db_Connector_Mysql($this->_options);
				break;
		
			default:
				throw new Exception('Unsupported type of db connector');
		}
	}
}