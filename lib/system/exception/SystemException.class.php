<?php
/**
 * Exception for system errors
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SystemException extends Exception {
	
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
		// send message
		$message = explode("\n", $this->message);
		Services::getConnection()->sendLogLine("Exception thrown with message:".$message[0]);
		
		if (count($message) > 1) {
			foreach($message as $key => $log) {
				if ($key > 0) Services::getConnection()->sendLogLine($log);
			}
		}
		
		// send code
		Services::getConnection()->sendLogLine("Code: ".$this->code);
		
		// send stacktrace
		$stacktraceArray = explode("\n", $this->__getTraceAsString());
		
		Services::getConnection()->sendLogLine("Stacktrace:");
		foreach($stacktraceArray as $stacktrace) {
			Services::getConnection()->sendLogLine($stacktrace);
		}
	}
}
?>