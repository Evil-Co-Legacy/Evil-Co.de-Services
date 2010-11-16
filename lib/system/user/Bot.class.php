<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserType.class.php');

/**
 * Represents a bot user
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Bot extends AbstractUserType {
	
	/**
	 * Markes this UserType as bot
	 * @var	boolean
	 */
	public $isBot = true;
}
?>