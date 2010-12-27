<?php
// imports
require_once(SDIR.'lib/system/module/ModuleException.class.php');

/**
 * Manages module instances
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class ModuleManager {

	/**
	 * Contains all availablemodules
	 * @var	array<Module>
	 */
	protected $availableModules = array();

	/**
	 * Contains the module information of each module
	 * @var	array
	 */
	protected $moduleInformation = array();

	/**
	 * Contains all running bots
	 * @var	array<array<BotModule>>
	 */
	protected $runningBots = array();

	/**
	 * Creates a new instance of ModuleManager
	 */
	public function init() {
		$this->readModuleList();
	}

	/**
	 * Reads all modules from cache or database
	 */
	protected function readModuleList() {
		if (!Services::memcacheLoaded() or !$this->readModuleListCache()) {
			// add debug log
			if (defined('DEBUG') and Services::memcacheLoaded()) Services::getConnection()->getProtocol()->sendLogLine("Cannot read modules from memcache! Loading from database and storing data in memcache ...");

			// create needed arrays
			$modules = array();
			$botInstances = array();

			// load modules from database
			$sql = "SELECT
						*
					FROM
						module";
			$result = Services::getDB()->sendQuery($sql);

			while($row = Services::getDB()->fetchArray($result)) {
				$modules[] = $row;
				$this->loadModule(SDIR.'lib/modules/'.$row['name'].'.class.php', $row['address'], true);
			}

			$sql = "SELECT
						*
					FROM
						module_instance_bot";
			$result = Services::getDB()->sendQuery($sql);

			while($row = Services::getDB()->fetchArray($result)) {
				$botInstances[] = $row;
				$this->createBotInstance($row['moduleAddress'], $row['trigger'], $row['nick'], $row['hostname'], $row['ident'], $row['ip'], $row['modes'], $row['gecos']);
			}

			// bind commands
			$sql = "SELECT
						*
					FROM
						module_instance_command
					ORDER BY
						commandName ASC";
			$result = Services::getDB()->sendQuery($sql);

			while($row = Services::getDB()->fetchArray($result)) {
				$this->runningBots[$row['parentAddress']]->registerCommand(new $row['address']($this->runningBots[$row['parentAddress']], $row['commandName'], ($row['appearInHelp'] ? true : false)));
			}

			// check for memcache support and store data
			if (Services::memcacheLoaded()) {
				Services::getMemcache()->add('moduleList', $modules);
				Services::getMemcache()->add('botInstances', $botInstances);
			}
		}
	}

	/**
	 * Loads modules from memcache
	 */
	protected function readModuleListCache() {
		// check for memcache extension
		if (!Services::memcacheLoaded()) return false;

		// check for stored data
		if (!Services::getMemcache()->get('moduleList') or !Services::getMemcache()->get('botInstances')) return false;

		// load data
		$modules = Services::getMemcache()->get('moduleList');
		$botInstances = Services::getMemcache()->get('botInstances');

		// debug log
		if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Loading modules from cache ...");

		// start modules
		foreach($modules as $row) {
			$this->loadModule(SDIR.'lib/modules/'.$row['name'].'.class.php', $row['address'], true);
		}

		// start bot instances
		foreach($botInstances as $row) {
			$this->createBotInstance($row['moduleAddress'], $row['trigger'], $row['nick'], $row['hostname'], $row['ident'], $row['ip'], $row['modes'], $row['gecos']);
		}

		// ok all done
		return true;
	}

	/**
	 * Notifies bots if a new messages received
	 * @param	UserType	$user
	 * @param	string		$target
	 * @param	string	$message
	 */
	public function handleLine($user, $target, $message) {
		if ($target{0} != '#') {
			foreach($this->runningBots as $key => $bot) {
				if ($this->runningBots[$key]->getBot()->getUuid() == $target)  $this->runningBots[$key]->handleLine($user, $target, $message);
			}
		} else {
			$trigger = $message{0};
			$message = substr($message, 1);

			foreach($this->runningBots as $key => $bot) {
				if (strtolower($this->runningBots[$key]->getTrigger()) == strtolower($trigger)) $this->runningBots[$key]->handleLine($user, $target, $message);
			}
		}
	}

	/**
	 * Loads a module
	 * @param	string	$file
	 */
	public function loadModule($file, $moduleAddress = null, $fromDatabase = false) {
		// validate
		if (!file_exists($file)) throw new ModuleException("Cannot find module file '".$file."'");
		if (!is_readable($file)) throw new ModuleException("Cannot read module file '".$file."'");

		// get class name
		$moduleName = basename($file, '.class.php');
		if (!$moduleAddress === null) $moduleAddress = "Ox".strtoupper(dechex((time() + count($this->availableModules)) * 100000));

		// validate module
		if (isset($this->availableModules[$moduleName])) throw new ModuleException("Module '".$moduleName."' is already loaded!");
		if (in_array($moduleAddress, $this->availableModules)) throw new ModuleException("What the hell?! A module with address ".$moduleAddress." is already loaded! This should never happen!!!");

		// read module file
		$module = file_get_contents($file);

		// replace module name with address
		$module = str_replace($moduleName, $moduleAddress, $module);

		// write file
		file_put_contents(SDIR.'cache/'.$moduleAddress.'.php', $module);

		// load module
		require_once(SDIR.'cache/'.$moduleAddress.'.php');

		if (!$fromDatabase) {
			// write address to database
			$sql = "INSERT INTO
						module (`name`, `address`, `timestamp`)
					VALUES
						('".$moduleName.", '".$moduleAddress."', '".time()."');";
			Services::getDB()->sendQuery($sql);
		}

		// get module type
		switch(get_parent_class($moduleAddress)) {
			case "BotModule":
				$moduleType = 'Bot';
				break;
			case "CommandModule":
				$moduleType = 'Command';
				break;
			default:
				$moduleType = 'Extension';
				break;
		}

		// register module
		$this->availableModules[$moduleName] = $moduleAddress;

		// write module information
		$this->moduleInformation[$moduleAddress] = array('type' => $moduleType);

		// send debug log message
		if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Loaded module at address ".$moduleAddress);

		return $moduleAddress;
	}

	/**
	 * Creates a new bot instance
	 * @param	string	$moduleAddress
	 * @param	string	$trigger
	 * @param	string	$nick
	 * @param	string	$hostname
	 * @param	string	$ident
	 * @param	string	$ip
	 * @param	string	$modes
	 * @param	string	$gecos
	 */
	public function createBotInstance($moduleAddress, $trigger, $nick, $hostname, $ident, $ip, $modes, $gecos) {
		// validate
		if (!$this->moduleLoaded($moduleAddress)) throw new ModuleException("No module found at address ".$moduleAddress."!");

		// validate module information
		if ($this->moduleInformation[$moduleAddress]['type'] != 'Bot') throw new ModuleException("You can only create instances of bot modules!");

		// create bot user
		$user = Services::getConnection()->getProtocol()->createBot($nick, $hostname, $ident, $ip, $modes, $gecos);

		// create instance of BotModule
		$this->runningBots[$moduleAddress] = new $moduleAddress($user, $trigger);
	}

	/**
	 * Returnes true if a module with given address exists
	 * @param	string	$moduleAddress
	 */
	public function moduleLoaded($moduleAddress) {
		foreach($this->availableModules as $module) {
			if ($module == $moduleAddress) return true;
		}
		return false;
	}
}
?>