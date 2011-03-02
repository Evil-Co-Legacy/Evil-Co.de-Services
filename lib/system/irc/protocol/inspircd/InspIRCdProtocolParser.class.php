<?php

/**
 * Parses server-to-server commands
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class InspIRCdProtocolParser {
	
	/**
	 * Contains a pattern for server numerics
	 * @var string
	 */
	const NUMERIC_PATTERN = '~^:[0-9][A-Z0-9][A-Z0-9]$~i';
	
	/**
	 * Contains a pattern for user numerics
	 * @var string
	 */
	const UUID_PATTERN = '~^:[A-Z][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9]$~i';
	
	/**
	 * Contains information about the server connection (
	 * @var array
	 */
	protected static $connectionInformation = array();
	
	/**
	 * Contains all loaded command parsers
	 * @var array<CommandParser>
	 */
	protected static $loadedCommandParsers = array();
	
	/**
	 * Contains an array with all loaded modules
	 * @var array
	 */
	protected static $loadedModules = array();
	
	/**
	 * Returnes the given command parser
	 * @param	string	$parser
	 */
	protected static function getCommandParserInstance($parser) {
		if (!isset(self::$loadedCommandParsers[$parser])) {
			self::$loadedCommandParsers[$parser] = new $parser();
		}
		
		return self::$loadedCommandParsers[$parser];
	}
	
	/**
	 * Returnes information about the connection
	 * @return array
	 */
	public static function getConnectionInformation() {
		return self::$connectionInformation;
	}
	
	/**
	 * Returnes all loaded modules for current connection
	 * @return array
	 */
	public static function getLoadedModules() {
		return self::$loadedModules;
	}
	
	/**
	 * Handles commands while the connection is still alive and synched
	 * @param	string	$line
	 * @return string
	 * @throws RecoverableException
	 */
	public static function handleCommand($line) {
		// FIXME: handle empty lines in connection class
		if (empty($line)) return;
		
		// explode string
		$lineEx = explode(" ", $line);
		
		// get correct command
		if (!preg_match(self::NUMERIC_PATTERN, $lineEx[0]))
			$command = $lineEx[0];
		else
			$command = $lineEx[1];
		
		// try to find a command handler
		if (!file_exists(SDIR.'lib/system/irc/protocol/inspircd/command/'.strtoupper($command).'.class.php')) throw new RecoverableException("No command parser for link command '".strtoupper($command)."' found! Maybe the protocol definition is outdated!");
		
		// load command handler
		require_once(SDIR.'lib/system/irc/protocol/inspircd/command/'.strtoupper($command).'.class.php');
		
		// remove numeric
		if (preg_match(self::NUMERIC_PATTERN, $lineEx[0])) {
			// get source
			$source = Services::getServerManager()->getServerByIdentifier(substr($lineEx[0], 1));
			
			// validate
			if ($source === null) throw new RecoverableException("Received message from non-existant server '".$lineEx[0]."'");
			
			// delete first position in array
			unset($lineEx[0]);
			
			// resort
			$lineEx = array_merge(array(), $lineEx);
		} elseif (preg_match(self::UUID_PATTERN, $lineEx[0])) {
			// get source
			$source = Services::getUserManager()->getUser(substr($lineEx[0], 1));
			
			// validate
			if ($source === null) throw new RecoverableException("Received message from non-existant user '".$lineEx[0]."'");
			
			// delete first position in array
			unset($lineEx[0]);
			
			// resort
			$lineEx = array_merge(array(), $lineEx);
		};
		
		// generate instance
		$instance = self::getCommandParserInstance($command);
		
		// parse command
		$instance->parse($line, $lineEx, (isset($source) ? $source : null));
		
		// return command name
		return $command;
	}
	
	/**
	 * Handles connection commands (Such as CAPAB)
	 * @param	string	$line
	 * @return boolean
	 * @throws ProtocolException
	 */
	public static function handleConnectionCommand($line) {
		// FIXME: handle empty lines in connection class
		if (empty($line)) return;
		
		// explode string
		$lineEx = explode(" ", $line);
		
		// wait for known commands
		switch($lineEx[0]) {
			case "CAPAB":
				// capab has subcommands
				switch($lineEx[1]) {
					case 'CAPABILITIES':
						// read connection information
						$capabilities = substr($line, (stripos($line, ':') + 1));
						
						// split
						$capabilitiesEx = explode(" ", $capabilities);
						
						// loop through elements
						foreach($capabilitiesEx as $info) {
							// split
							$infoEx = explode("=", $info);
							
							// save
							if (count($infoEx) >= 2) self::$connectionInformation[$infoEx[0]] = $infoEx[1];
						}
						break;
					case 'MODULES':
						// read modules
						$modules = substr($line, (stripos($line, ':') + 1));
						
						// split
						$modulesEx = explode(",", $modules);
						
						// write to class array
						// Note: we use array_merge here to handle module information that can't send in one command (IRC has a limit of 512bit)
						self::$loadedModules = array_merge($modulesEx, self::$loadedModules);
						
						// resort array
						sort(self::$loadedModules);
						break;
					case 'END':
						return true;
						break;
				}
				break;
			default:
				throw new ProtocolException("Unknown connection command '".$line."'");
				break;
		}
		
		return false;
	}
}
?>