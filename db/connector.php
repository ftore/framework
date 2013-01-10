<?php
include_once dirname(__FILE__) . '/../configuration.php'; 

class Db_Connector
{
	protected $_config = array();
	
	public function initialize()
	{
		return $this;
	}
	
	public function __construct()
	{
		$configObj = new Configuration();
		$configObj = $configObj->inilialize();
		
		$this->_config = $configObj->parse(dirname(__FILE__) . '/../configuration');
	}
}