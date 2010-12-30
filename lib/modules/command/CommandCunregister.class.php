<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Unregisters the channel
 *
 * @author	Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandCunregister extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'cunregister';

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);
		$this->checkTarget($target, $messageEx);
		
		if (!$this->bot->isRegistered($target)) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.notRegistered'));
		}
		$access = $this->bot->getAccess($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		
		if ($access < 500) {
			throw new PermissionDeniedException();
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
			$this->bot->unregister($target);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success', $target));
		}
		else {
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.code', $code));
		}
	}
}
?>