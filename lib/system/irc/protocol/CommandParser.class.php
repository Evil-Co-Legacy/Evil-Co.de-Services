<?php

/**
 * Defines default methods and variables for command parsers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class CommandParser {
	
	/**
	 * Contains an instance of CommandParser
	 * @var CommandParser
	 */
	protected static $instance = null;
	
	/**
	 * Creates a new instance of type CommandParser
	 */
	protected function __construct() { }
	
	/**
	 * Parses the given line
	 * @param	string	$line
	 * @param	string	$lineEx
	 */
	abstract public function parse($line, $lineEx);
	
	/**
	 * Returnes an instance of CommandParser
	 */
	public static final function getInstance() {
		if (static::$instance === null) {
			static::$instance = new static();
		}
		
		return static::$instance;
	}
}
?>