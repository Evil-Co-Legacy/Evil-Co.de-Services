<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Deletes a user
 *
 * @author	Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandUnregister extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'unregister';

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

		if (count($messageEx) == 2) {
			$password = $messageEx[1];
			if (!$this->bot->isAuthed($user->getUuid())) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.notAuthed'));
			}
			
			$accountname = Services::getUserManager()->getUser($user->getUuid())->accountname;
			
			if (!$this->bot->checkCredentials($accountname, $password)) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.auth.invalidCredentials'));
			}
			$userID = $this->bot->getUserID($accountname);
			$this->bot->delete($accountname);
			// TODO: Kill user to log him out
			$sql = "DELETE FROM chanserv_channels_to_users WHERE userID = ".$userID;
			Services::getDB()->sendQuery($sql);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		} else {
			throw new SyntaxErrorException();
		}
	}
}
?>