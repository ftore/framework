<?php
include_once dirname(__FILE__) . '/../connector.php';
include_once dirname(__FILE__) . '/../query/mysql.php';

class Db_Connector_Mysql extends Db_Connector
{
	// db handler
	protected $_service;
	
	// db host
	protected $_host;
	
	// db username
	protected $_username;
	
	// db password
	protected $_password;
	
	// db name
	protected $_schema;
	
	// db port
	protected $_port = '3306';
	
	// table type
	protected $_engine = 'InnoDB';
	
	protected $_isConnected = false;
	
	/**
	 * Constructor
	 */
	public function __construct($options)
	{
		parent::__construct();
		
		if($this->_config['database']['default']['type'] == 'mysql')
		{
			$this->_host = $this->_config['database']['default']['host'];
			$this->_username = $this->_config['database']['default']['username'];
			$this->_password = $this->_config['database']['default']['password'];
			$this->_schema = $this->_config['database']['default']['dbname'];
			$this->_port = $this->_config['database']['default']['port'];
		}
		else
		{
			throw new Exception('Unsupported db type');
		}
	}
	
	/**
	 * Checks if connected to the database
	 */
	protected function _isValidService()
	{
		$isEmpty = empty($this->_service);
		$isInstance = $this->_service instanceof mysqli;
		
		if($this->_isConnected && $isInstance && !$isEmpty)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Connect to the database
	 */
	
	public function connect()
	{
		if(!$this->_isValidService())
		{
			$this->_service = new mysqli(
					$this->_host,
					$this->_username,
					$this->_password,
					$this->_schema,
					$this->_port
				);
			
			if($this->_service->connect_error)
			{
				throw new Exception("Unable to connect to database");
			}
			
			$this->_isConnected = true;
		}
		
		return $this;
	}
	
	/**
	 * Disconnects from database
	 */
	public function disconnect()
	{
		if($this->_isValidService())
		{
			$this->_isConnected = false;
			$this->_service->close();
		}
		return $this;
	}
	
	/**
	 * Returns corresponding query instance
	 */
	public function query()
	{
		return new Db_Query_Mysql(array(
				"connector" => $this
			));
	}
	
	/**
	 * Executes the provided sql statement
	 */
	public function execute($sql)
	{
		if(!$this->_isValidService())
		{
			throw new Exception("Not connected to the valid service");
		}
		
		return $this->_service->query($sql);
	}
	
	/**
	 * Escapes the provided value to make it safe for queries
	 */
	public function escape($value)
	{
		if(!$this->_isValidService())
		{
			throw new Exception("Not connected to the valid service");
		}
		return $this->_service->real_escape_string($value);
	}
	
	/**
	 * Returns the ID of the last row
	 * to be inserted
	 */
	public function getLastInsertedId()
	{
		if(!$this->_isValidService())
		{
			throw new Exception("Not connected to the valid service");
		}
		return  $this->_service->insert_id;
	}
	
	/**
	 * Returns the number of rows affected
	 * by the last SQL query executed
	 */
	public function getAffectedRows()
	{
		if(!$this->_isValidService())
		{
			throw new Exception("Not connected to the valid service");
		}
		return  $this->_service->affected_rows;
	}
	
	/**
	 * Returns the last error of occur
	 * by the last SQL query executed
	 */
	public function getLastError()
	{
		if(!$this->_isValidService())
		{
			throw new Exception("Not connected to the valid service");
		}
		return  $this->_service->error;
	}
	
}