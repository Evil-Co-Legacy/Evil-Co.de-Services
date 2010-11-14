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
	 * Returnes the uuid of this user type instance
	 * @return	string
	 */
	public function getUuid();
	
	/**
	 * Returnes the creation timestamp of this user type instance
	 * @return	integer
	 */
	public function getTimestamp();
	
	/**
	 * Returnes the nickname of this user type instance
	 * @return	string
	 */
	public function getNick();
	
	/**
	 * Returnes the hostname of this user type instance
	 * @return	string
	 */
	public function getHostname();
	
	/**
	 * Returnes the displayed-hostname for this user type instance
	 * @return	string
	 */
	public function getDisplayedHostname();
	
	/**
	 * Returnes the ident of this user type instance
	 * @return	string
	 */
	public function getIdent();
	
	/**
	 * Returnes the ip of this user type instance
	 * @return	string
	 */
	public function getIP();
	
	/**
	 * Returnes the signon timestamp of this user type instance
	 * @return	string
	 */
	public function getSignonTimestamp();
	
	/**
	 * Returnes the modes of this user type instance
	 * @return	ModeList
	 */
	public function getModes();
	
	/**
	 * Returnes the gecos of this user type instance
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
}
?>