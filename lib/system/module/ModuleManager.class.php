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
	 * Contains all modules
	 * @var	array<Module>
	 */
	protected $moduleList = array();
	
	/**
	 * Contains all runtime files
	 * @var array<string>
	 */
	protected $runtimeFiles = array();
	
	/**
	 * Creates a new instance of ModuleManager
	 */
	public function __construct() {
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
		
		// read module file
		$moduleFile = file_get_contents($file);
		
		// get class name
		$className = basename($file, '.class.php');
		
		// create runtime name
		$runtimeName = sha1($className.time());
		
		// modify
		$moduleFile = preg_replace('~^class (.*)(| implements (.*)) extends (.*)~', 'class '.$runtimeName.' $2 $4');
		
		// register runtime file
		$this->registerRuntimeFile($runtimeName.'.php');
		
		// create file
		$this->createRuntimeFile($runtimeName.'.php', $moduleFile);
		
		// include runtime file
		require_once($this->getRuntimeFilePath($runtimeName.'.php'));
		
		// call special module methods
		switch(get_parent_class($this->moduleList[$index])) {
			case 'Bot':
				call_user_func(array($runtimeName, 'registerBot'));
				break;
			case 'Command':
				call_user_func(array($runtimeName, 'registerCommand'));
				break;
		}
	}
	
	/**
	 * Registeres a new runtime file
	 * @param	string	$filename
	 */
	public function registerRuntimeFile($filename) {
		$this->runtimeFiles[] = $filename;
	}
	
	/**
	 * Creates a new runtime file
	 * @param	string	$filename
	 * @param	string	$content
	 */
	public function createRuntimeFile($filename, $content) {
		file_put_contents(SDIR.'runtime/'.$filename, $content);
	}
	
	/**
	 * Returnes the absolute path to runtime file
	 * @param	string	$filename
	 */
	public function getRuntimeFilePath($filename) {
		return SDIR.'runtime/'.$filename;
	}
}
?>