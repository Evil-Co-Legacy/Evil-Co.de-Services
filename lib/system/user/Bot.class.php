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
	public function join($channel, $modes = "+nt", $userModes = "+a") {
		// remove + from string
		// if (stripos($userModes, '+') !== false) $userModes = substr($userModes, 1);

		// join channel
		Services::getConnection()->getProtocol()->join($this->getUuid(), $channel, $modes);
		Services::getConnection()->getProtocol()->sendMode($this->getUuid(), $channel, $userModes." ".Services::getConnection()->getProtocol()->getNumeric().$this->getUuid());

		// notify channel manager
		if (($chan = Services::getChannelManager()->getChannel($channel)) === null) {
			Services::getChannelManager()->addChannel($channel, time(), $modes, array(array('mode' => $userModes, 'user' => $this)));
		} else {
			$chan->join(array(array('mode' => $userModes, 'user' => $this)));
		}
	}

	/**
	 * Removes the bot from given channel
	 * @param	string	$channel
	 */
	public function part($channel, $message = "Leaving") {
		// join channel
		Services::getConnection()->getProtocol()->part($this->getUuid(), $channel, $message);

		// notify channel manager
		$chan = Services::getChannelManager()->getChannel($channel);
		$chan->part($this->getUuid());
	}
	
	/**
	 * Sends a QUIT to server
	 * @param	string	$message
	 */
	public function quit($message = "Shutting down ...") {
		Services::getConnection()->getProtocol()->sendQuit($this->getUuid(), $message);
	}
}
?>