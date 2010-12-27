<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the OpServ bot
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
}
?>