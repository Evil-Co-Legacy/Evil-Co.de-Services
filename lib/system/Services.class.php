<?php
// php version check
if (!version_compare(PHP_VERSION, '5.3.0', '>=')) die("This application requires PHP 5.3!");

// defines
define('IRCD', 'inspircd');
define('SERVICES_VERSION', '2.0.0-eatsChildren');
// set this to your location
date_default_timezone_set('Europe/Berlin');

// imports
require_once(SDIR.'lib/core.functions.php');
require_once(SDIR.'lib/system/event/EventHandler.class.php');
require_once(SDIR.'lib/system/irc/ChannelManager.class.php');
require_once(SDIR.'lib/system/irc/IRC.class.php');
require_once(SDIR.'lib/system/irc/LineManager.class.php');
require_once(SDIR.'lib/system/irc/ProtocolManager.class.php');
require_once(SDIR.'lib/system/irc/ServerManager.class.php');
require_once(SDIR.'lib/system/language/LanguageManager.class.php');
require_once(SDIR.'lib/system/log/IRCLogWriter.class.php');
require_once(SDIR.'lib/system/module/ModuleManager.class.php');
require_once(SDIR.'lib/system/timer/TimerManager.class.php');
require_once(SDIR.'lib/system/user/BotManager.class.php');
require_once(SDIR.'lib/system/user/UserManager.class.php');

// Zend imports
require_once('Zend/Config/Xml.php');
require_once('Zend/Db.php');
require_once('Zend/Log.php');
require_once('Zend/Log/Writer/Stream.php');
require_once('Zend/Memory.php');
require_once('Zend/Console/Getopt.php');
require_once('Zend/Text/Figlet.php');
/**
 * Manages all needed core instances
 *
 * @author	Johannes Donath, Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Services {
	
	/**
	 * Contains the dir where our services should store cached information
	 *
	 * @var string
	 */
	const MEMORY_CACHE_DIR = './cache/';
	
	
	protected static $managers = array();
	
	/**
	 * Contains the LanguageManager object
	 *
	 * @var	array<LanguageManager>
	 */
	protected static $languages = array();

	/**
	 * Creates a new instance of Services
	 */
	public function __construct() {
		// correct dir
		@chdir(SDIR);
		
		try {
			// read arguments
			self::$managers['Arguments'] = new Zend_Console_Getopt(array('debug' => 'Enables debug mode', 'quiet|q-i' => 'Prints less output',  'verbose|v-i' => 'Prints more output', 'config=s' => 'Define a config-file'));
			self::getArguments()->parse();
		}
		catch (Zend_Console_Getopt_Exception $e) {
			echo $e->getUsageMessage();
			exit;
		}
		$f = new Zend_Text_Figlet(array('smushMode' => 7));
		echo $f->render('Services');
		
		define('DEBUG', isset(self::getArguments()->debug));
		
		// init components
		$this->initLog();
		$this->initConfiguration();
		self::$managers['EventHandler'] = new EventHandler();
		self::$managers['TimerManager'] = new TimerManager();
		$this->initDB();
		self::$managers['MemoryManager'] = Zend_Memory::factory('File', array('cache_dir' => self::MEMORY_CACHE_DIR));
		self::$managers['UserManager'] = new UserManager();
		self::$managers['BotManager'] = new BotManager();
		self::$managers['ChannelManager'] = new ChannelManager();
		self::$managers['ServerManager'] = new ServerManager();
		self::$managers['LineManager'] = new LineManager();
		self::$managers['ModuleManager'] = new ModuleManager();
		self::$managers['IRC'] = new IRC();
		self::$managers['ProtocolManager'] = new ProtocolManager();
		
		// start connection
		self::getProtocolManager()->initConnection();
		
		// not senseless
		return;
		throw new SuccessException("DAM DAM DAAAAAAAM!");
	}

	/**
	 * Shuts down our services
	 *
	 * @return	void
	 */
	public static function destruct() {
		// call protocol shutdown method
		if (isset(self::$managers['ProtocolManager']) && self::$managers['ProtocolManager'] !== null && self::$managers['ProtocolManager']->isAlive() && self::$managers['IRC']->isAlive()) self::$managers['ProtocolManager']->shutdown();
		
		// call connection shutdown method
		if (isset(self::$managers['IRC']) && self::$managers['IRC'] !== null && self::$managers['IRC']->isAlive()) self::$managers['IRC']->shutdown();
		
		if (!defined('DEBUG') || !DEBUG) {
			$cacheFiles = glob(SDIR.'cache/*');
			foreach ($cacheFiles as $file) {
				unlink($file);
			}
		}
		
		// remove pidfile (if any)
		if (file_exists(SDIR.'services.pid')) @unlink(SDIR.'services.pid');
		
		// add shutdown log entry
		if (isset(self::$managers['Logger']) && self::$managers['Logger'] !== null) self::$managers['Logger']->info("Shutting down ...");
	}

	/**
	 * Creates a new configuration object
	 *
	 * @return	void
	 */
	protected function initConfiguration() {
		// get from argumentList
		$config = self::getArguments()->config;
		
		// fallback
		if ($config === null) $config = SDIR.'config/config.xml';
		
		// log event
		self::getLogger()->info("Reading configuration file '".$config."'");
		
		// start config manager
		self::$managers['Configuration'] = new Zend_Config_Xml($config);
	}
	
	/**
	 * Creates a new database connection
	 *
	 * @return	void
	 */
	protected function initDB() {
		self::$managers['DB'] = Zend_Db::factory(self::getConfiguration()->database);
		self::$managers['DB']->setFetchMode(Zend_Db::FETCH_OBJ);
	}
	
	/**
	 * Creates a new Zend_Log_Writer_Stream and Zend_Log instance
	 * @return void
	 */
	protected function initLog() {
		self::$managers['Logger'] = new Zend_Log();
		// set timestamp format
		self::getLogger()->setTimestampFormat('H:i:s');
		
		// add irc debug level
		self::getLogger()->addPriority('IRCDEBUG', 8);
		
		// create formatter
		$formatter = new Zend_Log_Formatter_Simple('[%timestamp%] %priorityName% (%priority%): %message%' . PHP_EOL);
		
		// add file writer
		$file = new Zend_Log_Writer_Stream(fopen(SDIR.'logs/services-'.gmdate('M-d-Y').'.log', 'a', false));
		$file->setFormatter($formatter);
		self::getLogger()->addWriter($file);

		// add irc writer
		$irc = new IRCLogWriter();
		$irc->setFormatter($formatter);
		self::getLogger()->addWriter($irc);
		
		$inline = new Zend_Log_Writer_Stream(STDOUT);
		$inline->setFormatter($formatter);
		self::getLogger()->addWriter($inline);
		
		if (!DEBUG) {
			$filter = new Zend_Log_Filter_Priority(Zend_Log::ERR - (int) self::getArguments()->quiet + (int) self::getArguments()->verbose, '<=');
			$inline->addFilter($filter);
			$irc->addFilter($filter);
			
			$file->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG, '<'));
		}
				
		// add log entry
		self::getLogger()->info("Evil-Co.de Services ".SERVICES_VERSION." running on PHP ".phpversion());
	}
	
	public static function __callStatic($function, $args) {
		if (!isset(self::$managers[substr($function, 3)])) return null;
		return self::$managers[substr($function, 3)];
	}

	public static function getLanguage($language) {
		if (!isset(self::$languages[$language])) self::$languages[$language] = new LanguageManager($language);
		
		return self::$languages[$language];
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
		if ($ex instanceof SystemException && self::getProtocolManager() !== null && self::getProtocolManager()->isAlive()) $ex->sendDebugLog();
		
		// Call Zend_Log::err()
		if ($ex instanceof Zend_Exception && self::getLogger() !== null) self::getLogger()->err($ex);
		
		// send stacktrace
		if ($ex instanceof SystemException && self::getLogger() !== null) self::getLogger()->err($ex->__getTraceAsString());
		
		// Call Protocol::handleException()
		if ($ex instanceof ProtocolException && self::getProtocolManager() !== null && self::getProtocolManager()->isAlive()) self::getProtocolManager()->handleException($ex);
		
		// Call Connection::handleException()
		if ($ex instanceof ConnectionException && self::getIRC() !== null) self::getIRC()->handleException($ex);
		 
		// Call shutdown methods if the given exception isn't recoverable (UserExceptions and RecoverableExceptions)
		if (!($ex instanceof RecoverableException)) {
			// call connection shutdown method
			if (self::getConnection() !== null && self::getProtocolManager() !== null) self::getConnection()->getProtocol()->shutdownConnection($ex->getMessage());
		
			// kill services :>
			exit;
		}
	}
	
	public static function signalHandler($signal) {
		switch($signal) {
			case SIGTERM:
			
			case SIGUSR1:
			
			case SIGUSR2:
			
			case SIGHUP:
		}
	}
	
	public static function getRandomString() {
		return sha1(rand().microtime());
	}
	
	public function removeCR($string) {
		return str_replace(array("\r\n", "\r"), "\n", $string);
	}
}
?>