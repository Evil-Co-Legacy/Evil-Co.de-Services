<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(SDIR.'lib/system/user/ChannelUserType.class.php');

/**
 * Manages users in a channel
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ChannelUserList extends AbstractUserTypeManager {
	
	/**
	 * @see AbstractUserTypeManager::$userType
	 */
	protected $userType = 'ChannelUserType';
	
	/**
	 * Contains the parent channel
	 *
	 * @var	Channel
	 */
	protected $channel = null;
	
	/**
	 * Creates a new instance of type ChannelUserList
	 *
	 * @param	Channel	$channel
	 * @return 	void
	 */
	public function __construct(&$channel) {
		$this->channel = $channel;
	}
	
	/**
	 * Adds a new user to a channel
	 *
	 * @param	mixed			$userID
	 * @param	array<mixed>		$data
	 * @param	string			$userModes
	 * @return 	void
	 */
	public function addUser($userID, $data = array(), $userModes = '') {
		parent::addUser($userID, $data);
		$this->userList[$userID]->setModes($userModes);
	}
	
	/**
	 * Alias for ChannelUserList::addUser()
	 *
	 * @see ChannelUserList::addUser()
	 * @deprecated
	 */
	public function join($userID, $userModes, $data = array()) {
		$this->addUser($userID, $userModes, $data);
	}
	
	/**
	 * Alias for ChannelUserList::removeUser()
	 *
	 * @param	mixed	$userID
	 * @deprecated
	 */
	public function part($userID) {
		$this->removeUser($userID);
	}
}
?>