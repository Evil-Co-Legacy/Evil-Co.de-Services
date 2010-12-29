<?php
// defines
define('IRCD', 'inspircd');
define('SERVICES_VERSION', '2.0.0-eatsChildren');
// Uncomment the following to enable debugging
define('DEBUG', true);

// imports
require_once(SDIR.'lib/core.functions.php');
require_once(SDIR.'lib/system/user/BotManager.class.php');
require_once(SDIR.'lib/system/irc/channel/ChannelManager.class.php');
require_once(SDIR.'lib/system/configuration/Configuration.class.php');
require_once(SDIR.'lib/system/event/EventHandler.class.php');
require_once(SDIR.'lib/system/irc/IRCConnection.class.php');
require_once(SDIR.'lib/system/language/LanguageManager.class.php');
require_once(SDIR.'lib/system/module/ModuleManager.class.php');
require_once(SDIR.'lib/system/irc/Protocol.class.php');
require_once(SDIR.'lib/system/user/UserManager.class.php');

/**
 * Manages all needed core instances
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Services {
	
	/**
	 * Contains the BotManager object
	 * @var	BotManager
	 */
	protected static $botManagerObj = null;

	/**
	 * Contains the ChannelManager object
	 * @var	ChannelManager
	 */
	protected static $channelManagerObj = null;
	
	/**
	 * Contains the Configuration object
	 * @var Configuration
	 */
	protected static $configObj = null;

	/**
	 * Contains the database connection
	 * @var	Database
	 */
	protected static $dbObj = null;
	
	/**
	 * Contains the EventHandler object
	 * @var	EventHandler
	 */
	protected static $eventObj = null;

	/**
	 * Contains the IRCConnection object
	 * @var	IRCConnection
	 */
	protected static $ircObj = null;
	
	/**
	 * Contains the LanguageManager object
	 * @var	LanguageManager
	 */
	protected static $languageObj = null;

	/**
	 * Contains the ModuleManager object
	 * @var	ModuleManager
	 */
	protected static $moduleManagerObj = null;
	
	/**
	 * Contains the Protocol object
	 * @var Protocol
	 */
	protected static $protocolObj = null;
	
	/**
	 * Contains the UserManager object
	 * @var UserManager
	 */
	protected static $userManagerObj = null;

	/**
	 * Creates a new instance of Services
	 */
	public function __construct() {
		$this->initConfiguration();
		$this->initEvents();
		$this->initDB();
		$this->initLanguage();
		$this->initUserManager();
		$this->initBotManager();
		$this->initChannelManager();
		$this->initModules();
		$this->initConnection();
		$this->initProtocol();
	}

	/**
	 * Shuts down our services
	 */
	public static function destruct() {
		// call connection shutdown method
		if (self::getConnection() !== null) self::getConnection()->shutdown();

		// remove pidfile (if any)
		if (file_exists(SDIR.'services.pid')) @unlink(SDIR.'services.pid');
	}
	
	/**
	 * Creates a new BotManager instance
	 */
	protected function initBotManager() {
		self::$botManagerObj = new BotManager();
	}
	
	/**
	 * Creates an new ChannelManager instance
	 */
	protected function initChannelManager() {
		self::$channelManagerObj = new ChannelManager();
	}

	/**
	 * Creates a new configuration object
	 */
	protected function initConfiguration() {
		self::$configObj = new Configuration();
	}
	
	/**
	 * Creates a new IRCConnection instance
	 */
	protected function initConnection() {
		self::$ircObj = new IRCConnection();
	}
	
	/**
	 * Creates a new database connection
	 */
	protected function initDB() {
		// get configuration
		$db = self::getConfiguration()->get('database');

		// validate
		if (!isset($db['driver'], $db['hostname'], $db['username'], $db['password'], $db['dbname'])) throw new Exception("Invalid Database configuration!");

		// try to find database driver
		if (!file_exists(SDIR.'lib/system/database/'.$db['driver'].'Database.class.php')) throw new Exception("Invalid database driver: ".$db['driver']);

		// get drivers classname
		$className = $db['driver'].'Database';

		// include driver
		require_once(SDIR.'lib/system/database/'.$className.'.class.php');

		// create new instance
		self::$dbObj = new $className($db['dbname'], $db['username'], $db['password'], $db['hostname'], 'UTF8');
	}

	/**
	 * Creates a new EventHandler object
	 */
	protected function initEvents() {
		self::$eventObj = new EventHandler();
	}

	/**
	 * Creates a new LanguageManager instance
	 */
	protected function initLanguage() {
		self::$languageObj = new LanguageManager();
	}
	
	/**
	 * Creates a new ModuleManager instance
	 */
	protected function initModules() {
		self::$moduleManagerObj = new ModuleManager();
	}
	
	/**
	 * Creates a new Protocol instance
	 */
	protected function initProtocol() {
		self::$protocolObj = new Protocol();
	}

	/**
	 * Creates a new UserManager instance
	 */
	protected function initUserManager() {
		self::$userManagerObj = new UserManager();
	}
	
	/**
	 * Returnes the current bot manager object
	 */
	public static function getBotManager() {
		return self::$botManagerObj;
	}
	
	/**
	 * Returnes the current channel manager object
	 */
	public static function getChannelManager() {
		return self::$channelManagerObj;
	}

	/**
	 * Returnes the current configuration object
	 */
	public static function getConfiguration() {
		return self::$configObj;
	}
	
	/**
	 * Returnes the current irc connection
	 */
	public static function getConnection() {
		return self::$ircObj;
	}

	/**
	 * Returnes the current database connection
	 */
	public static function getDB() {
		return self::$dbObj;
	}
	
	/**
	 * Returnes the current EventHandler object
	 */
	public static function getEvent() {
		return self::$eventObj;
	}

	/**
	 * Returnes the current language manager
	 */
	public static function getLanguage() {
		return self::$languageObj;
	}
	
	/**
	 * Returnes the current ModuleManager object
	 */
	public static function getModuleManager() {
		return self::$moduleManagerObj;
	}
	
	/**
	 * Returnes the current Protocol object
	 * @return Protocol
	 */
	public static function getProtocol() {
		return self::$protocolObj;
	}

	/**
	 * Returnes the current user manager object
	 */
	public static function getUserManager() {
		return self::$userManagerObj;
	}

	/**
	 * Handles errors
	 * @param	integer	$errNo
	 * @param	string	$errMessage
	 * @param	string	$errFile
	 * @param	integer	$errLine
	 */
	public static function handleError($errorNo, $errMessage, $errFile, $errLine) {
		if (error_reporting() != 0) {
			$type = 'error';
			switch ($errorNo) {
				case 2: $type = 'warning';
					break;
				case 8: $type = 'notice';
					break;
			}
			
			if($type == 'error')
				throw new SystemException("Error in file ".$errFile." on line ".$errLine." (".$errNo."): ".$errMessage);
			elseif ($type == 'warning' or $type == 'notice')
				throw new RecoverableException("Error in file ".$errFile." on line ".$errLine." (".$errNo."): ".$errMessage);
		}
	}
	
	/**
	 * Handles uncought exceptions
	 * @param	Exception	$ex
	 */
	public static function handleException(Exception $ex) {
		// Call SystemException::sendDebugLog()
		if ($ex instanceof SystemException) $ex->sendDebugLog();
		
		// Call Protocol::handleException()
		if ($ex instanceof ProtocolException) self::$protocolObj->handleException($ex);

		// Call Connection::handleException()
		if ($ex instanceof ConnectionException) self::$ircObj->handleException($ex);
		 
		// Call shutdown methods if the given exception is recoverable (UserExceptions and RecoverableExceptions)
		if (!($ex instanceof RecoverableException) and !($ex instanceof UserException)) {
			// call connection shutdown method
			if (self::getConnection() !== null and self::getConnection()->getProtocol() !== null) self::getConnection()->getProtocol()->shutdownConnection($ex->getMessage());
		}
	}
}
?>