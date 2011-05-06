<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the ERROR command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ERROR extends CommandParser {
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// remove : prefix
		$line = substr($line, 1);
		
		// send log line with error message
		Services::getLogger()->err("Received error from server: ".substr($line, (stripos($line, ':') + 1)));
		
		// shut down services
		exit;
	}
}
?>