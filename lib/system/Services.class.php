<?php
// defines
define('IRCD', 'inspircd');

// imports
require_once(SDIR.'lib/core.functions.php');
require_once(SDIR.'lib/system/configuration/Configuration.class.php');
require_once(SDIR.'lib/system/event/EventHandler.class.php');
require_once(SDIR.'lib/system/irc/IRCConnection.class.php');
require_once(SDIR.'lib/system/language/LanguageManager.class.php');
require_once(SDIR.'lib/system/module/ModuleManager.class.php');
require_once(SDIR.'lib/system/user/UserManager.class.php');
require_once(SDIR.'lib/system/user/BotManager.class.php');

/**
 * Manages all needed core instances
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Services {
	
	/**
	 * Contains the Configuration object
	 * @var Configuration
	 */
	protected static $configObj = null;
	
	/**
	 * Contains the EventHandler object
	 * @var	EventHandler
	 */
	protected static $eventObj = null;
	
	/**
	 * Contains the database connection
	 * @var	Database
	 */
	protected static $dbObj = null;
	
	/**
	 * Contains the LanguageManager object
	 * @var	LanguageManager
	 */
	protected static $languageObj = null;
	
	/**
	 * Contains the IRCConnection object
	 * @var	IRCConnection
	 */
	protected static $ircObj = null;
	
	/**
	 * Contains the UserManager object
	 * @var UserManager
	 */
	protected static $userManagerObj = null;
	
	/**
	 * Contains the BotManager object
	 * @var	BotManager
	 */
	protected static $botManagerObj = null;
	
	/**
	 * Contains the ModuleManager object
	 * @var	ModuleManager
	 */
	protected static $moduleManager = null;
	
	/**
	 * Creates a new instance of Services
	 */
	public function __construct() {
		$this->initConfiguration();
		$this->initEvents();
		$this->initDB();
		$this->initLanguage();
		$this->initConnection();
		$this->initUserManager();
		$this->initBotManager();
		$this->initModules();
	}
	
	/**
	 * Shuts down our services
	 */
	public static function destruct() {
		if (self::getConnection() !== null) self::getConnection()->shutdown();
	}
	
	/**
	 * Creates a new configuration object
	 */
	protected function initConfiguration() {
		self::$configObj = new Configuration();
	}
	
	/**
	 * Creates a new EventHandler object
	 */
	protected function initEvents() {
		self::$eventObj = new EventHandler();
	}
	
	/**
	 * Creates a new database connection
	 */
	protected function initDB() {
		self::$dbObj = new MySQLDatabase($dbName, $dbUser, $dbPassword, $dbHost, $dbCharset = 'UTF8');
	}
	
	/**
	 * Creates a new LanguageManager instance
	 */
	protected function initLanguage() {
		self::$languageObj = new LanguageManager();
	}
	
	/**
	 * Creates a new IRCConnection instance
	 */
	protected function initConnection() {
		self::$ircObj = new IRCConnection();
	}
	
	/**
	 * Creates a new UserManager instance
	 */
	protected function initUserManager() {
		self::$userManagerObj = new UserManager();
	}
	
	/**
	 * Returnes the current configuration object
	 */
	public static function getConfiguration() {
		return self::$configObj;
	}
	
	/**
	 * Returnes the current EventHandler object
	 */
	public static function getEvent() {
		return self::$event;
	}
	
	/**
	 * Returnes the current database connection
	 */
	public static function getDB() {
		return self::$dbObj;
	}
	
	/**
	 * Returnes the current language manager
	 */
	public static function getLanguage() {
		return self::$languageObj;
	}
	
	/**
	 * Returnes the current irc connection
	 */
	public static function getConnection() {
		return self::$ircObj;
	}
	
	/**
	 * Returnes the current  current user manager object
	 */
	public static function getUserManager() {
		return self::$userManagerObj;
	}
	
	/**
	 * Handles uncought exceptions
	 * @param	Exception	$ex
	 */
	public static function handleException(Exception $ex) {
		print($ex);
	}
	
	/**
	 * Handles errors
	 * @param	integer	$errNo
	 * @param	string	$errMessage
	 * @param	string	$errFile
	 * @param	integer	$errLine
	 */
	public static function handleError($errNo, $errMessage, $errFile, $errLine) {
		throw new Exception("Error in file ".$errFile." on line ".$errLine." (".$errNo."): ".$errMessage);
	}
}
?>