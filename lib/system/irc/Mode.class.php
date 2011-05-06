<?php

/**
 * Represents a mode
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Mode {

	/**
	 * Contains the mode char of this mode
	 * @var string(1)
	 */
	protected $modeChar = '';

	/**
	 * Contains the argument for this mode
	 * @var string
	 */
	protected $argument = '';

	/**
	 * Creates a new instance of type Mode
	 * @param	string	$char
	 * @param	string	$argument
	 */
	public function _construct($char, $argument = null) {
		$this->modeChar = $char;
		if ($argument !== null) $this->argument = $argument;
	}

	/**
	 * Returns the argument of this mode
	 * @var string
	 */
	public function getArgument() {
		return $this->argument;
	}

	/**
	 * Converts this mode to string
	 * @var string
	 */
	public function __toString() {
		return $this->modeChar;
	}
}
?>