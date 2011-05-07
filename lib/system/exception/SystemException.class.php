<?php
/**
 * Exception for system errors
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SystemException extends Zend_Exception {

	/**
	 * Creates a new instance of type SystemEception
	 * @param	string	$message
	 * @param	string	$variable1
	 * @param	string	$variable2
	 * @param	string	...
	 */
	public function __construct() {
		// get arguments
		$arguments = func_get_args();

		// validate
		if (count($arguments) < 1) trigger_error("The minimal argument count for ".get_class($this)."::__construct() is 1", E_USER_ERROR);

		// get message
		$message = $arguments[0];

		// generate code
		$code = 1;

		for($i = 0; $i < strlen($message); $i++) {
			$code += ord($message{$i});
		}

		// get message
		$message = call_user_func_array('sprintf', $arguments);

		// call parent constructor
		parent::__construct($message, $code);
	}

	/**
	 * @see Exception::getTraceAsString
	 */
	public function __getTraceAsString() {
		$string = $this->getTraceAsString();

		// replace database shit
		$string = preg_replace("~Database->__construct\(.*\)~", "Database->__construct(...)", $string);
		$string = preg_replace("~mysqli->mysqli\(.*\)~", "mysqli->mysqli(.*)", $string);

		return $string;
	}

	/**
	 * Sends a debug log with exception data
	 *
	 * @return	void
	 */
	public function sendDebugLog() {
		// placeholder
	}
}
?>