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
	 * Contains all running bots
	 * @var	array<array<BotModule>>
	 */
	protected $runningBots = array();
	
	/**
	 * Creates a new instance of ModuleManager
	 */
	public function init() {
		// check dependencies
		if (!extension_loaded('runkit')) 
			throw new ModuleException("Required runtime libary not found!");
		elseif (defined('DEBUG')) 
			Services::getConnection()->getProtocol()->sendLogLine("Runtime libary is ready!");
		
		// load modules from database
		$sql = "SELECT
					*
				FROM
					module";
		$result = Services::getDB()->sendQuery($sql);
		
		while($row = Services::getDB()->fetchArray($result)) {
			$this->loadModule(SDIR.'lib/modules/'.$row['name'].'.class.php', $row['address'], true);
		}
		
		$sql = "SELECT
					*
				FROM
					module_instance_bot";
		$result = Services::getDB()->sendQuery($sql);
		
		while($row = Services::getDB()->fetchArray($result)) {
			$this->createBotInstance($row['moduleName'], $row['trigger'], $row['nick'], $row['hostname'], $row['ident'], $row['ip'], $row['modes'], $row['gecos']);
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
		if (!$moduleAddress === null) $moduleAddress = dechex(time() + count($this->availableModules));
		
		// validate module
		if (isset($this->availableModules[$moduleName])) throw new ModuleException("Module '".$moduleName."' is already loaded!");
		if (in_array($tempName, $this->availableModules)) throw new ModuleException("What the hell?! A module with address 0x".$moduleAddress." is already loaded! This should never happen!!!");
		
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
	}
	
	/**
	 * Creates a new bot instance
	 * @param	string	$moduleName
	 * @param	string	$trigger
	 * @param	string	$nick
	 * @param	string	$hostname
	 * @param	string	$ident
	 * @param	string	$ip
	 * @param	string	$modes
	 * @param	string	$gecos
	 */
	public function createBotInstance($moduleName, $trigger, $nick, $hostname, $ident, $ip, $modes, $gecos) {
		// validate
		if (!isset($this->availableModules[$moduleName])) throw new ModuleException("Module '".$moduleName." isn't loaded!");
		if ($this->availableModules[$moduleName][$type] != 'Bot') throw new ModuleException("You can only create instances of bot modules!");
		
		// create bot user
		$user = Services::getConnection()->getProtocol()->createBot($nick, $hostname, $ident, $ip, $modes, $gecos);
		
		// create instance of BotModule
		$this->availableModules[$moduleName]['sandbox']->bot = $user;
		$this->availableModules[$moduleName]['sandbox']->trigger = $trigger;
		$this->availableModules[$moduleName]['sandbox']->eval('$instance'.$user->getUuid().' = new '.$moduleName.'($bot, $trigger);');
		$instanceName = 'instance'.$user->getUuid();
		
		// add to running bots list
		$this->runningBots[$moduleName][] = &$$this->availableModules[$moduleName]['sandbox']->{$instanceName};
	}
}
?>