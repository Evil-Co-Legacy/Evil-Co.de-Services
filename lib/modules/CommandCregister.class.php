<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Registers the user
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandCregister extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'cregister';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 0;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);
		if ($target{0} != '#') {
			$target = $messageEx[1];
			unset($messageEx[1]);
		}
		
		$this->bot->register($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
	}
}
?>