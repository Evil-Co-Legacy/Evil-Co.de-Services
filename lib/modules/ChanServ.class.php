<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the ChanServ bot
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class ChanServ extends BotModule {
	
	public function __construct($bot, $trigger = '') {
		parent::__construct($bot, $trigger);
		
		$sql = "SELECT
				*
			FROM
				chanserv_channels";
		$result = Services::getDB()->sendQuery($sql);
		
		while ($row = Services::getDB()->fetchArray($result)) {
			$this->join($row['channel']);
		}
	}
	
	public function getAccess($channel, $accountname) {
		$userID = AuthServ::getUserID($accountname);
		$sql = "SELECT
				*
			FROM
				chanserv_channels_to_users
			WHERE
					channel = '".escapeString($channel)."'
				AND	userID = ".$userID;
		$row = Services::getDB()->getFirstRow($sql);
		if ($row) return $row['accessLevel'];
		return 0;
	}
}
?>