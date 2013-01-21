<?php
/** 
 * @author Christoff Trüter <christoff@cstruter.com>
 * @version 1.0
 * @copyright Copyright (c) 2011, CSTruter.com
 * @package MySQL_Strategies
*/

/**
 * Base class for creating MySQL strategy classes
 * @package MySQL_Strategies
 * @abstract
*/
abstract class MySQLStrategy
{
	protected $resource;

	/**
	* @var integer 
	*/
	protected $count;

	/**
	* @var MySQL
	*/
	protected $host;

	/**
	 * @param MySQL $host 
	*/
	public function __construct(MySQL $host) {
		$this->host = $host;
	}
	
	/**
	 *  Get number of affected rows in previous MySQL operation
	 * @return integer 
	*/	
	abstract public function AffectedRows();
	
	/**
	 * Get number of rows in result
	 * @return integer 
	*/	
	abstract public function Count();

	/**
	 * Escapes special characters in a string for use in a SQL statement
	 * @param string $value 
	 * @return string 
	*/
	abstract public function Escape($value);

	/**
	 * Get the ID generated in the last query
	 * @return integer 
	*/
	abstract public function LastId();
	
	/**
	 * Open a connection to a MySQL Server
	*/
	abstract public function Open();

	/**
	 * Executes a query that returns a set of rows e.g. select
	 * @return array
	*/
	abstract public function Query();
	
	/**
	 * Executes a query that doesn't return rows e.g. delete/insert/update
	 * @return integer
	*/
	abstract public function NonQuery();

	/**
	 * Executes a query that returns a single field/value e.g. count/sum
	 * @return integer
	*/	
	abstract public function Scalar();

	/**
	 * Close MySQL connection
	*/
	abstract public function Close();	
}

?>