<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the METADATA command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class METADATA extends CommandParser {
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// remove prefix
		$line = substr($line, 1);
		
		// set metadata
		Services::getUserManager()->getUser($lineEx[1])->__set($lineEx[2], substr($line, (stripos($line, ':') + 1)));
		
		// log
		Services::getLog()->debug("Set metadata '".$lineEx[2]."' for user ".Services::getUserManager()->getUser($lineEx[1])->nickname);
	}
}
?>