<?php
// imports
require_once(SDIR.'lib/system/database/Database.class.php');

/**
 * This class implements a database connection for mysql servers
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class MySQLDatabase extends Database {

	/**
	 * Connects to a MySQL Database
	 * @param	    string	$dbName
	 * @param	    string	$dbUser
	 * @param	    string	$dbPassword
	 * @param	    string	$dbHost
	 * @param	    string	$dbCharset
	 */
	public function __construct($dbName, $dbUser, $dbPassword, $dbHost, $dbCharset = 'UTF8') {
		$this->connect($dbHost, $dbUser, $dbPassword);
		$this->selectDatabase($dbName);
		$this->setCharset($dbCharset);
	}

	/**
	 * Connects to the database
	 * @param	    string	$dbHost
	 * @param	    string	$dbUser
	 * @param	    string	$dbPassword
	 */
	public function connect($dbHost, $dbUser, $dbPassword) {
		$this->linkID = mysql_connect($dbHost, $dbUser, $dbPassword, true);

		if ($this->linkID === false) {
		    throw new Exception("Connecting to MySQL server '".$dbHost."' failed.");
		}
	}

	/**
	 * Selects the Database
	 * @param	    string	$dbName
	 */
	public function selectDatabase($dbName) {
		if (@mysql_select_db($dbName, $this->linkID) === false) {
			throw new Exception("Cannot use database ".$dbName);
		}
	}

	/**
	 * Sets the database charset
	 * @param	    string	$dbCharset
	 */
	public function setCharset($charset) {
		try {
			$this->sendQuery("SET NAMES '".$this->escapeString($charset)."'");
		}
		catch (Exception $e) {
			// ignore
		}
	}

	/**
	 * Closes the connection
	 */
	public function shutdown() {
		mysql_close($this->linkID);
	}

	/**
	 * Escapes the given string
	 * @param	    string	$str
	 * @return	    string
	 */
	public function escapeString($str) {
		return mysql_real_escape_string($str, $this->linkID);
	}

	/**
	 * Sends a query to the database
	 * @param	    string	$sql
	 * @return	    object
	 */
	public function sendQuery($sql) {
		$result = mysql_query($sql);
		$this->lastResult = $result;
		if ($result === false) {
			throw new Exception("Invalid SQL: " . $sql . " Message: ".$this->getErrorDesc());
		}
		return $result;
    }

	/**
	 * Returns an array
	 * @param	    object	$result
	 * @return	    array
	 */
	public function fetchArray($result = null) {
		if (!is_resource($result)) {
			$result = $this->lastResult;
		}
		return mysql_fetch_array($result);
	}

	/**
	 * Returns a query with limit and offset
	 * @param	    string	$query
	 * @param	    integer	$limit
	 * @param	    integer	$offset
	 * @return	    string
	*/
	public function handleLimitParameter($query = '', $limit = 0, $offset = 0) {
		if ($limit != 0) {
			if ($offset > 0) $query .= ' LIMIT '.$offset.', '.$limit;
			else $query .= ' LIMIT '.$limit;
		}

		return $query;
	}

	/**
	 * Returns the insert id
	 * @return	    integer
	 */
	public function getInsertID() {
		return mysql_insert_id();
	}

	/**
	 * Returns the number of rows
	 * @param	    resource	    $result
	 * @return	    integer
	 */
	public function getNumRows($result = null) {
		if (!is_object($result)) $result = $this->lastResult;
		$numRows = mysql_num_rows($result);
		if ($numRows === false) {
			throw new Exception("Cannot count rows");
		}
		return $numRows;
	}

	/**
	 * Returns MySQL error number for last error.
	 *
	 * @return	    integer	MySQL error number
	 */

	public function getErrorNumber() {
		if (!($errorNumber = @mysql_errno($this->linkID))) {
			$errorNumber = @mysql_errno();
		}
		return $errorNumber;
	}

	/**
	 * Returns MySQL error description for last error.
	 *
	 * @return	    string	MySQL error description
	 */
	public function getErrorDesc() {
		if (!($errorDesc = @mysql_error($this->linkID))) {
			$errorDesc = @mysql_error();
		}
		return $errorDesc;
	}

	/**
	 * Returnes the Database type
	 * @return	    string
	 */
	public function getDBType() {
		return 'MySQL';
	}

	/**
	 * Returnes the MySQL-Server version
	 * @return	    string
	 */
	public function getVersion() {
		$result = $this->getFirstRow('SELECT VERSION() AS version');
		if (isset($result['version'])) {
		    return $result['version'];
		}
	}
}
?>