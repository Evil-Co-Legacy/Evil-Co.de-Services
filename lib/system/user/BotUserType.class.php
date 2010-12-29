<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserType.class.php');

/**
 * Represents a bot
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class BotUserType extends AbstractUserType {
	
	/**
	 * This is just set to true. It will used to identify bots
	 * @var boolean
	 */
	public final $isBot = true;
}
?>