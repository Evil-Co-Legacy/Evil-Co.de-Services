<?php

/**
 * Defines default methods for user type managers
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
interface UserTypeManager {
	
	/**
	 * Introduces a new user
	 * @param	integer	$timestamp
	 * @param	string	$nick
	 * @param	string	$hostname
	 * @param	string	$displayedHostname
	 * @param	string	$ident
	 * @param	string	$ip
	 * @param	integer	$signonTimestamp
	 * @param	string	$modes
	 * @param	string	$gecos
	 * @return	string
	 */
	public function introduceUser($timestamp, $nick, $hostname, $displayedHostname, $ident, $ip, $signonTimestamp, $modes, $gecos);

	/**
	 * Removes a user from manager
	 * @param	string	$uuid
	 */
	public function removeUser($uuid);
	
	/**
	 * Returnes the user object with given uuid
	 * @param	string	$uuid
	 * @return	UserType
	 */
	public function getUser($uuid);
}
?>