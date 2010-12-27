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
				channel, modes
			FROM
				chanserv_channels";
		$result = Services::getDB()->sendQuery($sql);
		
		while ($row = Services::getDB()->fetchArray($result)) {
			$this->join($row['channel']);
			$this->setStandardModes($row['channel'], $row['modes']);
		}
	}
	
	public function getAccess($channel, $accountname) {
		$authServ = Services::getModuleManager()->lookupModule('AuthServ');
		$userID =	call_user_func(array($authServ, 'getUserID'), $accountname);
		if (!$userID) return 0;
		$sql = "SELECT
				accessLevel
			FROM
				chanserv_channels_to_users
			WHERE
					channel = '".escapeString($channel)."'
				AND	userID = ".$userID;
		$row = Services::getDB()->getFirstRow($sql);
		if ($row) return $row['accessLevel'];
		return 0;
	}
	
	public function setStandardModes($channel, $modes = null) {
		if ($modes !== null) {
			$sql = "SELECT
					modes
				FROM
					chanserv_channels
				WHERE
					channel = '".escapeString($channel)."'";
			$row = Services::getDB()->getFirstRow($sql);
			$modes = $row['modes'];
		}
		// set the modes
		Services::getConnection()->getProtocol()->sendMode($this->getUuid(), $channel, $modes);
	}
}
?>