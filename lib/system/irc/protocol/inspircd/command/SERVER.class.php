<?php
// service imports
require_once(DIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the SERVER command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SERVER extends CommandParser {

	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// add server to list
		Services::getServerManager()->registerServer($lineEx[1], $lineEx[4]);
	}
}
?>