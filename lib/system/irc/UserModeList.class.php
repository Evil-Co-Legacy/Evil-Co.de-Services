<?php
// imports
require_once(SDIR.'lib/system/irc/AbstractModeList.class.php');

/**
 * Manages all modes for users
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class UserModeList extends AbstractModeList {
	
	/**
	 * @see	AbstractModeList::$type
	 */
	protected $type = 'user';
}
?>