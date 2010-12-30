<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Registers the user
 *
 * @author	Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandEmail extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'email';

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);

		if (count($messageEx) == 2) {
			$email = $messageEx[1];
			
			if (!$this->bot->isAuthed($user->getUuid())) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.notAuthed'));
			}
			$this->bot->email(Services::getUserManager()->getUser($user->getUuid())->accountname, $email);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		} else {
			throw new SyntaxErrorException();
		}
	}
}
?>