<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Sets channel info
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class CommandCinfo extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'cinfo';

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
		
		if (count($messageEx) == 1) {
			$sql = "SELECT
					*
				FROM
					chanserv_channels c
					LEFT JOIN
						authserv_users a
						ON
							c.registrar = a.userID
				WHERE
					channel = '".escapeString($target)."'";
			$row = Services::getDB()->getFirstRow($sql);
			$this->bot->sendMessage($user->getUuid(), 'Time: '.date('d.m.Y H:i:s', $row['time']));
			$this->bot->sendMessage($user->getUuid(), 'Registrar: '.$row['accountname']);
		}
		else {
			throw new SyntaxErrorException();
		}
	}
}
?>