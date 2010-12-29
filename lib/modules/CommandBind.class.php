<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Binds a command
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class CommandBind extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'bind';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 800;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);

		// BIND <bot> <module> <command> <appearInHelp>
		if (count($messageEx) >= 3) {
			$botAddress = Services::getModuleManager()->lookupModule($messageEx[1]);
			$moduleAddress = Services::getModuleManager()->lookupModule($messageEx[2]);
			$commandName = $messageEx[3];
			$appearInHelp = (boolean) (isset($messageEx[4]) ? $messageEx[4] : true);

			Services::getModuleManager()->bindCommand($botAddress, $moduleAddress, $commandName, $appearInHelp);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		} else {
			// send syntax hint
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>