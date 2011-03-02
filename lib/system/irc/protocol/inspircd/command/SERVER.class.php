<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the SERVER command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SERVER extends CommandParser {
	
	/**
	 * Contains a complete list of all servers
	 * @todo Add a central array for this
	 * @var array<string>
	 */
	protected $serverList = array();
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx) {
		$this->serverList[] = $lineEx[1];
	}
}
?>