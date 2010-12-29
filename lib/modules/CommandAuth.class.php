<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Auths the user
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandAuth extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'auth';

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

		if (count($messageEx) == 3) {
			$accountname = $messageEx[1];
			$password = $messageEx[2];
			if ($this->bot->isAuthed($user->getUuid())) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.alreadyAuthed'));
			}
			if (!$this->bot->checkCredentials($accountname, $password)) {
				return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.invalidCredentials'));
			}

			$users = $this->bot->getUsers($accountname);
			foreach ($users as $uuid) {
				$this->bot->sendMessage($uuid, Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.login'));
			}

			$this->bot->setAccount($user->getUuid(), $accountname);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success', $accountname));
		} else {
			// send syntax hint
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>