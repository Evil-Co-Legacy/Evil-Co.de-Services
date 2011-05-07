<?php
// php version check
if (!version_compare(PHP_VERSION, '5.3.0', '>=')) die("This application requires PHP 5.3!");

// defines
define('IRCD', 'inspircd');
// set this to your location
date_default_timezone_set('Europe/Berlin');

// imports
require_once(DIR.'lib/core.functions.php');
require_once(DIR.'lib/system/event/EventHandler.class.php');
require_once(DIR.'lib/system/irc/ChannelManager.class.php');
require_once(DIR.'lib/system/irc/IRC.class.php');
require_once(DIR.'lib/system/irc/LineManager.class.php');
require_once(DIR.'lib/system/irc/ProtocolManager.class.php');
require_once(DIR.'lib/system/irc/ServerManager.class.php');
require_once(DIR.'lib/system/language/LanguageManager.class.php');
require_once(DIR.'lib/system/log/IRCLogWriter.class.php');
require_once(DIR.'lib/system/module/ModuleManager.class.php');
require_once(DIR.'lib/system/timer/TimerManager.class.php');
require_once(DIR.'lib/system/user/BotManager.class.php');
require_once(DIR.'lib/system/user/UserManager.class.php');

/**
 * Manages all needed core instances
 *
 * @author	Johannes Donath, Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class Services {

	/**
	 * Contains the dir where our services should store cached information
	 *
	 * @var string
	 */
	const MEMORY_CACHE_DIR = './cache/';

	const VERSION = '2.0.0-eatsChildren';

	protected static $managers = array();

	private static $instanciated = false;

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
		if (self::$instanciated) {
			throw new SystemException('Tried to instanciate Services twice');
			return;
		}

		self::$instanciated = true;
		self::updateTitle();
		
		// correct dir
		@chdir(DIR);

		try {
			// read arguments
			self::$managers['Arguments'] = new Zend_Console_Getopt(array(
				'debug' => 'Enables debug mode',
				'quiet|q-i' => 'Prints less output',
				'verbose|v-i' => 'Prints more output',
				'config=s' => 'Define a config-file'
			));
			self::getArguments()->parse();
		}
		catch (Zend_Console_Getopt_Exception $e) {
			echo $e->getUsageMessage();
			exit;
		}

		$f = new Zend_Text_Figlet(array('smushMode' => 7, 'font' => DIR.'font.gz'));
		echo $f->render('Evil-Co.de - Services');
                echo $f->render('v'.self::VERSION);
		$progressBar = new Zend_ProgressBar(new Zend_ProgressBar_Adapter_Console(array('textWidth' => 30, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT, Zend_ProgressBar_Adapter_Console::ELEMENT_BAR, Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_ETA))), 0, 14);

		define('DEBUG', isset(self::getArguments()->debug));

		// init components
		$progressBar->update(0, 'Initialising Logging');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		$this->initLog();
		$progressBar->update(1, 'Initialising Configuration');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		$this->initConfiguration();
		$progressBar->update(2, 'Initialising Events');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['EventHandler'] = new EventHandler();
		$progressBar->update(3, 'Initialising Timers');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['TimerManager'] = new TimerManager();
		$progressBar->update(4, 'Connecting to DataBase');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		$this->initDB();
		$progressBar->update(5, 'Initialising MemoryManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['MemoryManager'] = Zend_Memory::factory('File', array('cache_dir' => self::MEMORY_CACHE_DIR));
		$progressBar->update(6, 'Initialising UserManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['UserManager'] = new UserManager();
		$progressBar->update(7, 'Initialising BotManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['BotManager'] = new BotManager();
		$progressBar->update(8, 'Initialising ChannelManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['ChannelManager'] = new ChannelManager();
		$progressBar->update(9, 'Initialising ServerManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['ServerManager'] = new ServerManager();
		$progressBar->update(10, 'Initialising LineManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['LineManager'] = new LineManager();
		$progressBar->update(11);
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['ModuleManager'] = new ModuleManager();
		$progressBar->update(12, 'Initialising IRC');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['IRC'] = new IRC();
		$progressBar->update(13, 'Initialising ProtocolManager');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		self::$managers['ProtocolManager'] = new ProtocolManager();
		$progressBar->update(14, 'Connecting');
		if (!defined('DEBUG') || !DEBUG) sleep(rand(1,3));
		$progressBar->finish();
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

		// close database-connection
		if (isset(self::$managers['DB']) && self::$managers['DB'] !== null) self::$managers['DB']->closeConnection();

		if (!defined('DEBUG') || !DEBUG) {
			$cacheFiles = glob(DIR.'cache/*');
			foreach ($cacheFiles as $file) {
				unlink($file);
			}
		}

		// remove pidfile (if any)
		if (file_exists(DIR.'services.pid')) @unlink(DIR.'services.pid');

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
		if ($config === null) $config = DIR.'config/config.xml';

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
		$file = new Zend_Log_Writer_Stream(fopen(DIR.'logs/services-'.gmdate('M-d-Y').'.log', 'a', false));
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
			$filter = new Zend_Log_Filter_Priority(max(0, min((Zend_LOG::DEBUG - 1), Zend_Log::ERR - (int) self::getArguments()->quiet + (int) self::getArguments()->verbose)), '<=');
			$inline->addFilter($filter);
			$irc->addFilter($filter);

			$file->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG, '<'));
		}
		else {
			$filter = new Zend_Log_Filter_Priority(8, '<=');
			$inline->addFilter($filter);
			$file->addFilter($filter);
			$irc->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG, '<='));
		}

		// add log entry
		self::getLogger()->info("Evil-Co.de Services ".self::VERSION." running on PHP ".phpversion());
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
				exit;
			case SIGUSR1:
			case SIGUSR2:
			break;
			case SIGHUP:
				if (!isset(self::$managers['ExternalManager'])) {
					require_once(DIR.'lib/system/external/ExternalManager.class.php');
					self::$managers['ExternalManager'] = new ExternalManager();
				}
				self::$managers['ExternalManager']->fire();
		}
	}
	
	public static function updateTitle() {
		if (function_exists('setproctitle')) setproctitle('Evil-Co.de - Services v'.self::VERSION);
	}
	
	public static function getRandomString() {
		return sha1(rand().microtime());
	}

	public static function removeCR($string) {
		return str_replace(array("\r\n", "\r"), "\n", $string);
	}
}
?>