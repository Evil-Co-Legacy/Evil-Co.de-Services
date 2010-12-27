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
	 * @param	string	$password
	 */
	public function checkCredentials($accountname, $password) {
		$sql = "SELECT 
				count(*) as count
			FROM
				authserv_users
			WHERE
					accountname = '".escapeString($accountname)."'
				AND	password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, '".escapeString($password)."'))))
				AND active = 1";
		$row = Services::getDB()->getFirstRow($sql);
		
		return $row['count'] > 0;
	}
	
	public function create($accountname, $password, $email) {
		$salt = sha1(uniqid().sha1(microtime()).rand());
		$password = sha1($salt.sha1($salt.$password));
		$sql = "INSERT INTO authserv_users (accountname, password, email, salt) VALUES ('".escapeString($accountname)."', '".$password."', '".escapeString($email)."', '".$salt."')";
		WCF::getDB()->sendQuery($sql);
	}
	
	public function accountExists($accountname) {
		$sql = "SELECT 
				count(*) as count
			FROM
				authserv_users
			WHERE
					accountname = '".escapeString($accountname)."'";
		$row = Services::getDB()->getFirstRow($sql);
		
		return $row['count'] > 0;
	}
	
	public function emailExists($email) {
		$sql = "SELECT 
				count(*) as count
			FROM
				authserv_users
			WHERE
					email = '".escapeString($email)."'";
		$row = Services::getDB()->getFirstRow($sql);
		
		return $row['count'] > 0;
	}
}
?>