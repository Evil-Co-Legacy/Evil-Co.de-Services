<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the AuthServ bot
 * @author		Tim D�sterhus
 * @copyright	2010 DEVel Fusion
 */
class AuthServ extends BotModule {

	/**
	 * Returnes true if a user is authed
	 * @param	string	$uuid
	 */
	public function isAuthed($uuid) {
		return (Services::getUserManager()->getUser($uuid)->accountname !== null ? true : false);
	}
}
?>