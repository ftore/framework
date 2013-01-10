<?php

class Db_Query
{
	// db connector
	protected $_connector;
	
	protected $_from;
	
	protected $_fields;
	
	protected $_limit;
	
	protected $_offset;
	
	protected $_order;
	
	protected $_direction;
	
	protected $_join = array();
	
	protected $_where = array();
	
	/**
	 * Constructor
	 */
	public function __construct($params = array())
	{
		if(array_key_exists('connector', $params))
		{
			$this->_connector = $params['connector'];
		}
	}
	
	/**
	 * Quotes the value correctly according how
	 * MySQL will exptect it
	 */
	protected function _quote($value)
	{
		if(is_string($value))
		{
			$escaped = $this->_connector->escape($value);
			return "'$escaped'";
		}
		
		if(is_array($value))
		{
			$buffer = array();
			
			foreach ($value as $i)
			{
				array_push($buffer, $this->_quote($i));
			}
			
			$buffer = join(", ", $buffer);
			return "($buffer)";
		}
		
		if(is_null($value))
		{
			return "NULL";
		}
		
		if(is_bool($value))
		{
			return (int)$value;
		}
		
		return $this->_connector->escape($value);
	}
	
	/**
	 * Builds SELECT SQL query string
	 */
	protected function _buildSelect()
	{
		$fields = array();
		$where = '';
		$order = '';
		$limit = '';
		$join = '';
		
		$template = "SELECT %s FROM %s %s %s %s %s";
		
		foreach ($this->_fields as $table => $_fields)
		{
			foreach($_fields as $field => $alias)
			{
				if(is_string($field))
				{
					$fields[] = "$field AS $alias";
				}
				else
				{
					$fields[] = $alias;
				}
			}
		}
		
		$fields = join(', ', $fields);
		
		$_join = $this->_join;
		if(!empty($_join))
		{
			$join = join(' ', $_join);
		}
		
		$_where = $this->_where;
		if(!empty($_where))
		{
			$joined = join(' AND ', $_where);
			$where = "WHERE $joined";
		}
		
		$_order = $this->_order;
		if(!empty($_order))
		{
			$_direction = $this->_direction;
			$order = "ORDER BY $_order $_direction";
		}
		
		$_limit = $this->_limit;
		if(!empty($_limit))
		{
			$_offset = $this->_offset;
			if($_offset)
			{
				$limit = "LIMIT $_limit, $_offset";
			}
			else
			{
				$limit = "LIMIT $_limit";
			}
		}
		
		return sprintf($template, $fields, $this->_from, $join, $where, $order, $limit);
	}
	
	/**
	 * Builds INSERT SQL query string
	 */
	public function _buildInsert($data)
	{
		$fields = array();
		$values = array();
		$template = "INSERT INTO '%s' ('%s') VALUES (%s)";
		
		foreach($data as $field => $value)
		{
			$fields[] = $field;
			$values[] = $this->_quote($value);
		}
		
		$fields = join(', ', $fields);
		$values = join(', ', $values);
		
		return sprintf($template, $this->_from, $fields, $values);
	}
	
	/**
	 * Builds UPDATE SQL query string
	 */
	public function _buildUpdate($data)
	{
		$parts = array();
		$where = '';
		$limit = '';
		$template = "UPDATE %s SET %s %s %s";
		
		foreach($data as $field => $value)
		{
			$parts[] = "$field=" . $this->_quote($value);
		}
		
		$parts = join(', ', $parts);
		
		$_where = $this->_where;
		if(!empty($_where))
		{
			$joined = join(', ', $_where);
			$where = "WHERE $joined";
		}
		
		
		$_limit = $this->_limit;
		if(!empty($_limit))
		{
			$_offset = $this->_offset;
			$limit = "LIMIT $_limit $_offset";
		}
		
		return sprintf($template, $parts, $where, $limit);
	}
	
	/**
	 * Builds DELETE SQL query string
	 */
	public function _buildDelete()
	{
		$where = '';
		$limit = '';
		$template = "DELETE FROM %s %s %s";
		
		$_where = $this->_where;
		if(!empty($_where))
		{
			$joined = join(', ', $_where);
			$where = "WHERE $joined";
		}
		
		
		$_limit = $this->_limit;
		if(!empty($_limit))
		{
			$_offset = $this->_offset;
			$limit = "LIMIT $_limit $_offset";
		}
		
		return sprintf($template, $this->_from, $where, $limit);
	}
	
	/**
	 * Inserts into, or updates data in database
	 */
	public function save($data)
	{
		$isInsert = sizeof($this->_where) == 0;
		if($isInsert)
		{
			$sql = $this->_buildInsert($data);
		}
		else
		{
			$sql = $this->_buildUpdate($data);
		}
		
		$result = $this->_connector->execute($sql);
		
		if($result === false)
		{
			throw new Exception();
		}
		
		if($isInsert)
		{
			return $this->_connector->lastInsertId;
		}
		
	}
	
	/**
	 * Deletes data from database
	 */
	public function delete()
	{
		$sql = $this->_buildDelete();
		$result = $this->_connector->execute($sql);
		
		if($result === false)
		{
			throw new Exception();
		}
		
		return $this->_connector->affectedRows;
	}
	
	/**
	 * Convenience methods for altering queries,
	 * namely from, limit and join, order, where
	 */
	public function from($from, $fields = array("*"))
	{
		if(empty($from))
		{
			throw new Exception("Invalid argument");
		}
		
		$this->_from = $from;
		
		if($fields)
		{
			$this->_fields[$from] = $fields;
		}
		return $this;
	}
	
	public function limit($limit, $page = '1')
	{
		if(empty($limit))
		{
			throw new Exception("Invalid argument");
		}
		
		$this->_limit = $limit;
		$this->_offset = $limit * ($page - 1);
		
		return $this;
	}
	
	public function join($join, $on, $fields = array())
	{
		if(empty($join) || empty($on))
		{
			throw new Exception("Invalid argument");
		}
		
		$this->_fields += array($join => $fields);
		$this->_join[] = "JOIN $join ON $on";
		
		return $this;
	}
	
	public function order($order, $direction = 'asc')
	{
		if(empty($order))
		{
			throw new Exception("Invalid argument");
		}
		
		$this->_order = $order;
		$this->_direction = $direction;
		
		return $this;
	}
	
	public function where()
	{
		$arguments = func_get_args();
		
		if(sizeof($arguments) < 1)
		{
			throw new Exception("Invalid argument");
		}
		
		$arguments[0] = preg_replace("/\?/", "%s", $arguments[0]);
		
		foreach (array_splice($arguments, 1, null, true) as $i => $parameter)
		{
			$arguments[$i] = $this->_quote($arguments[$i]);
		}
		
		$this->_where[] = call_user_func_array("sprintf", $arguments);
		
		return $this;
	}
	
	public function first()
	{
		$limit = $this->_limit;
		$offset = $this->_offset;
		
		$this->limit(1);
		
		$first = $this->all();
		return $first;
	}
	
	public function all()
	{
		$sql = $this->_buildSelect();
		$result = $this->_connector->execute($sql);
		
		if($result === false)
		{
			$error = $this->_connector->lastError;
			throw new Exception('There was an error with your SQL query: ' . $error);
		}
		
		$rows = array();
		for($i = 0; $i < $result->num_rows; $i++)
		{
			$rows[] = $result->fetch_array(MYSQLI_ASSOC);
		}
		
		return $rows;
	}
	
	public function count()
	{
		$this->_fields = array($this->_from => array("COUNT(1)" =>"rows"));
		
		$this->limit(1);
		$row = $this->first();
		return $row[0]['rows'];
	}
	
	public function __toString()
	{
		$sql = $this->_buildSelect();
		return $sql;
	}
}