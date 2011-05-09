<?php
// service imports
require_once(DIR.'lib/system/module/ExtensionModule.class.php');
require_once(DIR.'lib/system/user/UserType.class.php');

/**
 * Defines default methods for bots
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class BotModule extends ExtensionModule implements UserType {
	
	/**
	 * Contains additional properties for user objects
	 *
	 * @var array<mixed>
	 */
	protected $data = array();

	/**
	 * Contains an unique ID for users
	 *
	 * @var mixed
	 */
	public $userID = null;

	/**
	 * @see UserType::__set()
	 */
	public function __set($property, $value) {
		$this->data[$property] = $value;
	}

	/**
	 * @see UserType::__get()
	 */
	public function __get($property) {
		if (isset($this->data[$property])) return $this->data[$property];
		return null;
	}

	/**
	 * Converts this object to serialized string (Used for memory manager)
	 * @return string
	 */
	public function __toString() {
		return serialize($this);
	}
	
}
?>