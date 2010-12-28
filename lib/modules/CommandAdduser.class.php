<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Sets access-levels with ChanServ
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandAdduser extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'adduser';

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);
		if ($target{0} != '#') {
			$target = $messageEx[1];
			unset($messageEx[1]);
			$messageEx = array_values($messageEx);
		}

		$access = $this->bot->getAccess($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		if ($access < $this->bot->getNeededAccess($target, 'access')) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
		}

		if (count($messageEx) == 3) {
			if ($messageEx[2] > $access) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.tooHigh'));
			}
			else if ($messageEx[2] > 500) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.tooHigh'));
			}
			$authServ = Services::getModuleManager()->lookupModule('AuthServ');
			$userID = call_user_func(array($authServ, 'getUserID'), $messageEx[1]);
			if (!$userID) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.invalidUser'));
			}
			$sql = "INSERT INTO chanserv_channels_to_users (channel, userID, accessLevel)
				VALUES ('".escapeString($target)."', ".$userID.", ".intval($messageEx[2]).")
				ON DUPLICATE KEY UPDATE accessLevel = VALUES(accessLevel)";
			Services::getDB()->sendQuery($sql);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		}
		else {
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>