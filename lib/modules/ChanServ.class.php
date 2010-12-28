<?php
require_once(SDIR.'lib/modules/BotModule.class.php');

/**
 * Implements the ChanServ bot
 *
 * @author		Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 */
class ChanServ extends BotModule {
	
	
	public function __construct($bot, $trigger = '') {
		parent::__construct($bot, $trigger);

		// join all registered channels
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

	/**
	 * Returns the access of the given account in the given channel
	 *
	 * @param	string	$channel
	 * @param	string	$accountname
	 * @return	integer
	 */
	public function getAccess($channel, $accountname) {
		$authServ = Services::getModuleManager()->lookupModule('AuthServ');
		$userID = call_user_func(array($authServ, 'getUserID'), $accountname);
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

	/**
	 * Returns the needed access for the given function in the given channel
	 *
	 * @param	string	$channel
	 * @param	string	$function
	 * @return	mixed
	 */
	public function getNeededAccess($channel, $function) {
		$sql = "SELECT
				accessLevel
			FROM
				chanserv_channel_accessLevel
			WHERE
					channel = '".escapeString($channel)."'
				AND	function = '".escapeString($function)."'";
		$row = Services::getDB()->getFirstRow($sql);
		if (!$row) return false;
		return $row['accessLevel'];
	}
	
	/**
	 * Sets the default modes in the given channel
	 *
	 * @param	string	$channel
	 * @param	string	$modes
	 * @return	void
	 */
	public function setStandardModes($channel, $modes = null) {
		if ($modes === null) {
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

	/**
	 * Unregisters the channel
	 *
	 * @param	string	$channel
	 * @return	void
	 */
	public function unregister($channel) {
		// remove +r
		Services::getConnection()->getProtocol()->sendMode($this->getUuid(), $channel, '-r');
		
		$sql = "DELETE FROM
				chanserv_channels
			WHERE
				channel = '".escapeString($channel)."'";
		Services::getDB()->sendQuery($sql);

		$sql = "DELETE FROM
				chanserv_channels_to_users
			WHERE
				channel = '".escapeString($channel)."'";
		Services::getDB()->sendQuery($sql);

		$sql = "DELETE FROM
				chanserv_channel_accessLevel
			WHERE
				channel = '".escapeString($channel)."'";
		Services::getDB()->sendQuery($sql);
		
		// part the channel
		$this->part($channel, 'Unregistered');
	}
	
	/**
	 * Registers the channel with chanserv
	 *
	 * @param	string	$channel
	 * @param	string	$accountname
	 * @return	void
	 */
	public function register($channel, $accountname) {
		$authServ = Services::getModuleManager()->lookupModule('AuthServ');
		$userID = call_user_func(array($authServ, 'getUserID'), $accountname);
		
		$sql = "INSERT INTO chanserv_channels (channel, modes, time, registrar, unregistercode) VALUES ('".escapeString($channel)."', '+tnr', ".time().", ".$userID.", '".StringUtil::getRandomID()."')";
		Services::getDB()->sendQuery($sql);

		$sql = "INSERT INTO chanserv_channels_to_users (channel, userID, accessLevel) VALUES ('".escapeString($channel)."', ".$userID.", 500)";
		Services::getDB()->sendQuery($sql);

		$values = '';
		$sql = "SELECT
				*
			FROM
				chanserv_default_accessLevel";
		$result = Services::getDB()->sendQuery($sql);
		while ($row = Services::getDB()->fetchArray($result)) {
			if ($values != '') $values .= ',';
			$values .= "('".escapeString($channel)."', '".$row['function']."', ".$row['accessLevel'].")";
		}

		$sql = "INSERT INTO chanserv_channel_accessLevel (channel, function, accessLevel) VALUES ".$values;
		Services::getDB()->sendQuery($sql);
		
		// join the channel
		$this->join($channel);
		// set modes
		$this->setStandardModes($channel, '+tnr');
	}
	
	/**
	 * Checks whether the given channel is registered
	 *
	 * @param	string	$channel
	 * @return	boolean
	 */
	public function isRegistered($channel) {
		$sql = "SELECT
				count(*) as count
			FROM
				chanserv_channels
			WHERE
				channel = '".escapeString($channel)."'";
		$row = Services::getDB()->getFirstRow($sql);
		
		return $row['count'] > 0;
	}
}
?>