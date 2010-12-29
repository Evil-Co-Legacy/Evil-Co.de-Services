<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(SDIR.'lib/system/user/User.class.php');

/**
 * Manages all network users
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class UserManager extends AbstractUserTypeManager {
	
	/**
	 * @see	AbstractUserTypeManager::$userType
	 */
	protected $userType = 'User';
}
?>