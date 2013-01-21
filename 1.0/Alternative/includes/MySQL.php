<?php

/**
 * @author Christoff Trüter <christoff@cstruter.com>
 * @version 1.0 
 * @copyright Copyright (c) 2011, CSTruter.com
 * @package MySQL_Strategies
*/

/**
 * Class used for exposing/hosting the most appropriate or available strategy
 * @package MySQL_Strategies
*/
final class MySQL
{
	/**
	* @staticvar MySQL Singleton
	*/
	private static $_instance;
	
	/**
	* @var MySQLStrategy strategy
	*/
	private $_strategy;
	
	/**
	* @var bool Status of the connection
	*/
	private $_IsOpen = false;
	
	/**
	* @var array Contains all parameters used for binding a query
	*/
	private $_parameters = array();
	
	/**
	* @var string SQL Query
	*/
	public $query;
	
	/**
	* @var array Connection settings
	*/
	public $settings;
	
	/**
	* @param array $settings
	*/
	private function __construct(array $settings) {
		$this->settings = $settings;
		
		if (function_exists('mysqli_connect')) {
			$this->_strategy = new MySQLI_Strategy($this);
		} else if (function_exists('mysql_connect')) {
			$this->_strategy = new MySQL_Strategy($this);
		} else {
			throw new Exception("No compatible MySQL strategy found");
		}
	}
	
	/**
	* Instance & strategy available and connection open for queries
	* @return bool
	* @static
	*/
	private static function _IsInstance()
	{
		return ((isset(self::$_instance)) && (isset(self::$_instance->_strategy)) 
				&& (self::$_instance->_IsOpen == true));
	}
	
	/**
	* @param string $host The MySQL Server
	* @param string $username
	* @param string $password
	* @param string $database
	* @param integer $port Option parameter - default 3306
	* @static
	*/
	public static function Config($host, $username, $password, $database, $port = 3306)
	{
		self::Close();
		self::$_instance = new MySQL
		(
			array
			(
				'host'=> $host, 
				'username'=> $username,
				'password'=> $password, 
				'database'=> $database,
				'port'=> $port
			)		
		);
	}
	
	/**
	* Create a new SQL Query
	* @param string $query
	* @return MySQL
	*/
	public static function Create($query)
	{
		if (isset(self::$_instance))
		{
			self::$_instance->query = $query;
			self::$_instance->_parameters = array();
			return self::$_instance;
		}
		throw new MySQLException("MySQL configuration not set");
	}
	
	/**
	* Add parameter used for SQL Query
	* @param string $name
	* @param string|integer|bool|double|float $value
	* @param $type e.g. string|integer|boolean|double|float
	* @return MySQL
	*/
	public function Parameter($name, $value, $type = NULL) 
	{	
		if (!isset($name))
			throw new Exception("Parameter name can't be null");
			
		if ($name[0] != '_')
		{
			if (!isset($this->_parameters[$name]))
			{			
				if (isset($type)) 
				{
					if (in_array($type, array('boolean', 'integer', 'float', 'double', 'string'))) {				
						settype($value, $type);
					} else {
						throw new Exception("Invalid type '$type' specified for parameter '$name'");
					}
				}
				else if (is_array($value) || is_object($value)) {
						throw new Exception("Invalid type specified for parameter '$name'");
				}				
				if (is_string($value)) {
					$this->_parameters[$name] = str_replace('?', '?_', $value);
				} else {
					$this->_parameters[$name] = $value;
				}
				return $this;
			}
			throw new Exception("Parameter $name already added");
		}
		throw new Exception("Parameter $name, not allowed to start with an underscore");
	}
	
	/**
	* Method used for insuring that queries are safe for execution
	*/
	private function _Bind()
	{
		self::Open();
		$this->query = str_replace('?_', '?__', $this->query);
		
		foreach($this->_parameters as $key=>$value)
		{
			if (is_string($value)) {
				$value = "'".$this->_strategy->Escape($value)."'";
			} else if (is_bool($value)) {
				$value = ($value) ? 1 : 0;
			} else if (is_null($value)) {
				$value = 'NULL';
			}
			$this->query = str_replace('?'.$key, $value, $this->query, $count);
			
			if ($count == 0) {
				throw new Exception("Parameter $key not found");
			}
		}
		$this->query = str_replace('?_', '?', $this->query);
	}
	
	/**
	 * Executes and sanatize a query via strategy that returns a set of rows e.g. select
	 * @return array
	*/		
	public function Query()
	{
		$this->_Bind();
		return $this->_strategy->Query();
	}
	
	/**
	 * Executes and sanatize a query via strategy that returns a single field/value e.g. count/sum
	 * @return integer
	*/		
	public function NonQuery()
	{
		$this->_Bind();
		$this->_strategy->NonQuery();
		return self::AffectedRows();
	}	

	/**
	 * Executes and sanatize a query via strategy that returns a single field/value e.g. count/sum
	 * @param $type e.g. string|integer|boolean|double|float
	 * @return integer
	*/	
	public function Scalar($type = NULL)
	{
		$this->_Bind();
		$value = $this->_strategy->Scalar();
		
		if (isset($type)) 
		{
			if (in_array($type, array('boolean', 'integer', 'float', 'double', 'string'))) {				
				settype($value, $type);
			} else {
				throw new Exception("Invalid type '$type' specified for parameter '$name'");
			}
		}
		return $value;
	}

	/**
	 *  Get number of affected rows via strategy in previous MySQL operation
	 * @return integer
	 * @static
	*/	
	public static function AffectedRows()
	{
		if (self::_IsInstance()) {
			return self::$_instance->_strategy->AffectedRows();
		}
	}

	/**
	 * Get number of rows in result via strategy
	 * @return integer 
	 * @static
	*/	
	public static function Count()
	{
		if (self::_IsInstance()) {
			return self::$_instance->_strategy->Count();
		}
	}

	/**
	 * Get the ID generated in the last query via strategy
	 * @return integer
	 * @static
	*/	
	public static function LastId()
	{
		if (self::_IsInstance()) {
			return self::$_instance->_strategy->LastId();
		}
	}
	
	/**
	 * Open a connection to a MySQL Server via strategy
	 * @static
	*/	
	public static function Open()
	{
		if (!self::_IsInstance()) {
			self::$_instance->_strategy->Open();
			self::$_instance->_IsOpen = true;
		}
	}
	
	/**
	 * Close MySQL connection via strategy
	 * @static
	*/	
	public static function Close()
	{
		if (self::_IsInstance()) {
			self::$_instance->_strategy->Close();
			self::$_instance->_IsOpen = false;
		}
	}
	
	public function __destruct() {
		self::Close();
	}
}

?>