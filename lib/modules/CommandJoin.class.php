<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Loads modules via IRC
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandJoin extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'join';

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
			
			$this->bot->join($channel);
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.success', $channel));
		} else {
			// send syntax hint
			$this->bot->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'command.'.$this->originalName.'.syntaxHint'));
		}
	}
}
?>