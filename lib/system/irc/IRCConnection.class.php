<?php
// imports
require_once(SDIR.'lib/system/irc/Mode.class.php');
require_once(SDIR.'lib/system/irc/ModeList.class.php');
require_once(SDIR.'lib/system/irc/AbstractModeList.class.php');
require_once(SDIR.'lib/system/irc/ChannelModeList.class.php');
require_once(SDIR.'lib/system/irc/UserModeList.class.php');
require_once(SDIR.'lib/system/irc/'.IRCD.'/Protocol.class.php');

/**
 * Represents the connection to IRC
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class IRCConnection {
	
	/**
	 * Contains socket to server
	 * @var	resource
	 */
	protected $socket = null;
	
	/**
	 * Contains the protocol object
	 * @var	Protocol
	 */
	protected $protocol = null;
	
	/**
	 * Creates a new instance of type IRCConnection
	 */
	public function start() {
		// init protocol
		$this->protocol = Protocol::getInstance();
		
		// start socket
		$this->startSocket();
		
		// start server2server protocol
		$this->protocol->initConnection();
	}
	
	/**
	 * Starts the socket connection
	 */
	public function startSocket() {
		// get connection configuration
		$conf = Services::getConfiguration()->get('connection');
		
		// validate configuration
		if (!isset($conf['hostname'], $conf['port'])) throw new Exception("Invalid connection configuration!");
		
		// create socket
		$this->socket = socket_create(AF_INET, SOCK_STREAM, 0);
		
		// try to connect
		if (!@socket_connect($this->socket, $conf['hostname'], intval($conf['port']))) {
			throw new Exception("Cannot connect to server: ".socket_strerror());
		}
	}
	
	/**
	 * Sends a message to server
	 * @param	string	$message
	 */
	public function send($message) {
		if (defined('DEBUG')) print("<-- ".$message);
		return socket_write($this->socket, $message);
	}
	
	/**
	 * Sends a line to server
	 * @param	string	$message
	 */
	public function sendLine($message) {
		$this->send($message."\n");
	}
	
	/**
	 * Reads a line from socket
	 */
	public function readLine() {
		// read line
		$input = str_replace("\n", "", socket_read($this->socket, 512, PHP_NORMAL_READ));
		
		// send debug lines
		if (defined('DEBUG') and $input != "") print("--> ".$input."\n");
		
		return $input;
	}
	
	/**
	 * Returnes the protocol instance
	 */
	public function getProtocol() {
		return $this->protocol;
	}
	
	/**
	 * Sends a server line to server
	 * @param	string	$message
	 */
	public function sendServerLine($message) {
		$this->sendLine($this->protocol->formateServerLine($message));
	}
	
	/**
	 * Returnes true if the connection is alive
	 */
	public function isAlive() {
		if ($this->socket !== false) return true;
		return false;
	}
	
	/**
	 * Shuts down the connection
	 */
	public function shutdown() {
		Services::getEvent()->fire($this, 'shutdown');
		
		// Shutdown server connection if existant
		if ($this->socket !== false) {
			$this->protocol->shutdownConnection();
		}
	}
}
?>