<?php

/**
 * Defines default methods for argument lists
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ArgumentList {
	
	/**
	 * Creates a new instance of type ArgumentList
	 * @param	string	$argumentString
	 */
	public function __construct($modeSource, $argumentString);
	
	/**
	 * Parses an argument list
	 * @param	string	$argumentString
	 */
	protected function parseArgumentString($argumentString);
	
	/**
	 * Returnes the argument for given index
	 * @param	integer	$index
	 */
	public function getArgument($index);
}
?>