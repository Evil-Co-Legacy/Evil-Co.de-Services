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
	 *
	 * @var	array<array>
	 */
	protected $cacheList = array();
	
	/**
	 * Contains the timeout in seconds
	 *
	 * @var	integer
	 */
	const CACHE_TIMEOUT = 0;
	
	/**
	 * Contains the deletion timeout in seconds
	 -
	 * @var	integer
	 */
	const DELETE_TIMEOUT = 0;
	
	/**
	 * Contains the time interval which should be used to check connection
	 *
	 * @var	integer
	 */
	const STATUS_CACHETIME = 10;
	
	/**
	 * Contains the maximum age of cache cache (Senseless name). This wrapper will cache values of memcache to fix some problems with connection
	 *
	 * @var	integer
	 */
	const CACHE_VARTIMEOUT = 1;
	
	/**
	 * Contains the timestamp of the last status check
	 *
	 * @var	integer
	 */
	protected $lastStatusCheck = null;
	
	/**
	 * Add an item to the server
	 * Note: Stores variable var with key only if such key doesn't exist at the server yet
	 *
	 * @param	string	$key
	 * @param	mixed	$var
	 * @param	integer	$expire
	 * @return	void
	 */
	public function add($key, $var, $expire = self::CACHE_TIMEOUT) {
		// add to log
		$this->cacheList[$key] = array('timestamp' => time(), 'value' => $var);
		
		// add to cache
		if (self::get($key) !== false)
			parent::set($key, $var, MEMCACHE_COMPRESSED, $expire);
		else
			parent::add($key, $var, MEMCACHE_COMPRESSED, $expire);
		
		// log 
		if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Stored key '".$key."' with hash '".sha1((is_array(self::get($key)) ? serialize(self::get($key)) : self::get($key)))."'");
	}
	
	/**
	 * Retrieve item from the server.
	 * Returns previously stored data if an item with such key exists on the server at this moment.
	 *
	 * @param	string	$key
	 * @return	mixed
	 */
	public function get($key) {
		if (isset($this->cacheList[$key])) {
			if (($this->cacheList[$key]['timestamp'] + self::CACHE_VARTIMEOUT) > time())
				return $this->cacheList[$key]['value'];
			else
				unset($this->cacheList[$key]);
		}
		
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
	 *
	 * @param	string	$key
	 * @param	integer	$timeout 
	 * @return	unknown
	 */
	public function delete($key, $timeout = self::DELETE_TIMEOUT) {
		if(isset($this->cacheList[$key])) unset($this->cacheList[$key]);
		return parent::delete($key, $timeput);
	}
	
	/**
	 * Flush all existing items at the server
	 * 
	 * @return	unknown
	 */
	public function flush() {
		// clear cache log
		$this->cacheList = array();
		
		// call parent method
		return parent::flush();
	}
	
	/**
	 * Tries to write and read cache from memcache source and returnes true if connection is still alive
	 *
	 * @return boolean
	 */
	public function checkConnection() {
		// read cached return value
		if ($this->lastStatusCheck !== null) if (($this->lastStatusCheck + self::STATUS_CACHETIME) > time()) return true;
		
		// update timestamp
		$this->lastStatusCheck = time();
		
		// load data
		try {
			$this->add('SERVICES_VERSION', SERVICES_VERSION);
			if ($this->get('SERVICES_VERSION') !== false) {
				// to much debug
				// if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("[Memcache] Ping -> Pong");
				return true;
			} else
				return false;
		} Catch (Exception $ex) {
			return false;
		}
	}
}
?>