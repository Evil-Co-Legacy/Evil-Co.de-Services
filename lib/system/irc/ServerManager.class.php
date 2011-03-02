<?php
// service imports
require_once(SDIR.'lib/system/irc/ConnectedServer.class.php');

/**
 * Manages servers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ServerManager implements Iterator {
	
	/**
	 * Contains all registered servers
	 * @var array<ConnectedServer>
	 */
	protected $serverList = array();
	
	/**
	 * Contains the pointer for iterator feature
	 * @var integer
	 */
	protected $serverListPointer = 0;
	
	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// get keys
		$keys = array_keys($this->serverList);
		
		// get identifier
		$identifier = $keys[$this->serverListPointer];
		
		// return ConnectedServer object
		return $this->serverList[$identifier];
	}
	
	/**
	 * Returnes the server with the given identifier
	 * @param	string	$identifier
	 */
	public function getServerByIdentifier($identifier) {
		foreach($this as $server) {
			if ($server->getIdentifier() == $identifier) return $server;
		}
		return null;
	}
	
	/**
	 * Returnes the server with the given name
	 * @param	string	$serverName
	 */
	public function getServerByName($serverName) {
		foreach($this as $server) {
			if ($server->getServerName() == $serverName) return $server;
		}
		return null;
	}
	
	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$keys = array_keys($this->serverList);
		
		// return current keys
		return $keys[$this->serverListPointer];
	}
	
	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->serverListPointer;
	}
	
	/**
	 * Registeres a new server
	 * @param	string	$serverName
	 * @param	mixed	$identifier
	 * @return mixed
	 * @throws RecoverableException
	 */
	public function registerServer($serverName, $identifier) {
		// validate
		if (isset($this->serverList[$identifier])) throw new RecoverableException("A server with identifier '".$identifier."' is already registered");
		
		// add to list
		$this->serverList[$identifier] = new ConnectedServer($serverName, $identifier);
		
		// return identifier
		return $identifier;
	}
	
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->serverListPointer = 0;
	}
	
	/**
	 * Removes a server
	 * @param	mixed	$identifier
	 * @return void
	 * @throws RecoverableException
	 */
	public function unregisterServer($identifier) {
		// validate
		if (!isset($this->serverList[$identifier])) throw new RecoverableException("Tried to unregister unknown server '".$identifier."'");
		
		// unregister
		unset($this->serverList[$identifier]);
	}
	
	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		// get keys
		$keys = array_keys($this->serverList);
		
		return (isset($keys[$this->serverListPointer]));
	}
}
?>