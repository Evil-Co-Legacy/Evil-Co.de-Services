<?php
// imports
require_once(SDIR.'lib/system/irc/Channel.class.php');

/**
 * Manages channels and their user lists
 *
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ChannelManager implements Iterator {

	/**
	 * Contains all channels
	 *
	 * @var array<Channel>
	 */
	protected $channelList = array();

	/**
	 * Points to the current selected channel object in list
	 *
	 * @var integer
	 */
	protected $channelPointer = 0;

	/**
	 * Returns true if a channel already exists in list
	 *
	 * @param	string	$channelName
	 * @return 	boolean
	 */
	public function channelExists($channelName) {
		return (isset($this->channelList[$channelName]));
	}

	/**
	 * Creates a new channel in manager
	 *
	 * @param	string		$channelName
	 * @param	array<mixed>	$data
	 * @return	void
	 */
	public function createChannel($channelName, $data) {
		// unify channel name
		$channelName = Channel::unifyChannelName($channelName);

		// validate
		if ($this->channelExists($channelName)) throw new RecoverableException("The channel '".$channelName."' does already exist!");

		// add channel
		$this->channelList[$channelName] = Services::getMemoryManager()->create(serialize(new Channel($channelName, $data)));

		// log
		Services::getLogger()->info("Created channel ".$channelName);
	}

	/**
	 * Returns the channel object for given channel name
	 *
	 * @param	string	$channelName
	 * @return 	Channel
	 */
	public function getChannel($channelName) {
		// unify channel name
		$channelName = Channel::unifyChannelName($channelName);

		// try to find
		if (!$this->channelExists($channelName)) return null;

		return unserialize($this->channelList[$channelName]->value);
	}

	/**
	 * Removes a channel from list
	 *
	 * @param	string	$channelName
	 * @return	void
	 */
	public function removeChannel($channelName) {
		// unify channel name
		$channelName = Channel::unifyChannelName($channelName);

		// validate
		if (!$this->channelExists($channelName)) throw new RecoverableException("The channel '".$channelName."' does not exist!");

		// remove channel
		unset($this->channelList[$channelName]);

		// log
		Services::getLogger()->info("Deleted channel ".$channelName);
	}

	// ITERATOR METHODS
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->channelPointer = 0;
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->getChannel($this->key());
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		$this->channelPointer++;
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$key = array_keys($this->channelList);

		return $key[$this->channelPointer];
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		return (isset($this->channelList[$this->key()]));
	}
}
?>