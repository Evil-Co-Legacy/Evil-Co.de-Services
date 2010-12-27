<?php
// imports
require_once(SDIR.'lib/system/user/UserTypeManager.class.php');
require_once(SDIR.'lib/system/irc/'.IRCD.'/UUID.class.php');
require_once(SDIR.'lib/system/irc/UserModeList.class.php');

/**
 * Defines default methods for UserTypeManagers
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class AbstractUserTypeManager implements UserTypeManager {

	/**
	 * Contains all users
	 * @var array<UserType>
	 */
	protected $userList = array();

	/**
	 * Contains the class name of the UserType that should used
	 * @var	string
	 */
	protected $userType = 'UserType';

	/**
	 * Contains the class name of the ModeList that should used
	 * @var	string
	 */
	protected $modeList = 'UserModeList';

	/**
	 * @see	UserTypeManager::introduceUser()
	 */
	public function introduceUser($timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, $modes, $gecos, $uuid = '') {
		if (empty($uuid)) {
			// get uuid manager instance
			$uuid = UUID::getInstance();

			// generate new uuid
			$uuid = $uuid->generate();
		}

		// get ID
		$ID = count($this->userList);

		// create new user
		if (!Services::memcacheLoaded())
			$this->userList[] = new $this->userType($uuid, $timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, new $this->modeList($modes), $gecos);
		else {
			$userList = Services::getMemcache()->get(get_class($this).'_data');
			$userList[] = new $this->userType($uuid, $timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, new $this->modeList($modes), $gecos);
			Services::getMemcache()->add(get_class($this).'_data', $userList);
		}

		// return uuid
		return $uuid;
	}

	/**
	 * @see	UserTypeManager::removeUser()
	 */
	public function removeUser($uuid) {
		// try to load memcache
		if (Services::memcacheLoaded() and Services::getMemcache()->get(get_class($this).'_data') !== false)
			$userList = Services::getMemcache()->get(get_class($this).'_data');
		else
			$userList = $this->userList;

		// remove user
		foreach($userList as $key => $user) {
			if ($userList[$key]->getUuid() == $uuid) unset($userList[$key]);
		}

		// save
		if (Services::memcacheLoaded())
			Services::getMemcache()->add(get_class($this).'_data', $userList);
		else
			$this->userList = $userList;
	}

	/**
	 * @see	UserTypeManager::getUser()
	 */
	public function getUser($uuid) {
		// try to load from memcache
		if (Services::memcacheLoaded() and Services::getMemcache()->get(get_class($this).'_data' !== false))
			$userList = Services::getMemcache()->get(get_class($this).'_data');
		else
			$userList = $this->userList;

		foreach($userList as $key => $user) {
			if ($userList[$key]->getUuid() == $uuid) {
				return $userList[$key];
			}
		}

		return null;
	}

	/**
	 * @see	UserTypeManager::getUserByNick()
	 */
	public function getUserByNick($nickname) {
		if (Services::memcacheLoaded() and Services::getMemcache()->get(get_class($this).'_data' !== false))
			$userList = Services::getMemcache()->get(get_class($this).'_data');
		else
			$userList = $this->userList;

		foreach($userList as $key => $user) {
			if ($userList[$key]->getNick() == $nickname) return $userList[$key];
		}

		return null;
	}
}
?>