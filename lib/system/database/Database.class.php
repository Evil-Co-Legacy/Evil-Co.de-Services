<?php

/**
 * This class implements ground database features
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class Database {
	protected $linkID = 0;
	protected $queryCount = 0;
	protected $lastResult = null;
	protected $lastQuery = "";

	/**
	 * Returnes the first row
	 * @param	    string	$query
	 * @param	    integer	$limit
	 * @param	    integer	$offset
	 * @return	    mixed
	 */
	public function getFirstRow($query, $limit = 1, $offset = 0) {
		$limit = (preg_match('/LIMIT\s+\d/i', $query) ? 0 : $limit);

		$query = $this->handleLimitParameter($query, $limit, $offset);

		$result = $this->sendQuery($query);

		if (is_resource($result)) {
			$row = $this->fetchArray($result);

			if (is_array($row)) {
				return $row;
			}
		}

		return false;
	}

	/**
	 * Returns a query with limit and offset
	 * @param string $query
	 * @param integer $limit
	 * @param integer $offset
	 * @return string
	 */
	public function handleLimitParameter($query = '', $limit = 0, $offset = 0) {
		if ($limit != 0) {
			if ($offset > 0) $query .= ' LIMIT '.$offset.', '.$limit;
			else $query .= ' LIMIT '.$limit;
		}

		return $query;
	}

	/**
	 * Deconnects from the database
	 */
	public function shutdown() {
		// empty
	}
}
?>