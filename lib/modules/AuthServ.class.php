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

	/**
	 * Get authed users for $accountname
	 * @param	string	$accountname
	 */
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
				AND	active = 1";
		$row = Services::getDB()->getFirstRow($sql);

		return $row['count'] > 0;
	}

	/**
	 * Creates a new account
	 * @param	string	$accountname
	 * @param	string	$password
	 * @param	string	$email
	 */
	public function create($accountname, $password, $email) {
		$salt = sha1(uniqid().sha1(microtime()).rand());
		$password = sha1($salt.sha1($salt.$password));
		$sql = "INSERT INTO authserv_users (accountname, password, email, salt, time) VALUES ('".escapeString($accountname)."', '".$password."', '".escapeString($email)."', '".$salt."', ".time().")";
		Services::getDB()->sendQuery($sql);
	}

	/**
	 * Modifies the password of $accountname
	 * @param	string	$accountname
	 * @param	string	$password
	 */
	public function pass($accountname, $password) {
		$sql = "UPDATE
				authserv_users
			SET
				password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, '".escapeString($password)."'))))
			WHERE
				accountname = '".escapeString($accountname)."'";
		Services::getDB()->sendQuery($sql);
	}

	/**
	 * Modifies the email address of $accountname
	 * @param	string	$accountname
	 * @param	string	$email
	 */
	public function email($accountname, $email) {
		$sql = "UPDATE
				authserv_users
			SET
				email = '".escapeString($email)."'
			WHERE
				accountname = '".escapeString($accountname)."'";
		Services::getDB()->sendQuery($sql);
	}

	/**
	 * Returnes true if the given accountname exists
	 * @param	string	$accountname
	 */
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

	/**
	 * Returnes true if the given email address already exists
	 * @param unknown_type $email
	 */
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

	/**
	 * Returnes the userID of the account with name $accountname
	 * @param	string	$accountname
	 */
	public static function getUserID($accountname) {
		$sql = "SELECT
				userID
			FROM
				authserv_users
			WHERE
					accountname = '".escapeString($accountname)."'";
		$row = Services::getDB()->getFirstRow($sql);

		return $row['userID'];
	}

	/**
	 * Returnes the access level of $accountname
	 * @param	string	$accountname
	 */
	public static function getAccessLevel($accountname) {
		$sql = "SELECT
					accessLevel
				FROM
					authserv_users
				WHERE
					accountname = '".escapeString($accountname)."'";
		$row = Services::getDB()->getFirstRow($sql);

		// workaround ...
		if (!Services::getDB()->getNumRows()) return 0;

		return intval($row['accessLevel']);
	}
}
?>