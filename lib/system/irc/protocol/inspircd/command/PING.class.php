<?php
// service imports
require_once(DIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the PING command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class PING extends CommandParser {

	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// send ping
		Services::getProtocolManager()->sendPong($lineEx[1]);

		// send log line
		Services::getLogger()->debug("Ping from ".$lineEx[1]." -> Pong");
	}
}
?>