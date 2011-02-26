<?php
// defines
define('IRCD', 'inspircd');
define('SERVICES_VERSION', '2.0.0-eatsChildren');
// Uncomment the following to enable debugging
define('DEBUG', true);

// imports
require_once(SDIR.'lib/core.functions.php');
require_once(SDIR.'lib/system/user/BotManager.class.php');
require_once(SDIR.'lib/system/irc/ChannelManager.class.php');
require_once(SDIR.'lib/system/event/EventHandler.class.php');
require_once(SDIR.'lib/system/irc/Connection.class.php');
require_once(SDIR.'lib/system/language/LanguageManager.class.php');
require_once(SDIR.'lib/system/module/ModuleManager.class.php');
require_once(SDIR.'lib/system/irc/ProtocolManager.class.php');
require_once(SDIR.'lib/system/user/UserManager.class.php');

// Zend imports
require_once('Zend/Config/Xml.php');
require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Stream.php');

/**
 * Manages all needed core instances
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Services {
	
	/**
	 * Contains the BotManager object
	 *
	 * @var	BotManager
	 */
	protected static $botManagerObj = null;

	/**
	 * Contains the ChannelManager object
	 *
	 * @var	ChannelManager
	 */
	protected static $channelManagerObj = null;
	
	/**
	 * Contains the Configuration object
	 *
	 * @var Configuration
	 */
	protected static $configObj = null;

	/**
	 * Contains the database connection
	 *
	 * @var	Database
	 */
	protected static $dbObj = null;
	
	/**
	 * Contains the EventHandler object
	 *
	 * @var	EventHandler
	 */
	protected static $eventObj = null;

	/**
	 * Contains the IRCConnection object
	 *
	 * @var	Connection
	 */
	protected static $ircObj = null;
	
	/**
	 * Contains the LanguageManager object
	 *
	 * @var	LanguageManager
	 */
	protected static $languageObj = null;
	
	/**
	 * Contains the logger object
	 * @var Zend_Log
	 */
	protected static $loggerObj = null;
	
	/**
	 * Contains the log writer
	 * @var Zend_Log_Writer_Stream
	 */
	protected static $logWriterObj = null;
	
	/**
	 * Contains a file stream
	 * @var resource
	 */
	protected static $logWriterStream = null;

	/**
	 * Contains the ModuleManager object
	 *
	 * @var	ModuleManager
	 */
	protected static $moduleManagerObj = null;
	
	/**
	 * Contains the Protocol object
	 *
	 * @var	Protocol
	 */
	protected static $protocolObj = null;
	
	/**
	 * Contains the UserManager object
	 *
	 * @var	UserManager
	 */
	protected static $userManagerObj = null;

	/**
	 * Creates a new instance of Services
	 */
	public function __construct() {
		$this->initLog();
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
		self::$protocolObj->initConnection();
	}

	/**
	 * Shuts down our services
	 *
	 * @return	void
	 */
	public static function destruct() {
		// call protocol shutdown method
		if (self::getProtocol() !== null && self::getProtocol()->isAlive()) self::getProtocol()->shutdown();
		
		// call connection shutdown method
		if (self::getConnection() !== null) self::getConnection()->shutdown();
		
		// remove pidfile (if any)
		if (file_exists(SDIR.'services.pid')) @unlink(SDIR.'services.pid');
	}
	
	/**
	 * Creates a new BotManager instance
	 *
	 * @return	void
	 */
	protected function initBotManager() {
		self::$botManagerObj = new BotManager();
	}
	
	/**
	 * Creates an new ChannelManager instance
	 *
	 * @return	void
	 */
	protected function initChannelManager() {
		self::$channelManagerObj = new ChannelManager();
	}

	/**
	 * Creates a new configuration object
	 *
	 * @return	void
	 */
	protected function initConfiguration() {
		self::$loggerObj->info = "Reading configuration file '".SDIR.'config/config.xml'."'";
		self::$configObj = new Zend_Config_Xml(SDIR.'config/config.xml');
	}
	
	/**
	 * Creates a new IRCConnection instance
	 *
	 * @return	void
	 */
	protected function initConnection() {
		self::$ircObj = new Connection();
	}
	
	/**
	 * Creates a new database connection
	 *
	 * @return	void
	 */
	protected function initDB() {
		// validate
		if (!isset(self::getConfiguration()->database->driver) or !isset(self::getConfiguration()->database->hostname) or !isset(self::getConfiguration()->database->username) or !isset(self::getConfiguration()->database->password) or !isset(self::getConfiguration()->database->dbname)) throw new SystemException("Invalid Database configuration!");
		
		// try to find database driver
		if (!file_exists(SDIR.'lib/system/database/'.self::getConfiguration()->database->driver.'Database.class.php')) throw new SystemException("Invalid database driver: ".$db['driver']);

		// get drivers classname
		$className = self::getConfiguration()->database->driver.'Database';

		// include driver
		require_once(SDIR.'lib/system/database/'.$className.'.class.php');

		// create new instance
		self::$dbObj = new $className(self::getConfiguration()->database->hostname, self::getConfiguration()->database->username, self::getConfiguration()->database->password, self::getConfiguration()->database->dbname, 'UTF-8');
	}

	/**
	 * Creates a new EventHandler object
	 *
	 * @return	void
	 */
	protected function initEvents() {
		self::$eventObj = new EventHandler();
	}

	/**
	 * Creates a new LanguageManager instance
	 *
	 * @return	void
	 */
	protected function initLanguage() {
		self::$languageObj = new LanguageManager();
	}
	
	/**
	 * Creates a new Zend_Log_Writer_Stream and Zend_Log instance
	 */
	protected function initLog() {
		// open file
		self::$logWriterStream = fopen(SDIR.'logs/services-'.gmdate('M-d-Y').'.log', 'a', false);
		 
		// create log instances
		self::$logWriterObj = new Zend_Log_Writer_Stream(self::$logWriterStream);
		self::$loggerObj = new Zend_Log(self::$logWriterObj);
		
		// add log entry
		self::$loggerObj->info = "Evil-Co.de Services ".SERVICES_VERSION." running on PHP ".phpversion();
	}
	
	/**
	 * Creates a new ModuleManager instance
	 *
	 * @return	void
	 */
	protected function initModules() {
		self::$moduleManagerObj = new ModuleManager();
	}
	
	/**
	 * Creates a new Protocol instance
	 *
	 * @return	void
	 */
	protected function initProtocol() {
		self::$protocolObj = new ProtocolManager();
	}

	/**
	 * Creates a new UserManager instance
	 *
	 * @return	void
	 */
	protected function initUserManager() {
		self::$userManagerObj = new UserManager();
	}
	
	/**
	 * Returnes the current bot manager object
	 * 
	 * @return	BotManager
	 */
	public static function getBotManager() {
		return self::$botManagerObj;
	}
	
	/**
	 * Returnes the current channel manager object
	 *
	 * @return	ChannelManager
	 */
	public static function getChannelManager() {
		return self::$channelManagerObj;
	}

	/**
	 * Returnes the current configuration object
	 *
	 * @return	Configuration
	 */
	public static function getConfiguration() {
		return self::$configObj;
	}
	
	/**
	 * Returnes the current irc connection
	 *
	 * @return	Connection
	 */
	public static function getConnection() {
		return self::$ircObj;
	}

	/**
	 * Returnes the current database connection
	 *
	 * @return	DataBase
	 */
	public static function getDB() {
		return self::$dbObj;
	}
	
	/**
	 * Returnes the current EventHandler object
	 *
	 * @return	EventHandler
	 */
	public static function getEvent() {
		return self::$eventObj;
	}

	/**
	 * Returnes the current language manager
	 *
	 * @return	LanguageManager
	 */
	public static function getLanguage() {
		return self::$languageObj;
	}
	
	/**
	 * Returnes the current Zend_Log instance
	 * @return Zend_Log
	 */
	public static function getLog() {
		return self::$loggerObj;
	}
	
	/**
	 * Returnes the current ModuleManager object
	 *
	 * @return	ModuleManager
	 */
	public static function getModuleManager() {
		return self::$moduleManagerObj;
	}
	
	/**
	 * Returnes the current Protocol object
	 *
	 * @return	Protocol
	 */
	public static function getProtocol() {
		return self::$protocolObj;
	}

	/**
	 * Returnes the current user manager object
	 *
	 * @return	UserManager
	 */
	public static function getUserManager() {
		return self::$userManagerObj;
	}

	/**
	 * Handles errors
	 *
	 * @param	integer	$errNo
	 * @param	string	$errMessage
	 * @param	string	$errFile
	 * @param	integer	$errLine
	 * @return	void
	 * @throws	SystemException
	 * @throws	RecoverableException
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
				throw new SystemException("Error in file ".$errFile." on line ".$errLine." (".$errorNo."): ".$errMessage);
			elseif ($type == 'warning' or $type == 'notice')
				throw new RecoverableException("Error in file ".$errFile." on line ".$errLine." (".$errorNo."): ".$errMessage);
		}
	}
	
	/**
	 * Handles uncought exceptions
	 *
	 * @param	Exception	$ex
	 * @return	void
	 */
	public static function handleException(Exception $ex) {
		// Call SystemException::sendDebugLog()
		if ($ex instanceof SystemException && self::$protocolObj !== null && self::$protocolObj->isAlive()) $ex->sendDebugLog();
		
		// Call Protocol::handleException()
		if ($ex instanceof ProtocolException && self::$protocolObj !== null && self::$protocolObj->isAlive()) self::$protocolObj->handleException($ex);
		
		// Call Connection::handleException()
		if ($ex instanceof ConnectionException) self::$ircObj->handleException($ex);
		 
		// Call shutdown methods if the given exception isn't recoverable (UserExceptions and RecoverableExceptions)
		if (!($ex instanceof RecoverableException) && !($ex instanceof UserException)) {
			// call connection shutdown method
			if (self::getConnection() !== null && self::$protocolObj !== null) self::getConnection()->getProtocol()->shutdownConnection($ex->getMessage());
		}
		
		// Call Zend_Log::err()
		if ($ex instanceof Zend_Exception) self::$loggerObj->err($ex);
	}
}
?>