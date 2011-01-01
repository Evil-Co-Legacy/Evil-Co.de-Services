<?php
// imports
require_once(SDIR.'lib/system/module/ModuleHandler.class.php');
require_once(SDIR.'lib/system/module/ModuleParser.class.php');

/**
 * Manages module instances
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleManager {
	
	/**
	 * Contains a relative path from SDIR to location where module classes are stored
	 * @var unknown_type
	 */
	const MODULE_DIR = 'lib/modules/';
	
	/**
	 * Contains a relative path from SDIR to location where module information are stored
	 * @var stromg
	 */
	const MODULE_INFO_DIR = 'lib/modules/info/';
	
	/**
	 * Contains all known namespaces in the following format:
	 * array (
	 * 	[moduleName] => namespace
	 * 	...
	 * )
	 * @var array<string>
	 */
	protected $loadedModules = array();
	
	/**
	 * Contains information about loaded modules
	 * @var array<string>
	 */
	protected $moduleInformation = array();
	
	/**
	 * Contains dependencies for modules. The array contains the following layout:
	 * array(
	 * 	[module1] => array(
	 * 		[module2]
	 * 		[module3]
	 * 		[module4]
	 * 	)
	 * 	[module2] => array(
	 * 		...
	 * 	)
	 * )
	 * @var array
	 */
	protected $moduleDependencyTree = array();
	
	/**
	 * Creates a new instance of ModuleManager
	 */
	public function __construct() {
		ModuleHandler::getInstance()->setModuleInformation($this->moduleInformation);
	}
	
	/**
	 * Loads a module to memory
	 * @param	string	$name
	 * @return string
	 */
	public function loadModule($name) {
		// try to find module information
		if (!file_exists(SDIR.self::MODULE_INFO_DIR.$name.'.xml')) throw new ModuleException("Cannot find module information for module '".$name."'");
		
		// read module information
		$xml = new XML(SDIR.self::MODULE_INFO_DIR.$name.'.xml');
		$info = $xml->getElementTree('information');
		
		// try to find module information
		if (!file_exists(SDIR.$info['filename'])) throw new ModuleException("Cannot find defined module file for module '".$name."'");
		
		// create needed dependency array
		$dependencyMap = array();
		
		// create known namespace array
		$knownNamespaces = array();
		
		// loop through given requirements
		foreach($info['requirements'] as $requirement) {
			if (!in_array($requirement, array_keys($this->loadedModules)))
				throw new ModuleException("Cannot load module '".$name."'! Required module '".$requirement."' isn't loaded");
			else {
				foreach($this->moduleDependencyTree[$requirement] as $dependency) {
					if (!in_array($dependency, $dependencyMap)) $dependencyMap[] = $dependency;
				}
			}
			if (!in_array($requirement, $dependencyMap)) $dependencyMap[] = $requirement;
		}
		
		if (isset($info['parentExtension'])) {
			// try to find parent package
			if (!in_array($info['parentExtension'], array_keys($this->loadedModules))) throw new ModuleException("What the hell? You should really add the parent package to requirement list my friend ...");
			
			$namespace = $this->loadedModules[$info['parentExtension']];
		}
		
		// start parser
		$namespace = ModuleParser::parseModule(SDIR.$info['filename'], (isset($namespace) ? $namespace : null));
		
		// load parsed file
		require_once(SDIR.ModuleParser::PARSER_DIR.$namespace."/".basename($info['filename']));
		
		// add to loaded module list
		$this->loadedModules[$name] = $namespace;
		
		// write dependency map
		$this->moduleDependencyTree[$name] = $dependencyMap;
		
		// write module information
		$this->moduleInformation[$name] = array('type' => $info['type']);
		
		// return namespace
		return $namespace;
		
		// whoops?!
		throw new SuccessException("Something went terrible wrong ... :-X");
	}
	
	/**
	 * Alias for ModuleHandler::getModuleInstances()
	 * @see ModuleHandler::getModuleInstances()
	 */
	public function getModuleInstances($moduleName) {
		return ModuleHandler::getInstance()->getModuleInstances($moduleName);
	}
	
	/**
	 * Alias for ModuleHandler::getFirstModuleInstance()
	 * @see ModuleHandler::getFirstModuleInstance()
	 */
	public function getFirstModuleInstance($moduleName) {
		return ModuleHandler::getInstance()->getFirstModuleInstance($moduleName);
	}
}
?>