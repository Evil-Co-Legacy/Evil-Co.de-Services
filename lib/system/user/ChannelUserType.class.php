<?php
// imports
require_once(DIR.'lib/system/user/AbstractUserType.class.php');
require_once(DIR.'lib/system/user/ChannelUserModeList.class.php');

/**
 * Represents a user in a channel
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ChannelUserType extends AbstractUserType {

	/**
	 * Contains all user modes in the parent channel
	 *
	 * @var ModeList
	 */
	protected $modes = null;

	/**
	 * Sets new modes to user
	 *
	 * @param	string	$modeString
	 */
	public function setModes($modeString) {
		if ($this->modes === null) {
			$this->modes = new ChannelUserModeList($modeString);
		}
		else {
			$this->modes->updateModes($modeString);
		}
	}
}
?>