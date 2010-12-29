<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Kicks with ChanServ
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandKick extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'kick';

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
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
		}

		if (count($messageEx) == 2) {
			// check target access
			if ($access < $this->bot->getAccess($target, Services::getUserManager()->getUserByNick($messageEx[1])->accountname))
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
			else
				Services::getConnection()->getProtocol()->sendKick($this->bot->getUuid(), $target, $messageEx[1], $user->getNick());
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		}
		else {
			unset($messageEx[0]);
			$username = $messageEx[1];
			unset($messageEx[1]);
			// check target access
			if ($access < $this->bot->getAccess($target, Services::getUserManager()->getUserByNick($username)->accountname))
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.permissionDenied'));
			else
				Services::getConnection()->getProtocol()->sendKick($this->bot->getUuid(), $target, $username, $user->getNick().': '.implode(' ', $messageEx));
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		}
	}
}
?>
