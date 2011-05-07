<?php
// service imports
require_once(DIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the MOTD command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class MOTD extends CommandParser {

	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) { }
}
?>