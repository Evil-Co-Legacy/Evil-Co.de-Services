<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Lists users with access
 *
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandListuser extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'listuser';

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		// split message
		$messageEx = explode(' ', $message);
		$this->checkTarget($target, $messageEx);

		$access = $this->bot->getAccess($target, Services::getUserManager()->getUser($user->getUuid())->accountname);
		if ($access < $this->bot->getNeededAccess($target, 'access')) {
			throw new PermissionDeniedException();
		}

		if (count($messageEx) == 1) {
			$sql = "SELECT
					c.*,
					a.accountname
				FROM
					chanserv_channels_to_users c
					LEFT JOIN
						authserv_users a
						ON c.userID = a.userID
				WHERE
					channel = '".escapeString($target)."'
				ORDER BY 
					c.accessLevel DESC";
			$result = Services::getDB()->sendQuery($sql);
			while ($row = Services::getDB()->fetchArray($result)) {
				$this->bot->sendMessage($user->getUuid(), $row['accountname'].': '.$row['accessLevel']);
			}
		}
		else {
			throw new SyntaxErrorException();
		}
	}
}
?>