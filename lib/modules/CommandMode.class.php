<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Sets modes with ChanServ
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandMode extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'mode';

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
		if ($target{0} != '#') {
			$target = $messageEx[1];
			unset($messageEx[1]);
		}
		$access = $this->bot->getAccess($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		if ($access < $this->neededPermissions) {
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
		}
		
		if (count($messageEx) == 1) {
			$this->setStandardModes($target);
		}
		else {
			unset($messageEx[0];
			Services::getConnection()->getProtocol()->sendMode($this->bot->getUuid(), $target, implode(' ', $messageEx));
		}
	}
}
?>