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
	 * Contains true if the connection is ready for msgs and other funny things
	 * @var boolean
	 */
	protected $isReady = false;
	
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
	 * Formates the given message string
	 * @param	string	$target
	 * @param	string	$message
	 */
	public function formatMessage($target, $message) {
		return "PRIVMSG ".$target." :".$message;
	}
	
	/**
	 * Formates the ping command
	 * @param	string	$target
	 * @return void
	 */
	public function formatPing($target) {
		return "PING ".Services::getConfiguration()->connection->numeric." ".$target;
	}
	
	/**
	 * Formates the pong command
	 * @param	string	$target
	 * @return void
	 */
	public function formatPong($target) {
		return "PONG ".Services::getConfiguration()->connection->numeric." ".$target;
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
	 * @see Protocol::handleException()
	 */
	public function handleException(ProtocolException $ex) {
		// send error message to server
		if ($this->isAlive()) Services::getConnection()->sendLine("ERROR :".$ex->getMessage());
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
		Services::getConnection()->sendLine("CAPAB CAPABILITIES :PROTOCOL=".self::PROTOCOL_VERSION.(Services::getConfiguration()->connection->hmac != 'none' ? " CHALLENGE=".$this->generateHMACKey() : ""));
		
		// end capab
		Services::getConnection()->sendLine("CAPAB END");
		
		// start connection loop
		$synched = false;
		
		do {
			if (Services::getConnection()->check() > 0) { // we'll only read here if lines are available at socket
				// handle connection commands
				$synched = InspIRCdProtocolParser::handleConnectionCommand(Services::getConnection()->readLine());
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
		
		// set message prefix
		Services::getConnection()->setMessagePrefix(':'.Services::getConfiguration()->connection->numeric." ");
		
		// start burst
		Services::getConnection()->sendLine("BURST ".time());
		
		// fire burst event
		Services::getEvent()->fire($this, 'burst');
		
		// send version
		Services::getConnection()->sendLine("VERSION :Evil-Co.de Services ".SERVICES_VERSION." running on ".PHP_OS);
		
		// yes! end burst my friend ;-D
		Services::getConnection()->sendLine("ENDBURST");
		
		// fire endburst event
		Services::getEvent()->fire($this, 'endburst');
		
		// wait for burst
		do {
			if (Services::getConnection()->check()) {
				try {
					// parse command
					$line = Services::getConnection()->readLine();
					$command = InspIRCdProtocolParser::handleCommand($line);
					
					// check for endburst command
					if ($command == 'ENDBURST') $this->isReady = true;
				} catch (Exception $ex) {
					Services::handleException($ex);
				}
			}
		} while(!$this->isReady and Services::getConnection()->isAlive() and $this->isAlive());
		
		// send info log message
		Services::getLog()->info('Finished bursting! Synched completely with target server');
		
		// fire protocol independent event
		Services::getEvent()->fire($this, 'connected');
		
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
	 * @see Protocol::isReady()
	 */
	public function isReady() {
		return $this->isReady;
	}
	
	/**
	 * Connection main loop
	 */
	protected function listen() {
		while($this->isAlive() and Services::getConnection()->isAlive()) {
			try {
				// handle server commands
				if (Services::getConnection()->check()) { // we'll only parse new lines if there are changes at socket
					$line = Services::getConnection()->readLine();
					InspIRCdProtocolParser::handleCommand($line);
				}
				
				// handle timers
				Services::getTimerManager()->execute();
				
				// TODO: Add a handler for special methods that MUST called in every loop. This will be very usefull for additional socket servers (XMLRPC, HTTP and other nice shit)
				// At this moment you can use timers with an interval of zero to get the same effect
			} catch (Exception $ex) {
				Services::handleException($ex);
			}
		}
	}
	
	/**
	 * @see Protocol::shutdown()
	 */
	public function shutdown() {
		// set variables
		$this->isAlive = false;
		$this->isReady = false;
		
		// send SQUIT
		Services::getConnection()->sendLine("SQUIT ".Services::getConfiguration()->connection->name." :Shutting down");
	}
	
	/**
	 * Handles calls to our send<Command>() methods
	 * @param	string	$methodName
	 * @param	array	$arguments
	 * @return void
	 * @throws RecoverableException
	 */
	public function __call($methodName, $arguments) {
		if (substr($methodName, 0, 4) == 'send') {
			// try to find correct format method
			if (method_exists($this, 'format'.substr($methodName, 4))) return Services::getConnection()->sendLine(call_user_func_array(array($this, 'format'.substr($methodName, 4)), $arguments));
		}
		
		// throw exception ...
		throw new RecoverableException("Method '".$methodName."' does not exist in class ".get_class($this));
	}
}
?>