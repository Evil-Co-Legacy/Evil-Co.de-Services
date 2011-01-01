<?php

/**
 * Handles module instances
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleHandler {
	
	/**
	 * Contains the current instance of ModuleHandler
	 * @var ModuleHandler
	 */
	protected static $instance = null;
	
	/**
	 * @see ModuleManager::$loadedModules
	 */
	protected $loadedModules = null;
	
	/**
	 * @see ModuleManager::$moduleInformation
	 */
	protected $moduleInformation = null;
	
	/**
	 * Contains all module instances
	 * @var array
	 */
	protected $moduleInstances = array();
	
	/**
	 * Creates a new instance of ModuleHandler
	 */
	protected function __construct() { }
	
	/**
	 * Creates a new instance of given module
	 * @param	string	$moduleName
	 * @return Module
	 */
	public function createInstance($moduleName) {
		// validate
		if (!isset($this->loadedModules[$moduleName])) throw new ModuleException("Trying to create an instance of unloaded module");
		if (isset($this->moduleInstances[$moduleName])) throw new ModuleException("Module '".$moduleName."' has already an instance");
		
		// create a new instance
		$namespace = $this->loadedModules[$moduleName];
		$className = $namespace."\\".$moduleName;
		$this->moduleInstances[$moduleName] = new $className();
		
		return $this->moduleInstances[$moduleName];
	}
	
	/**
	 * Returnes the current instance of ModuleHandler
	 */
	public static function getInstance() {
		if (static::$instance === null) {
			static::$instance = new ModuleHandler();
		}
		
		return static::$instance;
	}
	
	/**
	 * Sets loaded modules array
	 * @param	array	$loadedModules
	 */
	public function setLoadedModules(&$loadedModules) {
		$this->loadedModules = $loadedModules;
	}
	
	/**
	 * Sets module information array
	 * @param	array	$moduleInformation
	 */
	public function setModuleInformation(&$moduleInformation) {
		$this->moduleInformation = $moduleInformation;
	}
}
?>