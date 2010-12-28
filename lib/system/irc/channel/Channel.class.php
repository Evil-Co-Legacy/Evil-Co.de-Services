<?php
/**
 * Represents a channel
 *
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Channel {

	/**
	 * Contains channel's data
	 *
	 * @var	array<mixed>
	 */
	protected $data = array();

	/**
	 * Contains a list of all users
	 * This is a little workaround
	 *
	 * @var	array<UserType>
	 */
	protected $userList = array();

	/**
	 * Creates a new instance of type Channel
	 *
	 * @param	string			$name
	 * @param	integer			$timestamp
	 * @param	ModeList		$modes
	 * @param	array<UserType>		$userList
	 */
	public function __construct($name, $timestamp, $modes, $userList) {
		$this->name = $name;
		$this->timestamp = $timestamp;
		$this->modes = $modes;
		$this->userList = $userList;
	}

	/**
	 * Returnes the name of this channel
	 *
	 * @return	string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returnes the timestamp of this channel
	 *
	 * @return	integer
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}

	/**
	 * Returnes the modes of this channel
	 *
	 * @return	ModeList
	 */
	public function getModes() {
		return $this->modes;
	}
	
	/**
	 * Returns modes such as o, q or a for given UUID
	 * Note: The string can be empty
	 *
	 * @param	string	$uuid
	 * @return	string
	 */
	public function getUserModes($uuid) {
		foreach($this->userList as $key => $user) {
			if ($user['user']->getUuid() == $uuid) return $user['modes'];
		}
		return null;
	}

	/**
	 * Returns the current userlist
	 *
	 * @return	array<UserType>
	 */
	public function getUserList() {
		return $this->userList;
	}

	/**
	 * Adds a user to channel
	 *
	 * @param	array<UserType>		$userList
	 * @return	void
	 */
	public function join($userList) {
		$this->userList = array_merge($this->userList, $userList);
	}

	/**
	 * Removes a user from database
	 *
	 * @param	string	$uuid
	 * @return	void
	 */
	public function part($uuid) {
		foreach($this->userList as $key => $user) {
			if ($this->userList[$key]['user']->getUuid() == $uuid) unset($this->userList[$key]);
		}
	}

	/**
	 * Returnes whether the $uuid joined this channel
	 *
	 * @param	string	$uuid
	 * @return	boolean
	 */
	public function isJoined($uuid) {
		foreach($this->userList as $key => $user) {
			if ($this->userList[$key]['user']->getUuid() == $uuid) return true;
		}
		return false;
	}

	/**
	 * Removes invalid entries from userlist
	 *
	 * @deprecated
	 */
	protected function cleanUserlist() {
		foreach($this->userList as $key => $user) {
			if (!($user['user'] instanceof UserType)) unset($this->userList[$key]);
		}
	}

	/**
	 * Magic method to support channel metadata
	 *
	 * @param	string	$variable
	 * @param	mixed	$value
	 * @return	void
	 */
	public function __set($variable, $value) {
		$this->data[$variable] = $value;
	}

	/**
	 * Magic method to support channel metadata
	 *
	 * @param 	string	$variable
	 * @return	mixed
	 */
	public function __get($variable) {
		if (isset($this->data[$variable])) {
			if (is_array($this->data[$variable]))
				return (array) $this->data[$variable];
			else
				return $this->data[$variable];
		}
		return null;
	}
}
?>