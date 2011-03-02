<?php

/**
 * Defines default methods and variables for command parsers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class CommandParser {
	
	/**
	 * Creates a new instance of type CommandParser
	 */
	public function __construct() { }
	
	/**
	 * Parses the given line
	 * @param	string	$line
	 * @param	string	$lineEx
	 */
	abstract public function parse($line, $lineEx, $source = null);
}
?>