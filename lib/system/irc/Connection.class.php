<?php
/**
 * Manages the connection to server
 *
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Connection {
	
	/**
	 * Contains a timeout for socket_select calls
	 *
	 * @var integer
	 */
	const SOCKET_CHECK_TIMEOUT = 0;
	
	/**
	 * Contains the maximum of bytes that should read from socket
	 * Note: This is defined by IRC RCF
	 *
	 * @var integer
	 */
	const SOCKET_READ_MAX = 512;
	
	/**
	 * Contains the socket that is connected to server
	 *
	 * @var Resource
	 */
	protected $socket = null;
	
	/**
	 * Contains configuration variables
	 *
	 * @var	array<string>
	 */
	private $configuration = array();
	
	/**
	 * Contains the current connection state
	 *
	 * @var string
	 */
	protected $connectionState = 'none';
	
	/**
	 * Creates a new instance of construct
	 */
	public function __construct() {
		// read connection details
		$this->configuration = Services::getConfiguration()->get('connection');
		
		// call init methods
		$this->initSocket();
		$this->connect();
	}
	
	/**
	 * Returnes a count of modified sockets
	 * Note: This returnes 0 if no sockets modified
	 *
	 * @return integer
	 */
	public function check() {
		$read = array(&$this->socket);
		$write = array(&$this->socket);
		$except = array(&$this->socket);
		if (($state = socket_select($read, $write, $except, self::SOCKET_CHECK_TIMEOUT, self::SOCKET_CHECK_TIMEOUT)) === false)
			throw new ConnectionException("An error occoured: ".socket_strerror(socket_last_error($this->socket)), socket_last_error($this->socket));
		
		return $state;
	}
	
	/**
	 * Starts the connection
	 *
	 * @return void
	 */
	protected function connect() {
		if(!socket_connect($this->socket, $this->configuration->hostname, intval($this->configuration->port)))
			throw new ConnectionException("Cannot connect to ".$this->configuration->hostname.":".$this->configuration->port);
		else
			$this->connectionState = 'connected';
		
		// log
		Services::getLog()->info('Connected to '.$this->configuration->hostname.':'.intval($this->configuration->port));
			
		// fire event
		Services::getEvent()->fire($this, 'connected');
	}
	
	/**
	 * Returnes the current connection state
	 *
	 * @return string
	 */
	public function getConnectionState() {
		return $this->connectionState;
	}
	
	/**
	 * Alias for Services::getProtocol()
	 * @see Services::getProtocol()
	 * @deprecated
	 */
	public function getProtocol() {
		// log this shit!
		Services::getLog()->debug("Use of deprecated method Connection::getProtocol():" . PHP_EOL . implode(PHP_EOL, debug_backtrace()));
		
		// call alias
		return Services::getProtocol();
	}
	
	/**
	 * Creates a new socket
	 *
	 * @return void
	 */
	protected function initSocket() {
		// check configuration
		if (!isset($this->configuration->ipversion) or !isset($this->configuration->hostname) or !isset($this->configuration->port)) throw new ConnectionException("Invalid configuration. Please recheck your configuration ... NOOB!");
		
		// create socket
		$this->socket = socket_create(($this->configuration->ipversion == '4' ? AF_INET : AF_INET6), SOCK_STREAM, getprotobyname('tcp'));
		
		// send debug log
		Services::getLog()->debug("Created IPv".($this->configuration->ipversion == '4' ? '4' : '6')." socket");
		
		// check created socket
		if ($this->socket === false)
			throw new ConnectionException("Cannot create socket");
		else
			$this->connectionState = 'socketCreated';
	}
	
	/**
	 * Returnes true if the connection is still alive
	 */
	public function isAlive() {
		// socket errors
		if (socket_last_error($this->socket) != 0) return false;
		
		return true;
	}
	
	/**
	 * Reads a line from server
	 *
	 * @return string
	 */
	public function readLine() {
		// read line and check for errors
		if (($line = socket_read($this->socket, self::SOCKET_READ_MAX, PHP_NORMAL_READ)) === false)
			throw new ConnectionException("An error occoured while reading from socket: ".socket_strerror(socket_last_error($this->socket)), socket_last_error($this->socket));
		
		// unify newlines
		$line = StringUtil::unifyNewlines($line);
			
		// remove newlines
		$line = str_replace("\n", "", $line);
			
		// debug information
		if (strlen($line)) Services::getLog()->debug("[-->] ".$line);
			
		// return line
		return $line;
	}
	
	/**
	 * Writes the given message with given length to string
	 * Note: The second parameter is optional. It will set automaticly if it isn't given
	 *
	 * @param	string	$message
	 * @param	integer	$length
	 * @return 	integer
	 */
	protected function __send($message, $length = null) {
		// unify newlines
		$message = StringUtil::unifyNewlines($message);
		
		// send message
		if (($bytes = socket_write($this->socket, $message, ($length !== null ? $length : strlen($message)))) === false)
			throw new ConnectionException("An error occoured while write to socket: ".socket_strerror(socket_last_error($this->socket)), socket_last_error($this->socket));
		
		// send log message
		Services::getLog()->debug("[<--] ".preg_replace("%(\r\n)|(\r)%", "", $message));
			
		return $bytes;
	}
	
	/**
	 * Sends a line to server
	 *
	 * @param	string	$message
	 * @param	integer	$length
	 * @return 	integer
	 */
	public function sendLine($message, $length = null) {
		return $this->__send($message.(stripos($message, "\n") === false ? "\n" : ""));
	}
	
	/**
	 * Shuts down the connection
	 */
	public function shutdown() {
		socket_shutdown($this->socket);
	}
}
?>