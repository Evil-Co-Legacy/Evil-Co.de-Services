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

	/**
	 * Sends a message to $target
	 * @param	string	$target
	 * @param	string	$message
	 */
	public function sendMessage($target, $message) {
		Services::getConnection()->getProtocol()->sendNotice($this->getUuid(), $target, $message);
	}
}
?>