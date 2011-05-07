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
		if (file_exists(DIR.'modules/bot/'.ucfirst($moduleName).'BotModule.class.php')) $path = DIR.'modules/bot/'.ucfirst($moduleName).'BotModule.class.php';
		if (file_exists(DIR.'modules/command/'.ucfirst($moduleName).'CommandModule.class.php')) $path = DIR.'modules/command/'.ucfirst($moduleName).'CommandModule.class.php';
		if (file_exists(DIR.'modules/extension/'.ucfirst($moduleName).'ExtensionModule.class.php')) $path = DIR.'modules/extension/'.ucfirst($moduleName).'ExtensionModule.class.php';

		// no path found
		if (!isset($path)) throw new ModuleException("Cannot find module '%s'", $moduleName);

		// debug
		Services::getLogger()->debug("Creating first cache file  for module '".$moduleName."'");

		// let's have some fun with namespaces
		$tempFile = file_get_contents($path);
		$tempFile = str_replace("<?php", "<?php\nnamespace Cached".$loadedModuleInstance->getModuleHash().";", $tempFile);
		$tempFile = str_replace("extends ", "extends \\", $tempFile);
		$tempFile = str_replace("new ", "new \\", $tempFile);
		$tempFile = preg_replace("~([A-Z]([A-Z0-9_\-]+))::([A-Z0-9]+)\((.*)\)~i", "\\$1::$2($3)", $tempFile);
/*		file_put_contents(DIR.'cache/load.'.$moduleName.'.cache', $tempFile);

		// debug
		Services::getLogger()->debug("Getting module information from cache file '".DIR.'cache/load.'.$moduleName.'.cache'."'");

		// get module information
		$generator = Zend_CodeGenerator_Php_File::fromReflectedFileName(DIR.'cache/load.'.$moduleName.'.cache');

		// delete cache file
		unlink(DIR.'cache/load.'.$moduleName.'.cache');

		// debug
		Services::getLogger()->debug("Preparing new file ...");

		// set new classname
		$generator->getClass(basename($path, '.class.php'))->setName("Cache".$loadedModuleInstance->getModuleHash());

		// debug
		Services::getLogger()->debug("Generating second cache file ...");
*/
		// write cache file
		file_put_contents(DIR.'cache/'.$loadedModuleInstance->getModuleHash().'.php', $tempFile);

		// include file
		require_once(DIR.'cache/'.$loadedModuleInstance->getModuleHash().'.php');

		// eval code
		/* eval('?>'.$file->generate()); */

		// debug
		Services::getLogger()->debug("Successfully loaded second cache file");

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