<?php
// imports
require_once(SDIR.'lib/system/irc/channel/Channel.class.php');

/**
 * Manages all channels on network
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class ChannelManager {
	
	/**
	 * Contains all channels on network
	 * @var	array<Channel>
	 */
	protected $channelList = array();
	
	/**
	 * Adds a channel to manager
	 * @param	string			$name
	 * @param	integer			$timestamp
	 * @param	ModeList		$modes
	 * @param	array<UserType>	$userList
	 */
	public function addChannel($name, $timestamp, $modes, $userList) {
		$this->channelList[] = new Channel($name, $timestamp, $modes, $userList);
	}
	
	/**
	 * Returnes the channel object for the given channel
	 * @param	string	$name
	 */
	public function getChannel($name) {
		foreach($this->channelList as $key => $channel) {
			if ($channel->getName() == $name) $chan = &$this->channelList[$key];
		}
		
		if (isset($chan)) return $chan;
		return null;
	}
	
	/**
	 * Removes a channel from list
	 * @param	string	$name
	 */
	public function removeChannel($name) {
		foreach($this->channelList as $key => $channel) {
			if ($channel->getName() == $name) unset($this->channelList[$key]);
		}
	}
}
?>