<?php
// imports
require_once(DIR.'lib/system/user/AbstractUserType.class.php');

/**
 * Represents a user
 *
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserUserType extends AbstractUserType {

	/**
	 * @see AbstractUserType::__set()
	 */
	public function __set($property, $value) {
		parent::__set($property, $value);

		// fire event
		Services::getEventHandler()->fire($this, 'propertyModified', array('property' => $property, 'value' => $value));
	}
}
?>