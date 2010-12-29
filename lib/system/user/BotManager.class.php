<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(SDIR.'lib/system/user/Bot.class.php');

/**
 * Manages all bots
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class BotManager extends AbstractUserTypeManager {
	
	/**
	 * @see	AbstractUserTypeManager::$userType
	 */
	protected $userType = 'Bot';
}
?>