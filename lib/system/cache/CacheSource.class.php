<?php

/**
 * Extends the memcache source
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class CacheSource extends Memcache {
	
	/**
	 * Contains a list of all created cache sources
	 * This will used to remove all resources at end of instance
	 * @var	array
	 */
	protected $cacheList = array();
	
	/**
	 * Contains the timeout in seconds
	 * @var	integer
	 */
	const CACHE_TIMEOUT = 0;
	
	/**
	 * Contains the deletion timeout in secound
	 * @var	integer
	 */
	const DELETE_TIMEOUT = 0;
	
	/**
	 * Add an item to the server
	 * Note: Stores variable var with key only if such key doesn't exist at the server yet
	 * @param	string	$key
	 * @param	mixed	$var
	 * @param	integer	$expire
	 */
	public function add($key, $var, $expire = self::CACHE_TIMEOUT) {
		// add to log
		$this->cacheList[] = $key;
		
		// add to cache
		return parent::add($key, $var, MEMCACHE_COMPRESSED, $expire);
	}
	
	/**
	 * Retrieve item from the server.
	 * Returns previously stored data if an item with such key exists on the server at this moment.
	 * @param	string	$key
	 */
	public function get($key) {
		// read value
		$val = parent::get($key, MEMCACHE_COMPRESSED);
		
		// try to unserialize
		try {
			if (unserialize($val) !== false) $val = unserialize($val);
		} Catch (Exception $ex) {
			// ignore
		}
		
		// return value
		return $val;
	}
	
	/**
	 * Delete item from the server
	 * Note: Deletes item with the key. If parameter timeout is specified, the item will expire after timeout seconds. Also you can use memcache_delete() function.
	 * @param	string	$key
	 * @param	integer	$timeout 
	 */
	public function delete($key, $timeout = self::DELETE_TIMEOUT) {
		if(array_search($key, $this->cacheList)) unset($this->cacheList[$key]);
		return parent::delete($key, $timeput);
	}
	
	/**
	 * Flush all existing items at the server
	 */
	public function flush() {
		// clear cache log
		$this->cacheList = array();
		
		// call parent method
		return parent::flush();
	}
	
	/**
	 * Tries to write and read cache from memcache source and returnes true if connection is still alive
	 */
	public function checkConnection() {
		try {
			$this->add('SERVICES_VERSION', SERVICES_VERSION);
			if ($this->get('SERVICES_VERSION') !== false)
				return true;
			else
				return false;
		} Catch (Exception $ex) {
			return false;
		}
	}
}
?>