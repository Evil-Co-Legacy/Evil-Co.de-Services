<?php
// service imports
require_once(DIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the VERSION command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class VERSION extends CommandParser {

	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// get version string
		$line = substr($line, 1);
		$version = substr($line, (stripos($line, ':') + 1));

		// set version metadata
		$source->version = $version;
	}
}
?>