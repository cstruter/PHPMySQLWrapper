<?php

/**
 * @author Christoff Trüter <christoff@cstruter.com>
 * @version 1.0 
 * @copyright Copyright (c) 2011, CSTruter.com
 * @package MySQL_Strategies
*/


/**
 * MySQLI extension based strategy
 * @package MySQL_Strategies
 * @subpackage Strategies
*/
class MySQLI_Strategy extends MySQLStrategy
{
	/**
	 *  Get number of affected rows in previous MySQL operation
	 * @return integer 
	*/	
	public function AffectedRows() { return $this->resource->affected_rows; }
	
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
	public function Escape($value) { return $this->resource->real_escape_string($value); }	
	
	/**
	 * Get the ID generated in the last query
	 * @return integer 
	*/
	public function LastId() { return $this->resource->insert_id; }
	
	/**
	 * Close MySQL connection
	*/	
	public function Close() { $this->resource->close(); }
	
	/**
	 * Open a connection to a MySQL Server
	*/	
	public function Open()
	{
		$this->resource = @new mysqli($this->host->settings['host'], $this->host->settings['username'], 
										$this->host->settings['password'], $this->host->settings['database'], 
										$this->host->settings['port']);

		if (mysqli_connect_errno())	
		{
			$errno = mysqli_connect_errno();
			unset($this->resource);
			throw new MySQLException(mysqli_connect_error(), $errno);
		}
	}
	
	/**
	 * Executes a query that returns a set of rows e.g. select
	 * @return array
	*/
	public function Query()
	{
		$result = $this->resource->query($this->host->query, MYSQLI_USE_RESULT);
		$this->count = 0;
		
		if ($this->resource->error)
			throw new MySQLException($this->resource->error, $this->resource->errno);
		
		if (is_object($result))
		{	
			while ($row = $result->fetch_assoc()) 
			{
				if (!isset($rows)) {
					$rows = array();
				}
				$rows[] = $row;		
			}
			$this->count = $result->num_rows;
			$result->close();
		}
		return (isset($rows)) ? $rows : NULL;
	}
	
	/**
	 * Executes a query that doesn't return rows e.g. delete/insert/update
	 * @return integer
	*/	
	public function NonQuery()
	{
		$result = $this->resource->query($this->host->query, MYSQLI_STORE_RESULT);
	
		if ($this->resource->error)
			throw new MySQLException($this->resource->error, $this->resource->errno);

		if (is_object($result)) {
			throw new MySQLException("Query returned result set, rather use Query() method");
		}
	}
	
	/**
	 * Executes a query that returns a single field/value e.g. count/sum
	 * @return integer
	*/		
	public function Scalar()
	{
		$result = $this->resource->query($this->host->query);
	
		if ($this->resource->error)
			throw new MySQLException($this->resource->error, $this->resource->errno);
		
		if (is_object($result))
		{				
			if ($result->num_rows > 1) {
				throw new MySQLException("Query returned more than one result");
			}
			$row = $result->fetch_array(MYSQLI_NUM);
			$result->close();
		}
		return (isset($row)) ? $row[0] : NULL;
	}
}

?>