<?php
// imports
require_once(SDIR.'lib/system/irc/ChannelUserList.class.php');

/**
 * Represents a channel
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Channel {
	
	/**
	 * Contains the channel name
	 * @var	string
	 */
	protected $channelName = '';
	
	/**
	 * Contains additional properties
	 * @var array<mixed>
	 */
	protected $data = array();
	
	/**
	 * Contains a ChannelUserList
	 * @var ChannelUserList
	 */
	protected $userList = null;
	
	/**
	 * Creates a new instance of Channel
	 * @param	string			$channelName
	 * @param	array<mixed>	$data
	 */
	public function __construct($channelName, $data) {
		// set properties
		$this->channelName = self::unifyChannelName($channelName);
		
		// set additional properties
		foreach($data as $key => $value) {
			$this->{$key} = $value;
		}
		
		// create new ChannelUserList
		$this->userList = new ChannelUserList($this);
	}
	
	/**
	 * Returnes the name of this channel
	 * @return string
	 */
	public function getChannelName() {
		return $this->channelName;
	}
	
	/**
	 * Returnes the ChannelUserList object for this channel
	 */
	public function getUserList() {
		return $this->userList;
	}
	
	/**
	 * Sets an additional property
	 * @param	string	$property
	 * @param	mixed	$value
	 * @return void
	 */
	public function __set($property, $value) {
		Services::getEvent()->fire($this, 'dataSet', array('property' => $property, 'value' => $value));
		$this->data[$property] = $value;
	}
	
	/**
	 * Returnes true if the given property exists
	 * @param	string	$property
	 * @return boolean
	 */
	public function __isset($property) {
		return (isset($this->data[$property]));
	}
	
	/**
	 * Returnes the value of an additional property
	 * @param	string	$property
	 * @return mixed
	 */
	public function __get($property) {
		if (isset($this->data[$property])) return $this->data[$property];
		return null;
	}
}
?>