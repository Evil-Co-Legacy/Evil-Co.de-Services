<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(SDIR.'lib/system/user/ChannelUserType.class.php');

/**
 * Manages users in a channel
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ChannelUserList extends AbstractUserTypeManager {
	
	/**
	 * @see AbstractUserTypeManager::$userType
	 */
	protected $userType = 'ChannelUserType';
	
	/**
	 * Contains the parent channel
	 * @var	Channel
	 */
	protected $channel = null;
	
	/**
	 * Creates a new instance of type ChannelUserList
	 * @param	Channel	$channel
	 */
	public function __construct(&$channel) {
		$this->channel = $channel;
	}
}
?>