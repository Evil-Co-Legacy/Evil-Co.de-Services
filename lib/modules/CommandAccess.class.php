<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Sets access-levels with ChanServ
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandAccess extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'access';

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
		if ($access < $this->bot->getNeededAccess($target, $this->originalName)) {
			throw new PermissionDeniedException();
		}
		
		if (count($messageEx) == 1) {
			$sql = "SELECT
					*
				FROM
					chanserv_channel_accessLevel
				WHERE
					channel = '".escapeString($target)."'
				ORDER BY 
					accessLevel DESC";
			$result = Services::getDB()->sendQuery($sql);
			while ($row = Services::getDB()->fetchArray($result)) {
				$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.'.$row['function']).': '.$row['accessLevel']);
			}
		}
		else {
			if (count($messageEx) == 3) {
				if ($access < $messageEx[2]) {
					return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.tooHigh'));
				}
				if (!$this->bot->getNeededAccess($target, $messageEx[1])) {
					return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.unknown'));
				}
				$sql = "UPDATE
						chanserv_channel_accessLevel
					SET
						accessLevel = ".$messageEx[2]."
					WHERE
						function = '".escapeString($messageEx[1])."'";
				Services::getDB()->sendQuery($sql);
			}
			else {
				throw new SyntaxErrorException();
			}
		}
	}
}
?>