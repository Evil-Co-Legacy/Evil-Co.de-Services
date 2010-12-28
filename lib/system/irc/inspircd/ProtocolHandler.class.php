<?php
/**
 * Handles commands from server
 *
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class ProtocolHandler {

	/**
	 * Handles the ENDBURST command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function ENDBURST($input, $inputEx) {
		// set new connection state
		Services::getConnection()->getProtocol()->connectionState = 'authed';

		// send debug warning
		if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("WARNING! DEBUGMODE IS ENABLED!");

		// send debuglog to console
		if (defined('DEBUG')) print("ENDBURST\n");

		// send log message
		if (defined('DEBUG')) Services::getConnection()->sendServerLine("NOTICE ".Services::getConnection()->getProtocol()->servicechannel." :[".Services::getConnection()->getProtocol()->name."] Burst finished");

		// memcache
		if (extension_loaded('memcache')) {
			if(defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Memcache extension is available! Trying to find configuration for memcache ...");
			Services::loadMemcache();
		}

		// init modules
		Services::getModuleManager()->init();
	}

	/**
	 * Handles the FJOIN command
	 *
	 * @param	string			$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function FJOIN($input, $inputEx) {
		// get mode string
		if (($chan = Services::getChannelManager()->getChannel($inputEx[2])) === null) {
			$modes = '';
			$activeIndex = 4;

			while($inputEx[$activeIndex]{0} != ':' and stripos($inputEx[$activeIndex], ',') === false) {
				if (!empty($modes)) $modes .= " ";
				$modes .= $inputEx[$activeIndex];
				$activeIndex++;
			}

			// generate userlist
			$userListString = substr($input, (stripos($input, ':') + 1));
			$userListString = trim($userListString);
			$userList = array();

			// handle permanent channels
			if (!empty($userListString)) {
				$userListString = explode(' ', $userListString);

				foreach($userListString as $user) {
					$user = explode(',', $user);
					$userList[] = array('mode' => $user[0], 'user' => Services::getUserManager()->getUser($user[1]));
				}
			}

			// call event
			Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'channelCreated', array('channel' => $inputEx[2], 'userList' => $userList));

			// add channel
			Services::getChannelManager()->addChannel($inputEx[2], $inputEx[3], $modes, $userList);

			// send debug message
			if (defined('DEBUG')) print("Added channel ".$inputEx[2]."\n");
		} else {
			// generate userlist
			$userListString = substr($input, (stripos($input, ':') + 1));
			$userListString = explode(' ', $userListString);
			$userList = array();

			foreach($userListString as $user) {
				$user = explode(',', $user);
				if (count($user) == 2) {
					$userList[] = array('mode' => $user[0], 'user' => Services::getUserManager()->getUser($user[1]));
				}
			}

			// call event
			Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'channelJoined', array('channel' => $inputEx[2], 'userList' => $userList));

			// join users to channel
			$chan->join($userList);
		}
	}
	
	/**
	 * Handles the KICK command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function KICK($input, $inputEx) {
		// fire event
		Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'userKicked', array('target' => $inputEx[2], 'issuer' => $inputEx[0], 'victim' => $inputEx[3], 'reason' => substr($input, (stripos($input, ':') + 1))));
		
		// remove user
		Services::getChannelManager()->getChannel($inputEx[2])->part($inputEx[3]);
	}

	/**
	 * Handles the METADATA command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function METADATA($input, $inputEx) {
		if ($inputEx[2]{0} == '#' and Services::getChannelManager()->getChannel($inputEx[2]) !== null) {
			// set metadata
			Services::getChannelManager()->getChannel($inputEx[2])->{$inputEx[3]} = substr($input, (stripos($input, ':') + 1));

			// try to decode data
			try {
				$data = unserialize(Services::getChannelManager()->getChannel($inputEx[2])->{$inputEx[3]});
				Services::getChannelManager()->getChannel($inputEx[2])->{$inputEx[3]} = $data;
			} catch (Exception $ex) {
				// ignore
			}
		} elseif ($inputEx[2]{0} != '#' and Services::getUserManager()->getUser($inputEx[2]) !== null) {
			// set metadata
			Services::getUserManager()->getUser($inputEx[2])->{$inputEx[3]} = substr($input, (stripos($input, ':') + 1));

			// try to decode data
			try {
				$data = unserialize(Services::getUserManager()->getUser($inputEx[2])->{$inputEx[3]});
				Services::getUserManager()->getUser($inputEx[2])->{$inputEx[3]} = $data;
			} catch (Exception $ex) {
				// ignore
			}
		}
	}

	/**
	 * Handles the NOTICE command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function NOTICE($input, $inputEx) {
		self::PRIVMSG($input, $inputEx);
	}

	/**
	 * Handles PART command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function PART($input, $inputEx) {
		Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'userParted', array('channel' => $inputEx[2], 'user' => Services::getUserManager()->getUser($inputEx[0])));
		Services::getChannelManager()->getChannel($inputEx[2])->part($inputEx[0]);
	}

	/**
	 * Handles PING command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function PING($input, $inputEx) {
		// fire event
		Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'ping', array('source' => $inputEx[0]));

		// send PONG
		Services::getConnection()->sendServerLine("PONG ".$inputEx[3]." ".$inputEx[2]);

		// send debug line
		if (defined('DEBUG')) print("Ping -> Pong\n");
	}

	/**
	 * Handles the PRIVMSG command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function PRIVMSG($input, $inputEx) {
		if ($inputEx[2]{0} != '$') {
			// get source
			$source = Services::getUserManager()->getUser($inputEx[0]);

			if ($inputEx[2]{0} == '#' and $source !== null) {
				// send debug message
				if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine($source->getUuid()." (".$source->getNick().") sent a message to ".$inputEx[2]);

				// notify module manager
				Services::getModuleManager()->handleLine($source, $inputEx[2], substr($input, (stripos($input, ':') + 1)));
			} elseif ($source) {
				// kick numeric
				$inputEx[2] = substr($inputEx[2], strlen(Services::getConnection()->getProtocol()->numeric));

				// send debug message
				if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine($source->getUuid()." (".$source->getNick().") sent a message to ".$inputEx[2]);

				// try to find bot
				if (($bot = Services::getBotManager()->getUser($inputEx[2])) !== null) {
					// resolved uuid ... send debug message
					if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Resolved ".$inputEx[2]." to ".$bot->getNick());

					// notify module manager
					Services::getModuleManager()->handleLine($source, $inputEx[2], substr($input, (stripos($input, ':') + 1)));
				} else {
					// cannot find user ... send debug message
					if (defined('DEBUG')) Services::getConnection()->getProtocol()->sendLogLine("Cannot resolve '".$inputEx[2]."'! Type of return value: ".gettype($bot));
				}
			} else {
				Services::getConnection()->getProtocol()->sendLogLine("Received invalid UUID '".$inputEx[0]."'! Maybe choosen wrong IRCd?");
			}
		}
	}

	/**
	 * Handles the QUIT command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function QUIT($input, $inputEx) {
		Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'userQuit', array('user' => Services::getUserManager()->getUser($inputEx[0])));
		Services::getUserManager()->removeUser($inputEx[0]);
	}

	/**
	 * Handles the SERVER command
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function SERVER($input, $inputEx) {
		Services::getEvent()->fire(Services::getConnection()->getProtocol(), 'serverCreated', array('name' => $inputEx[2]));
		Services::getConnection()->getProtocol()->serverList[] = $inputEx[2];
	}

	/**
	 * Handles an UID line from server
	 *
	 * @param	string		$input
	 * @param	array<string>	$inputEx
	 * @return	void
	 */
	public static function UID($input, $inputEx) {
		// get mode string
		$modes = '';
		$activeIndex = 10;

		while($inputEx[$activeIndex]{0} != ':') {
			if (!empty($modes)) $modes .= " ";
			$modes .= $inputEx[$activeIndex];
			$activeIndex++;
		}

		// add user to manager
		Services::getUserManager()->introduceUser($inputEx[3], $inputEx[4], $inputEx[5], $inputEx[6], $inputEx[7], $inputEx[8], $inputEx[9], $modes, substr($input, (stripos($input, ':') + 1)), $inputEx[2]);

		// send debug message
		if (defined('DEBUG')) print("Added user ".$inputEx[2]."\n");
	}
}
?>