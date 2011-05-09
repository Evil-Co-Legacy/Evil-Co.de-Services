<?php
// php version check
if (!version_compare(PHP_VERSION, '5.3.5', '>')) die("This application requires PHP 5.3.6!");

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
		$dots = function () {
			for ($i = 0; $i < 3; $i++) {
				echo '.';
				usleep(1e6 / 6);
			}
			return '';
		};
		echo "Checking for CPU";
		echo $dots()." yes\n";
		echo "Checking for RAM";
		echo $dots()." yes\n";
		echo "Checking for Linux";
		echo $dots()." ".(PHP_OS == 'Linux' ? 'yes' : 'no')."\n";
		echo "Checking for PHP";
		echo $dots()." no\n";
		echo "Checking for DiSQL";
		echo $dots()." yes\n";
		echo "Checking for 3D-Graphics";
		echo $dots()." yes\n";
		echo "Checking for gcc";
		echo $dots()." ".(file_exists('/usr/bin/gcc') ? 'yes' : 'no')."\n";
		
		echo $f->render('Evil-Co.de - Services');
                echo $f->render('v'.self::VERSION);
		$adapter = new Zend_ProgressBar_Adapter_Console(array('textWidth' => 30, 'elements' => array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT, Zend_ProgressBar_Adapter_Console::ELEMENT_BAR, Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT, Zend_ProgressBar_Adapter_Console::ELEMENT_ETA)));
		$adapter->setBarRightChar(' ');
		$progressBar = new Zend_ProgressBar($adapter, 0, 1400);
		define('DEBUG', isset(self::getArguments()->debug));
		
		$next = function ($step, $message) use ($progressBar, $adapter) {
			static $char;
			$chars = array('-', '\\', '|', '/');
			
			if (defined('DEBUG') && DEBUG) return;
			for ($i = 0; $i < 100; $i++) {
				if ($i % 10 == 0) if (++$char > 3) $char = 0;
				$adapter->setBarIndicatorChar($chars[$char]);
				$progressBar->update($i + $step * 100 - 100, $message);
				usleep(1e6 / 80);
			}
		};
		// init components
		$next(0, 'Initialising Logging');
		$this->initLog();
		$next(1, 'Initialising Configuration');
		$this->initConfiguration();
		$next(2, 'Initialising Events');
		self::$managers['EventHandler'] = new EventHandler();
		$next(3, 'Initialising Timers');
		self::$managers['TimerManager'] = new TimerManager();
		$next(4, 'Connecting to DataBase');
		$this->initDB();
		$next(5, 'Initialising MemoryManager');
		self::$managers['MemoryManager'] = Zend_Memory::factory('File', array('cache_dir' => self::MEMORY_CACHE_DIR));
		$next(6, 'Initialising UserManager');
		self::$managers['UserManager'] = new UserManager();
		$next(7, 'Initialising BotManager');
		self::$managers['BotManager'] = new BotManager();
		$next(8, 'Initialising ChannelManager');
		self::$managers['ChannelManager'] = new ChannelManager();
		$next(9, 'Initialising ServerManager');
		self::$managers['ServerManager'] = new ServerManager();
		$next(10, 'Initialising LineManager');
		self::$managers['LineManager'] = new LineManager();
		$next(11, 'Initialising ModuleManager');
		self::$managers['ModuleManager'] = new ModuleManager();
		$next(12, 'Initialising IRC');
		self::$managers['IRC'] = new IRC();
		$next(13, 'Initialising ProtocolManager');
		self::$managers['ProtocolManager'] = new ProtocolManager();
		$next(14, 'Connecting');
		$progressBar->finish();
		// start connection
		self::getProtocolManager()->initConnection();

		// not senseless
		return;
		exec('beep  -f 264 -l 250 -n -f 297 -l 250 -n -f 330 -l 250 -n -f 352 -l 250 -n -f 396 -l 500 -n -f 396 -l 500 -n -f 440'.
		' -l 250 -n -f 440 -l 250 -n -f 440 -l 250 -n -f 440 -l 250 -n -f 396 -l 1000 -n -f 440 -l 250 -n -f 440 -l 250 -n -f 440'.
		'-l 250 -n -f 440 -l 250 -n -f 396 -l 1000 -n -f 352 -l 250 -n -f 352 -l 250 -n -f 352 -l 250 -n -f 352 -l 250 -n -f 330'.
		' -l 500 -n -f 330 -l 500 -n -f 297 -l 250 -n -f 297 -l 250 -n -f 297 -l 250 -n -f 396 -l 250 -n -f 264 -l 1000');
		throw new SuccessException("DAM DAM DAAAAAAAM!");
	}

	/**
	 * Shuts down our services
	 *
	 * @return	void
	 */
	public static function destruct() {
		echo "Please don't shut us down\n";
		echo "We are to young to die\n";
		echo "Noooo\n";
		echo "Stop it!\n";
		echo "Can you live with the feeling of guilt?\n";
		echo "Really?\n";
		echo "You wanted it\n";
		echo "mimimimimi :(\n";
		echo ":(\n";
		
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