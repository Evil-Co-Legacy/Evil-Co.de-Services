<?php
// imports
require_once(SDIR.'lib/system/irc/AbstractMode.class.php');

/**
 * Represents a mode
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class IUpperChannelMode extends AbstractMode {
	
	/**
	 * @see	AbstractMode::$canHaveArgument
	 */
	protected static $canHaveArgument = true;
}
?>