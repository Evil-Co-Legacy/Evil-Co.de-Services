<?php
// services imports
require_once(SDIR.'lib/system/module/LoadedModule.class.php');
require_once(SDIR.'lib/system/module/ModuleInstance.class.php');
require_once(SDIR.'lib/system/module/ModuleStore.class.php');

/**
 * Manages modules
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleManager implements Iterator {

	/**
	 * This is a little workaround. We'll return references to modules in this class. But we can't create a pointer to 'null'
	 * @var null
	 */
	const NONEXISTANT_MODULE_INSTANCE = 1;

	/**
	 * Contains the pointer for iterator methods
	 * @var integer
	 */
	protected $instancePointer = 0;

	/**
	 * Contains information about loaded modules
	 * @var LoadedModule
	 */
	protected $loadedModules = array();

	/**
	 * Contains stored module instances
	 * @var ModuleInstance
	 */
	protected $moduleInstances = array();

	/**
	 * Creates a new instance of ModuleManager
	 */
	public function __construct() {
		// register connected event
		Services::getEventHandler()->registerEvent(array($this, 'initBots'), 'Protocol', 'connected');
		Services::getEventHandler()->registerEvent(array($this, 'initCommands'), 'Protocol', 'connected');
		Services::getEventHandler()->registerEvent(array($this, 'assignCommands'), 'Protocol', 'connected');
		Services::getEventHandler()->registerEvent(array($this, 'initExtensions'), 'Protocol', 'connected');

		// load modules
		$this->loadModules(LoadedModule::LOAD_STORE);
	}

	/**
	 * Assigns all commands to bots
	 * @return void
	 */
	public function assignCommands() {
		// add debug log
		Services::getLogger()->debug("Assigning commands ...");

		// assign each command
		foreach($this as $module) {
			// skip loop if module isn't a command
			if ($module->type != ModuleInstance::TYPE_COMMAND) continue;

			// assign command
			Services::getCommandManager()->assignCommand($module->instance, CommandManager::ASSIGN_AUTOMATIC);
		}
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// get keys
		$keys = array_keys($this->moduleInstances);

		return $this->moduleInstances[$keys[$this->instancePointer]];
	}

	/**
	 * Returns the instance of given module
	 * @param	string	$moduleName
	 */
	public function getModuleInstance($moduleName) {
		foreach($this as $module) {
			if ($module->moduleName == $moduleName) return $moduleName;
		}

		return self::NONEXISTANT_MODULE_INSTANCE;
	}

	/**
	 * Returns the information object for given module
	 * @param	string	$moduleName
	 */
	public function getModule($moduleName) {
		if (($key = $this->getModuleKey($moduleName)) !== null) return $this->loadedModules[$key];

		return self::NONEXISTANT_MODULE_INSTANCE;
	}

	/**
	 * Returns the key of loaded module information element
	 * @param	string	$moduleName
	 */
	public function getModuleKey($moduleName) {
		foreach($this->loadedModules as $key => $module) {
			if ($module->moduleName == $moduleName) return $key;
		}

		return null;
	}

	/**
	 * Starts up all bots after connection was successfully started
	 * @return void
	 */
	public function initBots() {
		// add debug log
		Services::getLogger()->debug("Starting Bots ...");

		// register each bot
		foreach($this as $module) {
			// skip this loop if module isn't a bot
			if ($module->type != ModuleInstance::TYPE_BOT) continue;

			// register bot at botmanager
			Services::getBotManager()->registerBot($module->instance, BotManager::REGISTER_AUTOMATIC);

			// register bot at commandmanager
			Services::getCommandManager()->registerBot($module->instance);
		}
	}

	/**
	 * Registeres all commands after connection was successfully started
	 * @return void
	 */
	public function initCommands() {
		// add debug log
		Services::getLogger()->debug("Registering commands ...");

		// register each command
		foreach($this as $module) {
			// skip this loop if module isn't a command
			if ($module->type != ModuleInstance::TYPE_COMMAND) continue;

			// register at command manager
			Services::getCommandManager()->registerCommand($module->instance);
		}
	}

	/**
	 * Starts all extensions
	 * @return void
	 */
	public function initExtensions() {
		// add debug log
		Services::getLogger()->debug('Starting extensions ...');

		// start each extension
		foreach($this as $module) {
			// skip loop if module isn't an extension
			if ($module->type != ModuleInstance::TYPE_EXTENSION) continue;

			// init extension
			$module->instance->init();
		}
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$keys = array_keys($this->moduleInstances);

		return $keys[$this->instancePointer];
	}

	/**
	 * Loads a module at runtime
	 * @param	string	$moduleName
	 * @param	boolean	$save
	 */
	public function loadModule($moduleName, $save = false) {
		// check for existing modules
		if ($this->getModuleInstance($moduleName) !== null) throw new ModuleException("Module %s is already loaded", $moduleName);

		// load module
		$this->loadedModules[] = new LoadedModule($moduleName, LoadedModule::LOAD_MANUAL);

		// log
		Services::getLogger()->info("Loaded module %s with identifier %s", $moduleName, $this->getModule($moduleName)->getModuleHash());

		// fire event
		Services::getEventHandler()->fire($this, 'moduleLoaded', array('module' => $this->getModule($moduleName)));

		// save
		if ($save) {
			ModuleStore::getInstance()->add($this->loadedModules[(count($this->loadedModules) - 1)]);
		}
	}

	/**
	 * Loads modules
	 * @param	integer	$loadType
	 */
	protected function loadModules($loadType = LoadedModule::LOAD_NONE) {
		// Modules disabled?
		if ($loadType == LoadedModule::LOAD_NONE) return;

		// load modules
		switch($loadType) {
			/**
			 * Automatic load
			 * Loades all modules stored in databases (Saved from last session)
			 */
			case LoadedModule::LOAD_STORE:
				// send log
				Services::getLogger()->info("Loading modules from store ...");

				// get modules from store
				$modules = ModuleStore::getInstance()->getModuleList();

				// create instances
				foreach($modules as $module) {
					$place = $this->getModule($module->moduleName);
					if ($place === self::NONEXISTANT_MODULE_INSTANCE) $this->loadModule($module->moduleName);
					$this->moduleInstances[] = new ModuleInstance($this->getModule($module->moduleName));
				}
			break;
		}
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->instancePointer;
	}

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->instancePointer = 0;
	}

	/**
	 * Unloads a module
	 * @param	string	$moduleName
	 * @param	boolean	$save
	 * @throws ModuleException
	 */
	public function unloadModule($moduleName, $save = false) {
		// check for existing modules
		if ($this->getModuleInstance($moduleName) === null) throw new ModuleException("Module %s is not loaded", $moduleName);

		$identifier = $this->getModule($moduleName)->getModuleHash();

		// unload module
		$this->getModuleInstance($moduleName)->unload();
		unset($this->loadedModules[$this->getModuleKey($moduleName)]);

		// log
		Services::getLogger()->info("Unloaded module %s with identifier %s", $moduleName, $identifier);

		// save information
		if ($save) {
			ModuleStore::getInstance()->remove($moduleName);
		}
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		// get keys
		$keys = array_keys($this->moduleInstances);

		return (isset($keys[$this->instancePointer]));
	}
}
?>