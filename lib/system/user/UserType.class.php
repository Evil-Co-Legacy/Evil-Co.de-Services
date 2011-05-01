<?php

/**
 * Defines default methods for user types
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
interface UserType {

	/**
	 * Creates a new instance of type UserType
	 * @param	string		$uuid
	 * @param	integer		$timestamp
	 * @param	string		$nick
	 * @param	string		$hostname
	 * @param	string		$displayedHostname
	 * @param	string		$ident
	 * @param	string		$ip
	 * @param	integer		$signonTimestamp
	 * @param	ModeList	$modes
	 * @param	string		$gecos
	 */
	public function __construct($uuid, $timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, $modes, $gecos);

	/**
	 * Returns the uuid of this user type instance
	 * @return	string
	 */
	public function getUuid();

	/**
	 * Returns the creation timestamp of this user type instance
	 * @return	integer
	 */
	public function getTimestamp();

	/**
	 * Returns the nickname of this user type instance
	 * @return	string
	 */
	public function getNick();

	/**
	 * Returns the hostname of this user type instance
	 * @return	string
	 */
	public function getHostname();

	/**
	 * Returns the displayed-hostname for this user type instance
	 * @return	string
	 */
	public function getDisplayedHostname();

	/**
	 * Returns the ident of this user type instance
	 * @return	string
	 */
	public function getIdent();

	/**
	 * Returns the ip of this user type instance
	 * @return	string
	 */
	public function getIP();

	/**
	 * Returns the signon timestamp of this user type instance
	 * @return	string
	 */
	public function getSignonTimestamp();

	/**
	 * Returns the modes of this user type instance
	 * @return	ModeList
	 */
	public function getModes();

	/**
	 * Returns the gecos of this user type instance
	 * @return	string
	 */
	public function getGecos();

	/**
	 * Magic method to manage dynamic data
	 * @param	string	$variable
	 * @param	mixed	$value
	 */
	public function __set($variable, $value);

	/**
	 * Magic method to manage dynamic data
	 * @param	string	$variable
	 */
	public function __get($variable);

	/**
	 * Magic method to use empty() or isset()
	 * @param	string	$variable
	 */
	public function __isset($variable);
}
?>