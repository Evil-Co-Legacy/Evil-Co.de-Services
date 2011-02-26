<?php
// imports
require_once(SDIR.'lib/system/irc/protocol/Protocol.class.php');
require_once(SDIR.'lib/system/irc/protocol/inspircd/InspIRCdProtocolParser.class.php');

/**
 * Contains methods for IRCd linking protocols (Communicating with connected server)
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class InspIRCdProtocol implements Protocol {
	
	/**
	 * Contains the version of this protocol (Used for CAPAB command)
	 * @var string
	 */
	const PROTOCOL_VERSION = '1202';
	
	/**
	 * Contains a hmac key
	 * @var string
	 */
	protected $hmacKey = null;

	/**
	 * Contains true while the connection is still alive
	 * @var boolean
	 */
	protected $isAlive = false;
	
	/**
	 * Contains a reference to supportedTypes array
	 * @var array
	 */
	protected $supportedTypes = null;
	
	/**
	 * @see Protocol::__construct()
	 */
	public function __construct(&$supportedTypes) {
		$this->supportedTypes = &$supportedTypes;
	}
	
	/**
	 * Generates a hmac key
	 */
	protected function generateHMACKey() {
		// generate hmac key
		if ($this->hmacKey === null) $this->hmacKey = StringUtil::getRandomID();
		
		return $this->hmacKey;
	}
	
	/**
	 * @see Protocol::initConnection()
	 */
	public function initConnection() {
		// check for hmac methods
		if (Services::getConfiguration()->connection->hmac != 'none' and !function_exists('hash_hmac')) throw new ProtocolException("HMAC isn't supported by php installation");
		
		// send CAPAB
		Services::getConnection()->sendLine("CAPAB START");
		
		// send information about this server
		Services::getConnection()->sendLine("CAPAB CAPABILITIES PROTOCOL=".self::PROTOCOL_VERSION.(Services::getConfiguration()->connection->hmac != 'none' ? "CHALLENGE=".$this->generateHMACKey() : ""));
		
		// end capab
		Services::getConnection()->sendLine("CAPAB END");
		
		// start connection loop
		$synched = false;
		
		do {
			if (Services::getConnection()->check() > 0) { // we'll only read here if lines are available at socket
				// handle connection commands
				$synched = InspIRCdProtocolParser::handleConnectionCommand($line);
			}
		} while(!$synched);
		
		// get connection info
		$this->connectionInformation = InspIRCdProtocolParser::getConnectionInformation();
		
		// send server information
		if (Services::getConfiguration()->connection->hmac != 'none')
			Services::getConnection()->sendLine("SERVER ".Services::getConfiguration()->connection->name." HMAC-".strtoupper(Services::getConfiguration()->connection->hmac).":".hash_hmac(Services::getConfiguration()->connection->hmac, Services::getConfiguration()->connection->password, $this->generateHMACKey())." 0 ".Services::getConfiguration()->connection->numeric." :Evil-Co.de Services");
		else
			Services::getConnection()->sendLine("SERVER ".Services::getConfiguration()->connection->name." ".Services::getConfiguration()->connection->password." 0 ".Services::getConfiguration()->connection->numeric." :Evil-Co.de Services");
			
		// set connection flag
		$this->isAlive = true;
		
		// start main loop
		$this->listen();
	}
	
	/**
	 * @see Protocol::isAlive()
	 */
	public function isAlive() {
		return $this->isAlive;
	}
	
	/**
	 * Connection main loop
	 */
	protected function listen() {
		while($this->isAlive() and Services::getConnection()->isAlive()) {
			// handle server commands
			if (Services::getConnection()->check()) { // we'll only parse new lines if there are changes at socket
				$line = Services::getConnection()->readLine();
				InspIRCdProtocolParser::handleCommand($line);
			}
			
			// handle timers
			if (Services::getTimerManager()->check()) {
				Services::getTimerManager()->execute();
			}
		}
	}
	
	/**
	 * Handles calls to our send<Command>() methods
	 * @param	string	$methodName
	 * @param	array	$arguments
	 * @return void
	 * @throws RecoverableException
	 */
	public function __call($methodName, $arguments) {
		if (substr($methodName, 0, 3) == 'send') {
			// try to find correct format method
			if (method_exists($this, 'format'.substr($methodName, 3))) return $this->sendLine(call_user_func_array(array($this, 'format'.substr($methodName, 3)), $arguments));
		}
		
		// throw exception ...
		throw new RecoverableException("Method '".$methodName."' does not exist in class ".get_class($this));
	}
}
?>