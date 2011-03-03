<?php
// imports
require_once(SDIR.'lib/system/irc/AbstractModeList.class.php');

/**
 * Manages modes for users
 *
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserModeList extends AbstractModeList {
	
	/**
	 * @see AbstractModeList::$modeInformationFilename
	 */
	protected static $modeInformationFilename = 'userModes';
}
?>