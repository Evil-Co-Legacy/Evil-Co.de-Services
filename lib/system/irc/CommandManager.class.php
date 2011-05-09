<?php

/**
 * Manages commands
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandManager {
	
	/**
	 * Contains a list of registered bots
	 * @var array<ModuleInstance>
	 */
	protected $registeredBots = array();
	
	/**
	 * Contains a list of registered commands
	 * @var array<ModuleInstance>
	 */
	protected $registeredCommand = array();
	
	/**
	 * Contains a list of bound commands
	 * @var arra<array<ModuleInstance>>
	 */
	protected $boundCommands = array();
	
	/**
	 * Registeres a bot
	 * @param		ModuleInstance		$instance
	 */
	public function registerBot(ModuleInstance $instance) {
		$this->registeredBots[$instance->getModuleInformation()->moduleName] = $instance;
	}
	
	/**
	 * Registeres a command
	 * @param ModuleInstance $instance
	 */
	public function registerCommand(ModuleInstance $instance) {
		$this->registeredCommand[$instance->getModuleInformation()->moduleName] = $instance;
	}
}
?>