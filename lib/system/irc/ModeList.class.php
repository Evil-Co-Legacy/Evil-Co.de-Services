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
	 * Returnes true if the specified mode is set
	 * @param	string(1)	$modeChar
	 */
	public function hasMode($modeChar);
	
	/**
	 * Parses the given mode string
	 * @param	string	$string
	 */
	protected function parseModeString($string);
	
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