<?php
// defines
define('PROTOCOL_VERSION', '1202');

/**
 * Manages server2server protocol
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Protocol {
	
	/**
	 * Contains the current Protocol instance
	 */
	protected static $instance = null;
	
	/**
	 * Contains name of server
	 * @var	string
	 */
	protected $name = '';
	
	/**
	 * Contains the numeric of server
	 * @var	string
	 */
	protected $numeric = '';
	
	/**
	 * Contains port
	 * @var	integer
	 */
	protected $port = 0;
	
	/**
	 * Contains the password
	 * @var	string
	 */
	protected $password = '';
	
	/**
	 * A little variable that allows users to change hop count
	 * @var	integer
	 */
	protected $hops = 0;
	
	/**
	 * Contains the description of server
	 * @var	string
	 */
	protected $description = 'Evil-Co.de Services';
	
	/**
	 * Contains the current connection state
	 * @var	string
	 */
	protected $connectionState = 'auth';
	
	/**
	 * Contains a list of all servers
	 * @var	array<string>
	 */
	protected $serverList = array();
	
	/**
	 * Contains the cyclemessage configuration
	 * @var	array<string>
	 */
	protected $cyclemessage = array();
	
	/**
	 * Contains the channel where services should announce logmessages
	 * @var	string
	 */
	protected $servicechannel = '';
	
	/**
	 * Returnes an instance of Protocol
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Protocol();
		}
		
		return self::$instance;
	}
	
	/**
	 * Creates a new instance of type Protocol
	 */
	public function __construct() {
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
				switch($inputEx[1]) {
					case 'UID':
						// get mode string
						$modes = '';
						$activeIndex = 10;
						
						while($inputEx[$activeIndex]{0} != ':') {
							if (!empty($modes)) $modes .= " ";
							$modes .= $inputEx[$activeIndex];
							$activeIndex++;
						}
						
						// add user to manager
						Services::getUserManager()->introduceUser($inputEx[3], $inputEx[4], $inputEx[5], $inputEx[6], $inputEx[7], $inputEx[8], $inputEx[9], $modes, substr($input, (stripos(':', $input) + 1)), $inputEx[2]);
						
						// send debug message
						if (defined('DEBUG')) print("Added user ".$inputEx[2]."\n");
						break;
					case 'FJOIN':
						// get mode string
						$modes = '';
						$activeIndex = 4;
						
						while($inputEx[$activeIndex]{0} != ':') {
							if (!empty($modes)) $modes .= " ";
							$modes .= $inputEx[$activeIndex];
							$activeIndex++;
						}
						
						// generate userlist
						$userListString = substr($input, (stripos($input, ':') + 1));
						$userListString = explode(' ', $userListString);
						$userList = array();
						
						foreach($userListString as $user) {
							$user = explode(',', $user);
							if (count($user) == 2) {
								$userList[] = array('mode' => $user[0], 'user' => Services::getUserManager()->getUser($user[1]));
							}
						}
						
						// add channel
						Services::getChannelManager()->addChannel($inputEx[2], $inputEx[3], $modes, $userList);
						
						// send debug message
						if (defined('DEBUG')) print("Added channel ".$inputEx[2]."\n");
						break;
					case 'SERVER':
						$this->serverList[] = $inputEx[2];
						break;
					case 'ENDBURST':
						// TODO: This does not work ... it's a bug ...
						$this->connectionState = 'authed';
						
						if (defined('DEBUG')) print("ENDBURST\n");
						
						// send log message
						Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] Burst finished");
						
						// memcache
						if (extension_loaded('memcache')) {
							$this->sendLogLine("Memcache extension is available! Trying to find configuration for memcache ...");
							Services::loadMemcache();
						}
						
						// init modules
						Services::getModuleManager()->init();
						break;
				}
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
				switch($inputEx[1]) {
					case 'PING':
						Services::getEvent()->fire($this, 'ping', array('source' => $inputEx[0]));
						Services::getConnection()->sendServerLine("PONG ".$inputEx[3]." ".$inputEx[2]);
						if (defined('DEBUG')) print("Ping -> Pong\n");
						break;
					case 'FJOIN':
						// get mode string
						if (($chan = Services::getChannelManager()->getChannel($inputEx[2])) === null) {
							$modes = '';
							$activeIndex = 4;
							
							while($inputEx[$activeIndex]{0} != ':' and stripos($inputEx[$activeIndex], ',') === false) {
								if (!empty($modes)) $modes .= " ";
								$modes .= $inputEx[$activeIndex];
								$activeIndex++;
							}
							
							// generate userlist
							$userListString = substr($input, (stripos($input, ':') + 1));
							$userListString = explode(' ', $userListString);
							$userList = array();
							
							foreach($userListString as $user) {
								$user = explode(',', $user);
								if (count($user) == 2) {
									$userList[] = array('mode' => $user[0], 'user' => Services::getUserManager()->getUser($user[1]));
								}
							}
							
							// call event
							Services::getEvent()->fire($this, 'channelCreated', array('channel' => $inputEx[2], 'userList' => $userList));
							
							// add channel
							Services::getChannelManager()->addChannel($inputEx[2], $inputEx[3], $modes, $userList);
							
							// send debug message
							if (defined('DEBUG')) print("Added channel ".$inputEx[2]."\n");
						} else {
							// generate userlist
							$userListString = substr($input, (stripos($input, ':') + 1));
							$userListString = explode(' ', $userListString);
							$userList = array();
							
							foreach($userListString as $user) {
								$user = explode(',', $user);
								if (count($user) == 2) {
									$userList[] = array('mode' => $user[0], 'user' => Services::getUserManager()->getUser($user[1]));
								}
							}
							
							// call event
							Services::getEvent()->fire($this, 'channelJoined', array('channel' => $inputEx[2], 'userList' => $userList));
							
							// join users to channel
							$chan->join($userList);
						}
						break;
					case 'SERVER':
						Services::getEvent()->fire($this, 'serverCreated', array('name' => $inputEx[2]));
						$this->serverList[] = $inputEx[2];
						break;
					case 'PART':
						Services::getEvent()->fire($this, 'userParted', array('channel' => $inputEx[2], 'user' => Services::getUserManager()->getUser($inputEx[0])));
						Services::getChannelManager()->getChannel($inputEx[2])->part($inputEx[0]);
						break;
					case 'QUIT':
						Services::getEvent()->fire($this, 'userQuit', array('user' => Services::getUserManager()->getUser($inputEx[0])));
						Services::getUserManager()->removeUser($inputEx[0]);
						break;
					case 'NOTICE':
					case 'PRIVMSG':
						if ($inputEx[2]{0} != '$') {
							// get source
							$source = Services::getUserManager()->getUser($inputEx[0]);

							if ($inputEx[2]{0} == '#') {
								// send debug message
								if (defined('DEBUG')) $this->sendLogLine($source->getUuid()." (".$source->getNick().") sent a message to ".$inputEx[2]);
								
								$chan = Services::getChannelManager()->getChannel($inputEx[2]);
								$userList = $chan->getUserList();
								
								foreach($userList as $user) {
									if ($user['user']->isBot !== null) {
										Services::getModuleManager()->handleLine($source, $inputEx[2], substr($input, (4 + strlen($inputEx[0]) + strlen($inputEx[1]) + strlen($inputEx[2]))));
									}
								}
							} elseif ($source) {
								// send debug message
								if (defined('DEBUG')) $this->sendLogLine($source->getUuid()." (".$source->getNick().") sent a message to ".$inputEx[2]);
								
								// try to find bot
								if (($bot = Services::getBotManager()->getUser($inputEx[2])) !== null) {
									// resolved uuid ... send debug message
									if (defined('DEBUG')) $this->sendLogLine("Resolved ".$inputEx[2]." to ".$bot->getNick());
									
									// notify module manager
									Services::getModuleManager()->handleLine($source, $inputEx[2], substr($input, (4 + strlen($inputEx[0]) + strlen($inputEx[1]) + strlen($inputEx[2]))));
								} else {
									// cannot find user ... send debug message
									if (defined('DEBUG')) $this->sendLogLine("Cannot resolve '".$inputEx[2]."'! Type of return value: ".gettype($bot));
								}
							} else {
								$this->sendLogLine("Received invalid UUID '".$inputEx[0]."'! Maybe choosen wrong IRCd?");
							}
						}
						break;
				}
			}
			
			// check memcache connection
			if (Services::memcacheLoaded() and !Services::getMemcache()->checkConnection()) throw new Exception("Memcache is gone away!");
		}
	}
	
	/**
	 * Creates a new bot
	 * @param	string	$nick
	 * @param	string	$hostname
	 * @param	string	$ident
	 * @param	string	$ip
	 * @param	string	$modes
	 * @param	string	$gecos
	 */
	public function createBot($nick, $hostname, $ident, $ip, $modes, $gecos) {
		// get current unix timestamp
		$timestamp = time();
		
		// create user in bot manager
		$uuid = Services::getBotManager()->introduceUser($timestamp, $nick, $hostname, $hostname, $ident, $ip, $timestamp, $modes, $gecos);
		
		// send uid command
		Services::getConnection()->sendServerLine("UID ".$this->numeric.$uuid." ".$timestamp." ".$nick." ".$hostname." ".$hostname." ".$ident." ".$ip." ".$timestamp." ".$modes." :".$gecos);
		
		// return bot object
		return Services::getBotManager()->getUser($uuid);
	}
	
	// NETWORK METHODS
	/**
	 * Formates a line for server syntax
	 * @param	string	$message
	 */
	public function formateServerLine($message) {
		return ":".$this->numeric." ".$message;
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
				if (!empty($error)) Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name.":Fatal Error] ".$error);
				Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] Shutting down ...");
				Services::getConnection()->sendServerLine("SQUIT ".$this->name." :Shutting down!");
				break;
		}
		$this->connectionState = 'disconnected';
	}
	
	/**
	 * Returnes the service channel
	 */
	public function getServiceChannel() {
		return $this->servicechannel;
	}
}
?>