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
	 * Creates a new instance of ModuleManager
	 */
	public function __construct() {
		// check dependencies
		if (!extension_loaded('runkit') and !@dl('runkit.so') and !@dl('php_runkit.dll')) throw new ModuleException("Required runtime libary not found!");
		
		// load modules from database
		$sql = "SELECT
					*
				FROM
					module";
		$result = Services::getDB()->sendQuery($sql);
		
		while($row = Services::getDB()->fetchArray($result)) {
			$this->loadModule(SDIR.'lib/modules/'.$row['name'].'.class.php');
		}
	}
	
	/**
	 * Loads a module
	 * @param	string	$file
	 */
	public function loadModule($file) {
		// validate
		if (!file_exists($file)) throw new ModuleException("Cannot find module file '".$file."'");
		if (!is_readable($file)) throw new ModuleException("Cannot read module file '".$file."'");
		
		// get class name
		$moduleName = basename($file, '.class.php');
		
		// create runkit sandbox
		$this->availableModules[$moduleName] = array('sandbox' => new Runkit_Sandbox(Services::getConfiguration()->get('modules')));
		$this->availableModules[$moduleName]['sandbox']->eval("require_once('".$file."');");
		$this->availableModules[$moduleName]['sandbox']->eval('$moduleType = (is_subclass_of("BotModule") ? "Bot" : (is_subclass_of("CommandModule") ? "Command" : "Extension"));');
		$this->availableModules[$moduleName]['type'] = $this->availableModules[$moduleName]['sandbox']->moduleType;
		$this->availableModules[$moduleName]['sandbox']->eval('unset($moduleType);');
	}
}
?>