<?php

/**
 * @author Christoff Trüter <christoff@cstruter.com>
 * @version 1.0 
 * @copyright Copyright (c) 2011, CSTruter.com
 * @package MySQL_Strategies 
*/

/**
 * MySQL extension based strategy
 * @package MySQL_Strategies
 * @subpackage Strategies
*/
class MySQL_Strategy extends MySQLStrategy
{
	/**
	 *  Get number of affected rows in previous MySQL operation
	 * @return integer 
	*/
	public function AffectedRows() { return mysql_affected_rows($this->resource); }
	
	/**
	 * Get number of rows in result
	 * @return integer 
	*/	
	public function Count() { return $this->count; }
	
	/**
	 * Escapes special characters in a string for use in a SQL statement
	 * @param string $value 
	 * @return string 
	*/	
	public function Escape($value) { return mysql_real_escape_string($value, $this->resource); }
	
	/**
	 * Get the ID generated in the last query
	 * @return integer 
	*/	
	public function LastId() { return mysql_insert_id($this->resource); }
	
	/**
	 * Close MySQL connection
	*/	
	public function Close() { mysql_close($this->resource); }
	
	/**
	 * Open a connection to a MySQL Server
	*/	
	public function Open()
	{
		$this->resource = @mysql_connect($this->host->settings['host'].':'.$this->host->settings['port'], 
										 $this->host->settings['username'], $this->host->settings['password']);
											
		if (!$this->resource) {
			throw new MySQLException(mysql_error());
		}
		
		if (!mysql_select_db($this->host->settings['database'], $this->resource)) {
			throw new MySQLException(mysql_error(), mysql_errno($this->resource));
		}
	}

	/**
	 * Executes a query that returns a set of rows e.g. select
	 * @return array
	*/	
	public function Query()
	{
		$result = mysql_query($this->host->query, $this->resource);
		$this->count = 0;
		
		if (!$result)
			throw new MySQLException(mysql_error(), mysql_errno($this->resource));
			
		if (is_resource($result))
		{	
			while ($row = mysql_fetch_assoc($result)) 
			{
				if (!isset($rows)) {
					$rows = array();
				}
				$rows[] = $row;		
			}
			$this->count = mysql_num_rows($result);
			mysql_free_result($result);
		}
		return (isset($rows)) ? $rows : NULL;
	}
	
	/**
	 * Executes a query that doesn't return rows e.g. delete/insert/update
	 * @return integer
	*/	
	public function NonQuery()
	{
		$result = mysql_query($this->host->query, $this->resource);
		
		if (!$result)
			throw new MySQLException(mysql_error(), mysql_errno($this->resource));

		if (is_resource($result)) {
			throw new MySQLException("Query returned result set, rather use Query() method");
		}
	}
	
	/**
	 * Executes a query that returns a single field/value e.g. count/sum
	 * @return integer
	*/	
	public function Scalar()
	{
		$result = mysql_query($this->host->query, $this->resource);
	
		if (!$result)
			throw new MySQLException(mysql_error(), mysql_errno($this->resource));
			
		if (is_resource($result))
		{				
			if (mysql_num_rows($result) > 1) {
				throw new MySQLException("Query returned more than one result");
			}
			$row = mysql_fetch_array($result, MYSQL_NUM);
			mysql_free_result($result);
		}
		return (isset($row)) ? $row[0] : NULL;
	}
}

?>