<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Unregisters the channel
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandCunregister extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'curegister';

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
			$messageEx = array_values($messageEx);
		}
		if (!$this->bot->isRegistered($target)) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.notRegistered'));
		}
		$access = $this->bot->getAccess($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		if ($access < 500) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
		}
		$sql = "SELECT
				unregistercode
			FROM
				chanserv_channels
			WHERE
				channel = '".escapeString($target)."'";
		$row = Services::getDB()->getFirstRow($sql);
		$code = $row['unregistercode'];
		if (isset($messageEx[1])) {
			if ($code != trim($messageEx[1])) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.codeWrong', $target));
			}
			// TODO: Validate unregistercode
			$this->bot->unregister($target);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success', $target));
		}
		else {
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.code', $code));
		}
	}
}
?>