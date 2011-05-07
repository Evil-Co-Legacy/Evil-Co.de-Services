<?php
// imports
require_once(DIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(DIR.'lib/system/user/UserUserType.class.php');

/**
 * Manages all network users
 *
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserManager extends AbstractUserTypeManager {

	/**
	 * @see AbstractUserTypeManager::$userType
	 */
	protected $userType = 'UserUserType';
}
?>