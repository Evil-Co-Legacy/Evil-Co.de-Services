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
		} while(!isset($inputEx[1]) or $inputEx[1] != "BURST");
		
		// handle commands
		do {
			// read lines
			$input = Services::getConnection()->readLine();
			$input = substr($input, 1);
			$inputEx = explode(" ", $input);
			
			if (!empty($input) and count($inputEx) >= 2) {
				$inputEx = explode(" ", $input);
				
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
						
						// send global message
						if (isset($this->cyclemessage['startup']) and !empty($this->cyclemessage['startup'])) {
							foreach($this->serverList as $server) {
								Services::getConnection()->sendServerLine("NOTICE $".$server." :".$this->cyclemessage['startup']);
							}
						}
						break;
					case 'PING':
						Services::getConnection()->sendServerLine("PONG ".$inputEx[3]." ".$inputEx[2]);
						if (defined('DEBUG')) print("Ping -> Pong\n");
						break;
				}
			}
		} while(Services::getConnection()->isAlive());
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
	 * Shuts down the connection
	 */
	public function shutdownConnection($error = '') {
		switch($this->connectionState) {
			case 'auth':
				Services::getConnection()->sendLine("ERROR :Connection aborted!");
				break;
			case 'authed':
			case 'burst':
				if (!empty($error)) Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] A wild error occoures: ".$error);
				Services::getConnection()->sendServerLine("NOTICE ".$this->servicechannel." :[".$this->name."] Shutting down ...");
				Services::getConnection()->sendServerLine("SQUIT ".$this->name." :Shutting down!");
				break;
		}
		$this->connectionState = 'disconnected';
	}
}
?>