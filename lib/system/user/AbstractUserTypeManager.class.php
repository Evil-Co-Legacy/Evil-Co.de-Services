<?php
// imports
require_once(DIR.'lib/system/user/AbstractUserType.class.php');
require_once(DIR.'lib/system/user/UserTypeManager.class.php');

/**
 * Manages a special type of users
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractUserTypeManager implements UserTypeManager, Iterator {

	/**
	 * Contains all users to manage
	 *
	 * @var array<UserType>
	 */
	protected $userList = array();

	/**
	 * Contains the current iterator pointer
	 *
	 * @var integer
	 */
	protected $userListPointer = 0;

	/**
	 * Contains the class name that should used for every user
	 *
	 * @var string
	 */
	protected $userType = 'AbstractUserType';

	/**
	 * @see UserTypeManager::addUser()
	 */
	public function addUser($userID, $data = array()) {
		// add user
		$this->userList[$userID] = Services::getMemoryManager()->create(new $this->userType($userID, $data));

		// debug log
		Services::getLogger()->debug("Added a new user with ID ".$userID." to ".get_class($this));
	}

	/**
	 * @see UserTypeManager::getUser()
	 */
	public function getUser($userID) {
		if (isset($this->userList[$userID])) return unserialize($this->userList[$userID]->value);
		return null;
	}

	/**
	 * @see UserTypeManager::removeUser()
	 */
	public function removeUser($userID) {
		// remove user
		if (isset($this->userList[$userID])) unset($this->userList[$userID]);

		// debug log
		Services::getLogger()->debug("Removed the user with ID ".$userID." from ".get_class($this));
	}

	// ITERATOR METHODS
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->userListPointer = 0;
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// return value
		return $this->getUser($this->key());
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$keys = array_keys($this->userList);

		// validate current pointer
		if (!isset($keys[$this->userListPointer])) throw new SystemException("Pointer out of index");

		return $keys[$this->userListPointer];
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		$this->userListPointer++;
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		// get keys
		$keys = array_keys($this->userList);

		return (isset($keys[$this->userListPointer]));
	}
}
?>