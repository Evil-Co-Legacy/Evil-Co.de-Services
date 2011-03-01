<?php
// Zend imports
require_once('Zend/Exception.php');

/**
 * Exception for system errors
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SystemException extends Zend_Exception {
	
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