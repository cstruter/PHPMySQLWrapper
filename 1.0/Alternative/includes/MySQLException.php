<?php

/**
 * @author Christoff Trüter <christoff@cstruter.com>
 * @version 1.0 
 * @copyright Copyright (c) 2011, CSTruter.com
 * @package MySQL_Connectors
*/


/**
 * Exception class for throwing MySQL Exceptions
 * @package MySQL_Connectors
 * @subpackage Exceptions
*/
class MySQLException extends Exception 
{
	/**
	* @var integer
	*/
	public $errorno;
	
	public function __construct($message, $errorno = NULL)
	{
		parent::__construct($message);
		$this->errorno = $errorno;
	}
}

?>