<?php
// service imports
require_once(SDIR.'lib/system/irc/protocol/CommandParser.class.php');

/**
 * Parses the FJOIN command
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class FJOIN extends CommandParser {
	
	/**
	 * @see CommandParser::parse()
	 */
	public function parse($line, $lineEx, $source = null) {
		// try to find channel
		if (Services::getChannelManager()->getChannel($lineEx[1]) === null)
			// create channel if not exists
			Services::getChannelManager()->createChannel($lineEx[1], array());
			
		// remove prefix
		$line = substr($line, 1);
		
		// get userList
		$userList = substr($line, (stripos($line, ':') + 1));
		
		// trim message
		$userList = trim($userList);
		
		// little workaround for permanent channels
		if (!empty($userList)) {
			// split userlist
			$userList = explode(" ", $userList);
			
			if (count($userList)) {
				// loop through userList
				foreach($userList as $user) {
					// split user
					list($modes, $user) = explode(",", $user);
					
					// join users
					Services::getChannelManager()->getChannel($lineEx[1])->getUserList()->addUser($user, array(), $modes);
				}
			}
		}
	}
}
?>