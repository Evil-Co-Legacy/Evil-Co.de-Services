<?php
// Zend imports
require_once('Zend/Log/Writer/Abstract.php');
require_once('Zend/Log/Formatter/Simple.php');

/**
 * Custom log writer that logs to service-channel
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class IRCLogWriter extends Zend_Log_Writer_Abstract {
	
	/**
	 * Creates a new instance of IRCLogWriter
	 */
	public function __construct() {
		$this->_formatter = new Zend_Log_Formatter_Simple();
	}
	
	/**
	 * Writes an event to log
	 * @param	array	$event
	 * @return void
	 */
	protected function _write($event) {
		if (Services::getConnection()->isAlive() and Services::getProtocol()->isAlive()) {
			// get log string
			$line = $this->_formatter->format($event);
			
			// trim message
			$line = StringUtil::trim($line);
			
			// detect newlines
			if (stripos($line, "\n") !== null) {
				// split
				$lineEx = explode("\n", $line);
				
				// send each line to service channel
				foreach($lineEx as $line) {
					Services::getProtocol()->sendMessage(Services::getConfiguration()->connection->servicechannel, $line);
				}
			} else {
				Services::getProtocol()->sendMessage(Services::getConfiguration()->connection->servicechannel, $line);
			}
		}
	}
}
?>