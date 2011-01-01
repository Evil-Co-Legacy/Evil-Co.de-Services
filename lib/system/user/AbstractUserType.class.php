<?php
// imports
require_once(SDIR.'lib/system/user/UserType.class.php');

/**
 * Defines defaults for user types
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class AbstractUserType implements UserType {
	
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
	 * @see UserType::__construct()
	 */
	public function __construct($userID, $data = array()) {
		$this->userID = $userID;
		
		foreach($data as $key => $value) {
			$this->{$key} = $value;
		}
	}
	
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
}
?>