<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Registers the user
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandCregister extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'cregister';

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);
		$this->checkTarget($target, $messageEx);
		
		if ($this->bot->isRegistered($target)) {
			return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.alreadyRegistered'));
		}
		
		$users = Services::getChannelManager()->getChannel($target)->getUserList();
		foreach ($users as $channelUser) {
			if ($channelUser['user']->getUuid() == $user->getUuid()) {
				if (stripos($channelUser['mode'], 'o')) {
					$this->bot->register($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
					Services::getConnection()->getProtocol()->sendMode($this->bot->getUuid(), $target, '+q '.$user->getNick());
					return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success', $target));
				}
			}
		}
		return $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.noOp'));
	}
}
?>