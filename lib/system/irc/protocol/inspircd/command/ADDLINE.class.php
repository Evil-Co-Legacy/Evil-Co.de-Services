<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the ADDLINE command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ADDLINE extends CommandParser {

	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// remove numeric
		$line = substr($line, 4);

		// get reason
		$reason = substr($line, (stripos($line, ':') + 1));

		// add line
		Services::getLineManager()->addLine($lineEx[1], $lineEx[2], $lineEx[3], $lineEx[4], $lineEx[5], $reason);
	}
}
?>