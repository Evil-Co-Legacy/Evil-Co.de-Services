<?php
// imports
require_once(SDIR.'lib/system/irc/channel/Channel.class.php');
require_once(SDIR.'lib/system/irc/ChannelModeList.class.php');

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
	 * @param	string			$modes
	 * @param	array<UserType>	$userList
	 */
	public function addChannel($name, $timestamp, $modes, $userList) {
		if (!Services::memcacheLoaded())
			$this->channelList[] = new Channel($name, $timestamp, new ChannelModeList($modes), $userList);
		else
			Services::getMemcache()->add('channel_'.$name, (new Channel($name, $timestamp, new ChannelModeList($modes), $userList)));
	}
	
	/**
	 * Returnes the channel object for the given channel
	 * @param	string	$name
	 */
	public function getChannel($name) {
		if (!Services::memcacheLoaded()) {
			foreach($this->channelList as $key => $channel) {
				if ($channel->getName() == $name) return $this->channelList[$key];
			}
		} elseif (Services::getMemcache()->get('channel_'.$name))
			return Services::getMemcache()->get('channel_'.$name);
		
		return null;
	}
	
	/**
	 * Removes a channel from list
	 * @param	string	$name
	 */
	public function removeChannel($name) {
		if (!Services::memcacheLoaded()) {
			foreach($this->channelList as $key => $channel) {
				if ($channel->getName() == $name) unset($this->channelList[$key]);
			}
		} else
			Services::getMemcache()->delete('channel_'.$name);
	}
}
?>