<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the AuthServ bot
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class AuthServ extends BotModule {

	/**
	 * Returnes true if a user is authed
	 * @param	string	$uuid
	 */
	public function isAuthed($uuid) {
		return (Services::getUserManager()->getUser($uuid)->accountname !== null);
	}
	
	/**
	 * Sets the accountname for the given uuid
	 * @param	string	$uuid
	 * @param	string	$account
	 */
	public function setAccount($uuid, $account) {
		Services::getUserManager()->getUser($uuid)->accountname = $account;
	}
}
?>