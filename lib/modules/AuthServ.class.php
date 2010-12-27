<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the AuthServ bot
 * @author		Tim D�sterhus
 * @copyright	2010 DEVel Fusion
 */
class AuthServ extends BotModule {
	
	public $authedUsers = array();
	public function isAuthed($uuid) {
		return isset($this->authedUsers[$uuid]);
	}
}
?>