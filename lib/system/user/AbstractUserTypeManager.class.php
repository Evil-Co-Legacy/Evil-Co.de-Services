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
		else
			Services::getMemcache()->add('UserType_'.get_class($this).'_'.$this->userType.'_'.$uuid, (new $this->userType($uuid, $timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, new $this->modeList($modes), $gecos)));
			
		// return uuid
		return $uuid;
	}

	/**
	 * @see	UserTypeManager::removeUser()
	 */
	public function removeUser($uuid) {
		if (!Services::memcacheLoaded()) {
			foreach($this->userList as $key => $user) {
				if ($this->userList[$key]->getUuid() == $uuid) unset($this->userList[$key]);
			}
		} else
			Services::getMemcache()->delete('UserType_'.get_class($this).'_'.$this->userType.'_'.$uuid);
	}
	
	/**
	 * @see	UserTypeManager::getUser()
	 */
	public function getUser($uuid) {
		if (!Services::memcacheLoaded()) {
			foreach($this->userList as $key => $user) {
				if ($this->userList[$key]->getUuid() == $uuid) {
					return $this->userList[$key];
				}
			}
		} elseif(Services::getMemcache()->get('UserType_'.get_class($this).'_'.$this->userType.'_'.$uuid))
			return Services::getMemcache()->get('UserType_'.get_class($this).'_'.$this->userType.'_'.$uuid);
		
		
		return null;
	}
}
?>