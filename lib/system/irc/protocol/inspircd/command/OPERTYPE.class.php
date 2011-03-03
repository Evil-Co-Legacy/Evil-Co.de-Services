<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the OPERTYPE command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class OPERTYPE extends CommandParser {
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// set opertype
		$source->operType = $lineEx[1];
		
		// send info log
		Services::getLog()->info("User ".$source->nickname." is now operator of type ".$lineEx[1]);
	}
}
?>