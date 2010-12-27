<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the AuthServ bot
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class AuthServ extends BotModule {

	protected $accountToUser = array();

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
	 * @param	string	$accountname
	 */
	public function setAccount($uuid, $accountname) {
		Services::getUserManager()->getUser($uuid)->accountname = $accountname;
		
		if (isset($this->accountToUser[$accountname])) $this->accountToUser[$accountname][] = $uuid;
		else $this->accountToUser[$accountname] = array($uuid);
	}
	
	public function getUsers($accountname) {
		if (isset($this->accountToUser[$accountname])) return $this->accountToUser[$accountname];
		return array();
	}
	
	/**
	 * Checks the credentials
	 * @param	string	$accountname
	 * @param	strign	$password
	 */
	public function checkCredentials($accountname, $password) {
		$sql = "SELECT 
				count(*) as count
			FROM
				authserv_users
			WHERE
					accountname = '".escapeString($accountname)."'
				AND	password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, '".escapeString($password)."'))))";
		$row = Services::getDB()->getFirstRow($sql);
		
		return $row['count'] > 0;
	}
}
?>