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
		// add to manager
		$this->registeredCommand[$instance->getModuleInformation()->moduleName] = $instance;
		
		// add bindings
		$sql = "SELECT
				*
			FROM
				command_bind
			WHERE
				moduleName = ?";
		$result = Services::getDB()->fetchAll($sql, $instance->getModuleInformation()->moduleName);
		
		foreach($result as $item) {
			$this->boundCommands[strtolower($item->botNickname).'.'.strtolower($item->commandName)] = $instance;
		}
	}
	
	/**
	 * Parses a command and redirects to correct command
	 * @param		string		$source
	 * @param		string		$target
	 * @param		string		$line
	 */
	public function parseCommand($source, $target, $line) {
		// split line
		$lineEx = explode(' ', $line);
		
		// get correct data
		$nickname = strtolower(Services::getBotManager()->getUser($target));
		$command = strtolower($lineEx[0]);
		$identifier = $nickname.'.'.$command;
		
		// find command and execute
		if (isset($this->boundCommands[$identifier])) $this->boundCommands[$identifier]->execute(Services::getUserManager()->getUser($source), Services::getBotManager()->getUser($target), $line, $lineEx);
	}
}
?>