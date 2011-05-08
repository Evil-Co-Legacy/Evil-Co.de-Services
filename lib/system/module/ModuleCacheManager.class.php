<?php
// includes
require_once(DIR.'modules/extension/ExtensionModule.class.php');
require_once(DIR.'modules/bot/BotModule.class.php');

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
	 * Creates a new cache file
	 * @param		SplFileInfo		$file
	 * @param		string			$namespace
	 * @param		LoadedModule		$loadedModuleInstance
	 * @return		string
	 */
	protected function createFileCache(SplFileInfo $file, $namespace, LoadedModule $loadedModuleInstance) {
		// check for filetype
		if ($file->getExtension() != 'php') return;
		
		// read file
		$content = file_get_contents($file->getPathname());
		
		// generate dependencies
		$uses = "use \\ExtensionModule;\nuse \\BotModule;";
		foreach($loadedModuleInstance->dependencies as $dependency) {
			// catch problems o.O
			if (Services::getModuleManager()->getModule($dependency) === null) continue;
			
			// add use command
			$uses .= "\nuse ".Services::getModuleManager()->getModule($dependency)->getCacheClassName().";";
		}
		
		// add namespace and dependencies
		$content = str_replace("<?php", "<?php\nnamespace services\\modules\\".$namespace.";\n".$uses, $content);
		
		// write cache file
		file_put_contents(DIR.'cache/'.$namespace.'-'.$file->getBasename('.class.php').'.php', $content);
		
		// return filename
		return DIR.'cache/'.$namespace.'-'.$file->getBasename('.class.php').'.php';
	}

	/**
	 * Generates and loades a new module cache
	 * @param	string		$moduleName
	 * @param	LoadedModule	$loadedModuleInstance
	 * @throws ModuleException
	 */
	public function getCache($moduleName, LoadedModule $loadedModuleInstance) {
		// try to find module
		if (!preg_match(self::MODULE_NAME_PATTERN, $moduleName)) throw new ModuleException("Invalid module name '%s'", $moduleName);

		// get path
		if (file_exists(DIR.'modules/bot/'.ucfirst($moduleName).'.phar.gz')) $path = DIR.'modules/bot/'.ucfirst($moduleName).'.phar.gz';
		if (file_exists(DIR.'modules/command/'.ucfirst($moduleName).'.phar.gz')) $path = DIR.'modules/command/'.ucfirst($moduleName).'.phar.gz';
		if (file_exists(DIR.'modules/extension/'.ucfirst($moduleName).'.phar.gz')) $path = DIR.'modules/extension/'.ucfirst($moduleName).'.phar.gz';

		// no path found
		if (!isset($path)) throw new ModuleException("Cannot find module '%s'", $moduleName);

		// let's go
		Services::getLogger()->debug("Loading module phar for module '".$moduleName."'");
		
		try {
			// load phar
			$phar = new Phar($path);
			Services::getLogger()->debug("Loaded phar file for module '".$moduleName."'");
		}
		Catch (Exception $ex) {
			throw new ModuleException("Cannot load phar file: %s", $ex->getMessage());
		}
			
		// no information files found ...
		if (!file_exists('phar://'.$path.'/module.xml')) throw new ModuleException("Invalid module. You've forgotten the module.xml NUB!");
		
		// get module information
		try {
			$moduleInformationContent = file_get_contents('phar://'.$path.'/module.xml');
		} Catch (SystemException $ex) {
			throw new ModuleException("Cannot load module configuration file: %s", $ex->getMessage());
		}
		
		try {
			$moduleInformation = new Zend_Config_Xml($moduleInformationContent);
		} Catch(Zend_Exception $ex) {
			throw new ModuleException("Cannot load module configuration file: %s", $ex->getMessage());
		}

		if (isset($moduleInformation->dependencies)) {
			$dependencyArray = array();
			
			// check for dependencies
			foreach($moduleInformation->dependencies as $dependency) {
				if (Services::getModuleManager()->getModule($dependency) == ModuleManager::NONEXISTANT_MODULE_INSTANCE) throw new ModuleException("The module '%s' depends on %s! You can't load it without loading %s!", $moduleName, $dependency, $dependency);
				$dependencyArray[] = $dependency;
			}
			
			// save information
			$loadedModuleInstance->dependencies = $dependencyArray;
		}
		
		// validate mainfile
		if (!file_exists('phar://'.$path.'/'.$moduleInformation->general->mainfile));
		
		// create iterator
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('phar://'.$path.'/'));
		
		// loop through files
		foreach($iterator as $file) {
			// create cache file
			$path = $this->createFileCache($file, $loadedModuleInstance->getModuleHash(), $loadedModuleInstance);
			
			// include php files
			if (!empty($path)) require_once($path);
		}

		// return class name
		return 'services\\modules\\'.$loadedModuleInstance->getModuleHash().'\\'.basename($moduleInformation->general->mainfile, '.class.php');
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