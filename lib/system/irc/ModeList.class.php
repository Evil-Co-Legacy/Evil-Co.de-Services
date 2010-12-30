<?php

/**
 * Manages and parses modestrings
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ModeList {
	
	/**
	 * Creates a new instance of ModeList
	 * @param	string	$modeString
	 */
	public function __construct($modeString);
	
	/**
	 * Adds a mode char
	 * @param	string	$modeChar
	 */
	protected function addMode($modeChar);
	
	/**
	 * Returnes true if the given mode char has an argument
	 * @param	string(1) $modeChar
	 */
	public static function hasArgument($modeChar);
	
	/**
	 * Returnes true if the specified mode is set
	 * @param	string(1)	$modeChar
	 */
	public function hasMode($modeChar);
	
	/**
	 * Loads a mode from predefined location
	 * @param	string(1)	$modeChar
	 */
	public static function loadMode($modeChar);
	
	/**
	 * Parses the given mode string
	 * @param	string	$string
	 */
	protected function parseModeString($string);
	
	/**
	 * Removes a mode char
	 * @param	string(1)	$modeChar
	 */
	protected function removeMode($modeChar);
	
	/**
	 * Updates the current mode string
	 * @param	string	$modeString
	 */
	public function updateModes($modeString);
	
	/**
	 * Converts the ModeList to type string
	 */
	public function __toString();
}
?>