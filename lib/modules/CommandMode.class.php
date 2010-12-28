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
		if ($access < $this->bot->getNeededAccess($target, $this->originalName)) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
		}
		
		if (count($messageEx) == 1) {
			$this->bot->setStandardModes($target);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		}
		else {
			unset($messageEx[0]);
			$modeString = implode(' ', $messageEx);
			Services::getConnection()->getProtocol()->sendMode($this->bot->getUuid(), $target, str_replace(array('q', 'r', 'a', 'v', 'o', 'b', 'h', 'A', 'O', 'e', 'P'), '', $modeString));
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		}
	}
}
?>