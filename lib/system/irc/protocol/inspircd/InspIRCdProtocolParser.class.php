<?php

/**
 * Parses server-to-server commands
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class InspIRCdProtocolParser {
	
	/**
	 * Contains information about the server connection (
	 * @var array
	 */
	protected static $connectionInformation = array();
	
	/**
	 * Contains an array with all loaded modules
	 * @var array
	 */
	protected static $loadedModules = array();
	
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
	 * @throws RecoverableException
	 */
	public static function handleCommand($line) {
		// FIXME: handle empty lines in connection class
		if (empty($line)) return;
		
		// explode string
		$lineEx = explode(" ", $line);
		
		// try to find a command handler
		if (!file_exists(SDIR.'lib/irc/protocol/inspircd/command/'.strtoupper($lineEx[1])).'.class.php') throw new RecoverableException("No command parser for link command '".strtoupper($lineEx[1])."' found! Maybe the protocol definition is outdated!");
		
		// load command handler
		require_once(SDIR.'lib/irc/protocol/inspircd/command/'.strtoupper($lineEx[1]).'.class.php');
		
		// generate instance
		$instance = call_user_func(array(strtoupper($lineEx[1]), 'getInstance'));
		
		// parse command
		$instance->parse($line, $lineEx);
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