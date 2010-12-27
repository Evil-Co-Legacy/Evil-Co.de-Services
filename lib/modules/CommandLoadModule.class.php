<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Loads modules via IRC
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class CommandLoadModule extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'loadModule';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 700;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);

		if (count($messageEx) == 2 and !empty($messageEx[1])) {
			// get module name
			$moduleName = $messageEx[1];

			try {
				// try to load module
				Services::getModuleManager()->loadModule(SDIR.'lib/modules/'.ucfirst($moduleName).'.class.php');
			} catch (Exception $ex) {
				$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.cannotLoad', $ex->getMessage()));
			}
		} else {
			// send syntax hint
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>