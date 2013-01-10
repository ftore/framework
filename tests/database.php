<?php
include_once dirname(__FILE__) . '/../db/database.php';

try{
	$db = Db_Database::getInstance('mysql');
	$db = $db->getHandler();
	$db->connect();
	
	//======================= Example 1 =============================
	$result = $db->execute('SELECT * FROM users');
	for ($i = 0; $i < $result->num_rows; $i++)
	{
		$rows[] = (object) $result->fetch_array(MYSQLI_ASSOC);
	}
	echo '<pre>';
	print_r($rows);
	echo '</pre>';
	
	//======================= Example 2 =============================
	$row = $db->query()
				->from('users', array('name', 'email'))
				->first();
	echo '<pre>';
	print_r($row);
	echo '</pre>';
	
	//======================= Example 3 =============================
	$row = $db->query()
				->from('users', array('name' => 'Full_Name', 'email'))
				->all();
	echo '<pre>';
	print_r($row);
	echo '</pre>';
	
	//======================= Example 4 =============================
	$row = $db->query()
				->from('posts as p', array('p.*'))
				->join('users as u', 'u.id=p.uid', array('u.name','u.email'))
				->where('u.id = ?', 1)
				->all();
	
	echo '<pre>';
	print_r($row);
	echo '</pre>';
	
	echo $db->query()
			->from('posts as p', array('p.*'))
			->join('users as u', 'u.id=p.uid', array('u.name','u.email'))
			->where('u.id = ?', 1);
	echo '<br>';
	
	//======================= Example 5 =============================
	$count = $db->query()
				->from('users')
				->count();
	print_r($count);
	
	
}catch(Exception $e){
	echo $e->getMessage();
}