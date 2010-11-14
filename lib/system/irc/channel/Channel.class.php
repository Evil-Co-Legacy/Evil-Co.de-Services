<?php

/**
 * Represents a channel
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Channel {
	
	/**
	 * Contains channel's data
	 * @var	array<mixed>
	 */
	protected $data = array();
	
	/**
	 * Creates a new instance of type Channel
	 * @param	string			$name
	 * @param	integer			$timestamp
	 * @param	ModeList		$modes
	 * @param	array<UserType>	$userList
	 */
	public function __construct($name, $timestamp, $modes, $userList) {
		$this->name = $name;
		$this->timestamp = $timestamp;
		$this->modes = $modes;
		$this->userList = $userList;
	}
	
	/**
	 * Returnes the name of this channel
	 * @return	string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returnes the timestamp of this channel
	 * @return	integer
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	/**
	 * Returnes the modes of this channel
	 * @return	ModeList
	 */
	public function getModes() {
		return $this->modes;
	}
	
	/**
	 * Returnes the current userlist
	 * @return	array<UserType>
	 */
	public function getUserList() {
		$userList = &$this->userList;
		return $userList;
	}
	
	/**
	 * Adds a user to channel
	 * @param	UserType	$user
	 */
	public function join(&$user) {
		$this->userList[] = $user;
	}
	
	/**
	 * Removes a user from database
	 * @param	string	$uuid
	 */
	public function part($uuid) {
		foreach($this->userList as $key => $user) {
			if ($this->userList->getUuid() == $uuid) unset($this->userList[$key]);
		}
	}
	
	/**
	 * Magic method to support channel metadata
	 * @param	string	$variable
	 * @param	mixed	$value
	 */
	public function __set($variable, $value) {
		$this->data[$variable] = $value;
	}
	
	/**
	 * Magic method to support channel metadata
	 * @param unknown_type $variable
	 */
	public function __get($variable) {
		if (isset($this->data[$variable])) return $this->data[$variable];
		return null;
	}
}
?>