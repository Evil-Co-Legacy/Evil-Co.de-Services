<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(SDIR.'lib/system/user/BotUserType.class.php');

/**
 * Manages all service bots
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class BotManager extends AbstractUserTypeManager {
	
	/**
	 * @see AbstractUserTypeManager::$userType
	 */
	protected $userType = 'BotUserType';
}
?>