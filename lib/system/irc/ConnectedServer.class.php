<?php

/**
 * Represents a connected server
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ConnectedServer {
	
	/**
	 * Contains the name of the server
	 * @var string
	 */
	protected $serverName = '';
	
	/**
	 * Contains the identifier of this server (e.g. a numeric)
	 * @var string
	 */
	protected $identifier = '';
	
	/**
	 * Contains additional data for this server
	 * @var array
	 */
	protected $metadata = array();
	
	/**
	 * Creates a new instance of type ConnectedServer
	 * @param	string	$serverName
	 * @param	string	$identifier
	 */
	public function __construct($serverName, $identifier) {
		$this->serverName = $serverName;
		$this->identifier = $identifier;
	}
	
	/**
	 * Returns the identifier of this server
	 * @return mixed
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Returns the server name of this server
	 * @return string
	 */
	public function getServerName() {
		return $this->serverName;
	}
	
	/**
	 * Returns additional data of this server
	 * @param	string	$variable
	 */
	public function __get($variable) {
		if (isset($this->metadata[$variable])) return $variable;
		return null;
	}
	
	/**
	 * Sets additinal data for this server
	 * @param	string	$variable
	 * @param	mixed	$value
	 */
	public function __set($variable, $value) {
		$this->metadata[$variable] = $value;
	}
	
	/**
	 * Converts the object to string
	 * Alias for ConnectedServer::getServerName()
	 * @see ConnectedServer::getServerName()
	 */
	public function __toString() {
		return $this->getServerName();
	}
}
?>