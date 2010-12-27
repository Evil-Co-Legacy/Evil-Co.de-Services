<?php
// imports
require_once(SDIR.'lib/system/user/AbstractUserType.class.php');

/**
 * Represents an irc user
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class User extends AbstractUserType {

	/**
	 * @see lib/system/user/AbstractUserType::__set()
	 */
	public function __set($variable, $value) {
		parent::__set($variable, $value);

		// send metadata
		Services::getConnection()->getProtocol()->sendMetadata($this->getUuid(), $variable, $value);
	}
}
?>