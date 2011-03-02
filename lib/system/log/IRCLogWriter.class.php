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
	 * Create a new instance of Zend_Log_Writer_Mock
	 *
	 * @param  array|Zend_Config $config
	 * @return Zend_Log_Writer_Mock
	 */
	public static function factory($config) {
		return new self();
	}
	
	/**
	 * Writes an event to log
	 * @param	array	$event
	 * @return void
	 */
	protected function _write($event) {
		if (Services::getConnection() !== null and Services::getConnection()->isAlive() and Services::getProtocol() !== null and Services::getProtocol()->isAlive() and Services::getProtocol()->isReady()) {
			// get log string
			$line = $this->_formatter->format($event);
			
			// trim message
			$line = StringUtil::trim($line);
			
			// unify newlines
			$line = StringUtil::unifyNewlines($line);
			
			// remove tabs
			$line = str_replace("\t", "        ", $line);
			
			// detect newlines
			if (stripos($line, "\n") !== null) {
				// split
				$lineEx = explode("\n", $line);
				
				// send each line to service channel
				foreach($lineEx as $newline) {
					// trim message
					$newline = StringUtil::trim($newline);
					
					// send
					if (!empty($newline)) Services::getProtocol()->sendMessage(Services::getConfiguration()->connection->servicechannel, $newline);
				}
			} else {
				Services::getProtocol()->sendMessage(Services::getConfiguration()->connection->servicechannel, $line);
			}
		}
	}
}
?>