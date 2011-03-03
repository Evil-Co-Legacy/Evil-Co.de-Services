<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');
require_once(SDIR.'lib/system/user/UserModeList.class.php');

/**
 * Parses the UID command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UID extends CommandParser {
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// get realname
		$line = substr($line, 1);
		$realname = substr($line, (stripos($line, ':') + 1));
		
		// get modes
		$modes = substr($line, (stripos($line, '+')));
		$modes = substr($modes, 0, (stripos($modes, ':')));
		
		$modes = new UserModeList($modes);
		
		// add user
		Services::getUserManager()->addUser($lineEx[1], array('timestamp' => $lineEx[2], 'nickname' => $lineEx[3], 'hostname' => $lineEx[4], 'displayedHostname' => $lineEx[5], 'ident' => $lineEx[6], 'ip' => $lineEx[7], 'signon' => $lineEx[8], 'modes' => $modes));
	}
}
?>