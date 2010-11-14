<?php
// imports
require_once(SDIR.'lib/system/irc/AbstractModeList.class.php');

/**
 * Manages all modes for channels
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class ChannelModeList extends AbstractModeList {
	
	/**
	 * @see	AbstractModeList::$type
	 */
	protected $type = 'channel';
}
?>