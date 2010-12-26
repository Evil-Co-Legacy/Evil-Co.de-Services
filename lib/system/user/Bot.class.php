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

	/**
	 * Joins the bot to given channel
	 * @param	string	$channel
	 * @param	string	$modes
	 * @param	string	$userModes
	 */
	public function join($channel, $modes = "+nt", $userModes = "+oaq") {
		// remove + from string
		if (stripos($userModes, '+') !== false) $userModes = substr($userModes, 1);

		// join channel
		Services::getConnection()->getProtocol()->join($this->getUuid(), $channel, $modes, $userModes);

		// notify channel manager
		if (($chan = Services::getChannelManager()->getChannel($channel)) === null) {
			Services::getChannelManager()->addChannel($channel, time(), $modes, array(array('mode' => $userModes, 'user' => $this)));
		} else {
			$chan->join(array(array('mode' => $userModes, 'user' => $this)));
		}
	}
}
?>