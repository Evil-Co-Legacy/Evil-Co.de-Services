<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Loads modules via IRC
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandPart extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'part';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 700;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);

		if (count($messageEx) >= 2) {
			// get channel name
			$channel = $messageEx[1];
			// avoid empty strings
			if (empty($channel)) $this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
			// add the #
			if ($channel{0} != '#') $channel = '#'.$channel;
			
			unset($messageEx[0]);
			unset($messageEx[1]);
			$message = implode(' ', $messageEx);
			
			$this->bot->part($channel, $message);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success'));
		} else {
			// send syntax hint
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>