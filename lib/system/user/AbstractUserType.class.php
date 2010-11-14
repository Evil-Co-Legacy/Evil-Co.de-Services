<?php
// imports
require_once(SDIR.'lib/system/user/UserType.class.php');

/**
 * Defines default methods for UserTypes
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class AbstractUserType implements UserType {
	
	/**
	 * Contains dynamic (magic) variables
	 * @var	array<mixed>
	 */
	protected $data = array();
	
	/**
	 * @see	UserType::__construct()
	 */
	public function __construct($uuid, $timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, $modes, $gecos) {
		$this->uuid = $uuid;
		$this->timestamp = $timestamp;
		$this->nick = $nick;
		$this->hostname = $hostname;
		$this->displayedHostname = $displayedHostname;
		$this->ident = $ident;
		$this->ip = $ip;
		$this->signonTimestamp = $signOnTimestamp;
		$this->modes = $modes;
		$this->gecos = $gecos;
	}
	
	/**
	 * @see	UserType::getUuid()
	 */
	public function getUuid() {
		return $this->uuid;
	}
	
	/**
	 * @see	UserType::getTimestamp()
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	/**
	 * @see	UserType::getNick()
	 */
	public function getNick() {
		return $this->nick;
	}
	
	/**
	 * @see	UserType::getHostname()
	 */
	public function getHostname() {
		return $this->hostname;
	}
	
	/**
	 * @see	UserType::getDisplayedHostname()
	 */
	public function getDisplayedHostname() {
		return $this->displayedHostname;
	}
	
	/**
	 * @see	UserType::getIdent()
	 */
	public function getIdent() {
		return $this->ident;
	}
	
	/**
	 * @see	UserType::getIP()
	 */
	public function getIP() {
		return $this->ip;
	}
	
	/**
	 * @see	UserType::getSignonTimestamp()
	 */
	public function getSignonTimestamp() {
		return $this->signonTimestamp;
	}
	
	/**
	 * @see	UserType::getModes()
	 */
	public function getModes() {
		return $this->modes;
	}
	
	/**
	 * @see	UserType::getGecos()
	 */
	public function getGecos() {
		return $this->gecos;
	}
	
	/**
	 * @see	UserType::__set()
	 */
	public function __set($variable, $value) {
		$this->data[$variable] = $value;
	}
	
	/**
	 * @see	UserType::__get()
	 */
	public function __get($variable) {
		if (isset($this->data[$variable])) return $this->data[$variable];
		return null;
	}
}
?>