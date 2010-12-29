<?php

/**
 * Defines default methods for UserTypeManagers
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface UserTypeManager {
	
	/**
	 * Adds a new user to userlist
	 * @param	mixed			$userID
	 * @param	array<mixed>	$data
	 * @return void
	 */
	public function addUser($userID, $data = array());
	
	/**
	 * Returnes the user with ID $userID
	 * @param	mixed	$userID
	 * @return UserType
	 */
	public function getUser($userID);
	
	/**
	 * Removes a user from userlist
	 * @param	mixed	$userID
	 * @return void
	 */
	public function removeUser($userID);
}