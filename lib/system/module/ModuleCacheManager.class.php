<?php
/**
 * Generates cache files for modules
 *
 * @author	Johannes Donath, Tim Düsterhus
 * @copyright	2010 - 2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleCacheManager {

	/**
	 * Contains a pattern for module names
	 * @var string
	 */
	const MODULE_NAME_PATTERN = '~(42|21|[A-Z]([A-Z0-9_\-]+))~i';

	/**
	 * Contains the instance for factory pattern
	 * @var ModuleCacheManager
	 */
	protected static $instance = null;

	/**
	 * Constructor is marked as protected for factory pattern
	 */
	protected function __construct() { }

	private function __clone() { }

	/**
	 * Generates and loades a new module cache
	 * @param	string		$moduleName
	 * @param	LoadedModule	$loadedModuleInstance
	 * @throws ModuleException
	 */
	public function getCache($moduleName, LoadedModule &$loadedModuleInstance) {
		// try to find module
		if (!preg_match(self::MODULE_NAME_PATTERN, $moduleName)) throw new ModuleException("Invalid module name '%s'", $moduleName);

		// get path
		if (file_exists(DIR.'modules/bot/'.ucfirst($moduleName).'.phar')) $path = DIR.'modules/bot/'.ucfirst($moduleName).'.phar';
		if (file_exists(DIR.'modules/command/'.ucfirst($moduleName).'.phar')) $path = DIR.'modules/command/'.ucfirst($moduleName).'.phar';
		if (file_exists(DIR.'modules/extension/'.ucfirst($moduleName).'.phar')) $path = DIR.'modules/extension/'.ucfirst($moduleName).'.phar';

		// no path found
		if (!isset($path)) throw new ModuleException("Cannot find module '%s'", $moduleName);

		// let's go
		Services::getLogger()->debug("Loading module phar for module '".$moduleName."'");
		
		// no information files found ...
		if (!file_exists('phar://'.$path.'/module.xml')) throw new ModuleException("Invalid module. You've forgotten the module.xml NUB!");
		
		// get module information
		try {
			$moduleInformationContent = file_get_contents('phar://'.$path.'/module.xml');
		} Catch (PharException $ex) {
			throw new ModuleException($ex->getMessage());
		} Catch (SystemException $ex) {
			throw new ModuleException($ex->getMessage());
		}
		
		try {
			$moduleInformation = new Zend_Config_Xml($moduleInformationContent);
		} Catch(Zend_Exception $ex) {
			throw new ModuleException($ex->getMessage());
		}
		
		unset($moduleInformationContent); // we'll safe memory here ...


		// return class name
		return $loadedModuleInstance->getModuleHash();
	}

	/**
	 * Returnes an instance of ModuleCacheManager
	 *
	 * @return ModuleCacheManager
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
?>