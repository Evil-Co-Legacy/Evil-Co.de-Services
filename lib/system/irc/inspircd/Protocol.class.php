<?php
// imports
require_once(SDIR.'lib/system/irc/inspircd/ProtocolHandler.class.php');

// defines
define('PROTOCOL_VERSION', '1202');

/**
 * Manages server2server protocol
 *
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Protocol {
	/**
	 * Contains the current Protocol instance
	 *
	 * @var Protocol
	 */
	protected static $instance = null;

	/**
	 * Contains name of server
	 *
	 * @var	string
	 */
	public $name = '';

	/**
	 * Contains the numeric of server
	 *
	 * @var	string
	 */
	public $numeric = '';

	/**
	 * Contains port
	 *
	 * @var	integer
	 */
	public $port = 0;

	/**
	 * Contains the password
	 *
	 * @var	string
	 */
	public $password = '';

	/**
	 * A little variable that allows users to change hop count
	 *
	 * @var	integer
	 */
	public $hops = 0;

	/**
	 * Contains the description of server
	 *
	 * @var	string
	 */
	public $description = 'Evil-Co.de Services';

	/**
	 * Contains the current connection state
	 *
	 * @var	string
	 */
	public $connectionState = 'auth';

	/**
	 * Contains a list of all servers
	 *
	 * @var	array<string>
	 */
	public $serverList = array();

	/**
	 * Contains the cyclemessage configuration
	 *
	 * @var	array<string>
	 */
	public $cyclemessage = array();

	/**
	 * Contains the channel where services should announce logmessages
	 *
	 * @var	string
	 */
	public $servicechannel = '';

	/**
	 * Returnes an instance of Protocol
	 *
	 * @return	Protocol
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Protocol();
		}

		return self::$instance;
	}

	/**
	 * Returnes server's numeric
	 *
	 * @return	int
	 */
	public function getNumeric() {
		return $this->numeric;
	}

	/**
	 * Creates a new instance of type Protocol
	 */
	protected function __construct() {
		// get configuration
		$conf = Services::getConfiguration()->get('connection');

		// write vars
		foreach($conf as $name => $val) {
			// I'm to lazy to add all vals ... ;-)
			if (property_exists($this, $name)) $this->{$name} = $val;
		}
	}

	/**
	 * Starts network burst
	 * 
	 * @return	void
	 */
	public function initConnection() {
		// CAPAB
		Services::getConnection()->sendLine("CAPAB START ".PROTOCOL_VERSION);
		Services::getConnection()->sendLine("CAPAB CAPABILITIES :PROTOCOL=".PROTOCOL_VERSION);
		Services::getConnection()->sendLine("CAPAB END");

		// SERVER
		Services::getConnection()->sendLine("SERVER ".$this->name." ".$this->password." ".$this->hops." ".$this->numeric." :".$this->description);

		// BURST
		$this->connectionState = 'burst';
		Services::getConnection()->sendServerLine("BURST ".time());
		Services::getConnection()->sendServerLine("VERSION :Evil-Co.de Services (Protocol Version 1.0.0)");
		Services::getConnection()->sendServerLine("ENDBURST");

		// handle burst ...
		$input = "";

		do {
			// read lines
			$input = Services::getConnection()->readLine();
			$inputEx = explode(" ", $input);

			// auth commands
			switch($inputEx[0]) {
				case 'SERVER':
					$this->serverList[] = $inputEx[1];
					break;
			}
		} while(!isset($inputEx[1]) or $inputEx[1] != "BURST");

		// handle commands
		do {
			// read lines
			$input = Services::getConnection()->readLine();
			$input = substr($input, 1);
			$inputEx = explode(" ", $input);

			if (!empty($input) and count($inputEx) >= 2) {
				if (method_exists('ProtocolHandler', $inputEx[1])) call_user_func(array('ProtocolHandler', strtoupper($inputEx[1])), $input, $inputEx);
			}
		} while(!isset($inputEx[1]) or $inputEx[1] != 'ENDBURST');
		// Endburst processed!

		// Little ... er ... easteregg ... AI for services (Or automatic management for IRC networks)
		//Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :Evil-Co.de Service AI is now ready!");

		// Default runtime
		while(Services::getConnection()->isAlive()) {
			// read lines
			$input = Services::getConnection()->readLine();
			$input = substr($input, 1);
			$inputEx = explode(" ", $input);

			if (!empty($input) and count($inputEx) >= 2) {
				if (method_exists('ProtocolHandler', $inputEx[1])) call_user_func(array('ProtocolHandler', strtoupper($inputEx[1])), $input, $inputEx);
			}

			// check memcache connection
			if (Services::memcacheLoaded() and !Services::getMemcache()->checkConnection()) throw new Exception("Memcache is gone away!");
		}
	}

	/**
	 * Creates a new bot
	 *
	 * @param	string			$nick
	 * @param	string			$hostname
	 * @param	string			$ident
	 * @param	string			$ip
	 * @param	string			$modes
	 * @param	string			$gecos
	 * @return	AbstractUserType
	 */
	public function createBot($nick, $hostname, $ident, $ip, $modes, $gecos) {
		// get current unix timestamp
		$timestamp = time();

		// create user in bot manager
		$uuid = Services::getBotManager()->introduceUser($timestamp, $nick, $hostname, $hostname, $ident, $ip, $timestamp, $modes, $gecos);

		// send SVSHOLD command
		// Services::getConnection()->sendServerLine("SVSHOLD ".$nick." :Reserved for services");

		// send uid command
		Services::getConnection()->sendServerLine("UID ".$this->numeric.$uuid." ".$timestamp." ".$nick." ".$hostname." ".$hostname." ".$ident." ".$ip." ".$timestamp." ".$modes." :".$gecos);

		// return bot object
		return Services::getBotManager()->getUser($uuid);
	}

	/**
	 * Joins a user to channel
	 *
	 * @param	string	$uuid
	 * @param	string	$channel
	 * @return	void
	 */
	public function join($uuid, $channel, $channelModes = '+nt', $userMode = '') {
		Services::getConnection()->sendServerLine("FJOIN ".$channel." ".time()." ".$channelModes." :".$userMode.",".$this->numeric.$uuid);
	}

	/**
	 * Parts a user of channel
	 *
	 * @param	string	$uuid
	 * @param	string	$channel
	 * @return	void
	 */
	public function part($uuid, $channel, $message = "Leaving") {
		return Services::getConnection()->sendLine($this->formateUserLine($uuid, 'PART '.$channel.' :'.$message));
	}


	// NETWORK METHODS
	/**
	 * Formates a line for server syntax
	 *
	 * @param	string	$message
	 * @return	string
	 */
	public function formateServerLine($message) {
		return ":".$this->numeric." ".$message;
	}

	/**
	 * Formates a line for user syntax
	 * @param	string	$uuid
	 * @param	string	$message
	 */
	public function formateUserLine($uuid, $message) {
		return ":".$this->numeric.$uuid." ".$message;
	}

	/**
	 * Sends a log line to service channel
	 * @param	string	$message
	 */
	public function sendLogLine($message) {
		Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] ".$message);
	}

	/**
	 * Sends a global message to all servers
	 * @param	string	$message
	 * @param	string	$source
	 */
	public function sendGlobalMessage($message, $source = '') {
		foreach($this->serverList as $server) {
			Services::getConnection()->sendLine(":".$this->numeric.$source." NOTICE $".$server." :".$message);
		}
	}

	/**
	 * Shuts down the connection
	 */
	public function shutdownConnection($error = '') {
		switch($this->connectionState) {
			case 'auth':
				Services::getConnection()->sendLine("ERROR :Connection aborted!");
				break;
			case 'authed':
			case 'burst':
				if (!empty($error)) {
					$errorArray = explode("\n", str_replace("\r", "", $error));
					foreach($errorArray as $error) {
						Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name.":Fatal Error] ".$error);
					}
				}
				Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] Shutting down ...");
				Services::getConnection()->sendServerLine("SQUIT ".$this->name." :Shutting down!");
				break;
		}
		$this->connectionState = 'disconnected';
	}

	/**
	 * Sends a var_dump to service channel
	 * @param	mixed	$var
	 */
	public function var_dump($var) {
		// get print_r
		ob_start();
		var_dump($var);
		$content = ob_get_contents();
		ob_end_clean();

		// split
		$content = explode("\n", $content);

		// send log
		foreach($content as $text) {
			Services::getConnection()->getProtocol()->sendLogLine($text);
		}
	}

	/**
	 * Returnes the service channel
	 */
	public function getServiceChannel() {
		return $this->servicechannel;
	}

	// COMMAND METHODS
	/**
	 * Sends a PRIVMSG from source to target
	 * @param	string	$source
	 * @param	string	$target
	 * @param	string	$message
	 */
	public function sendPrivmsg($source, $target, $message) {
		Services::getConnection()->sendLine($this->formateUserLine($source, "PRIVMSG ".$target." :".$message));
	}

	/**
	 * Sends a NOTICE from $source to $target
	 * @param	string	$source
	 * @param	string	$target
	 * @param	string	$message
	 */
	public function sendNotice($source, $target, $message) {
		Services::getConnection()->sendLine($this->formateUserLine($source, "NOTICE ".$target." :".$message));
	}

	/**
	 * Sends a MODE from $source to $target with $modes
	 * @param	string	$source
	 * @param	string	$target
	 * @param	string	$modes
	 */
	public function sendMode($source, $target, $modes) {
		Services::getConnection()->sendLine($this->formateUserLine($source, "MODE ".$target." ".$modes));
	}

	/**
	 * Stores metadata on server
	 * @param	string	$target
	 * @param	string	$key
	 * @param	mixed	$value
	 */
	public function sendMetadata($target, $key, $value) {
		Services::getConnection()->sendServerLine("METADATA ".$target." ".$key." :".(is_string($value) ? $value : serialize($value)));
	}

	/**
	 * Sends a KICK from $source in channel $target for user $user with reason $reason
	 * @param	string	$source
	 * @param	string	$target
	 * @param	string	$user
	 * @param	string	$reason
	 */
	public function sendKick($source, $target, $user, $reason) {
		Services::getConnection()->sendLine($this->formateUserLine($source, "KICK ".$target." ".$user." :".$reason));
	}
}
?>