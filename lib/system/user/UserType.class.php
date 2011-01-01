<?php
/**
 * Defines default methods for user types
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface UserType {
	
	/**
	 * Creates a new instance of UserType
	 *
	 * @param	mixed	$userID
	 * @param	array	$data
	 */
	public function __construct($userID, $data = array());
	
	/**
	 * Sets a user property
	 *
	 * @param	string	$property
	 * @param	mixed	$value
	 */
	public function __set($property, $value);
	
	/**
	 * Gets a user property
	 *
	 * @param	string	$property
	 */
	public function __get($property);
}
?>