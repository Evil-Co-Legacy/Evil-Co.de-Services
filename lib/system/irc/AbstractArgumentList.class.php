<?php
// imports
require_once(SDIR.'lib/system/irc/ArgumentList.class.php');

/**
 * Defines default methods and properties for argument lists
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractArgumentList implements ArgumentList, Iterator {

	/**
	 * Contains a list of arguments
	 *
	 * @var array<mixed>
	 */
	protected $argumentList = array();

	/**
	 * Points to the current argument
	 *
	 * @var integer
	 */
	protected $argumentPointer = 0;

	/**
	 * Contains the source for modes
	 *
	 * @var string
	 */
	protected $modeSource = '';

	/**
	 * @see ArgumentList::__construct($argumentString)
	 */
	public function __construct($modeSource, $argumentString) {
		// handle arguments
		$this->modeSource = $modeSource;

		$argumentString = $this->stripDisallowedChars($argumentString);

		// parse arguments
		$this->parseArgumentString($argumentString);
	}

	/**
	 * Returns the argument at given index
	 *
	 * @param	integer	$index
	 * @return	string
	 */
	public function getArgument($index) {
		// return an empty string on error
		if (!isset($this->argumentList[$index])) return "";

		// return argument
		return $this->argumentList[$index];
	}

	/**
	 * @see ArgumentList::parseArgumentString()
	 */
	protected function parseArgumentString($argumentString) {
		// get variables
		$stringArray = explode(' ', $argumentString);
		$activeArgumentIndex = 1;

		for($i = 0; $i < strlen($stringArray[0]); $i++) {
			if (call_user_func(array($this->modeSource, 'hasArgument'), $stringArray[0]{$i})) {
				// get argument
				$this->argumentList[$i] = $stringArray[$activeArgumentIndex];
				$activeArgumentIndex++;
			} else
				$this->argumentList[$i] = '';
		}
	}

	/**
	 * Strips disallowed chars from argument string
	 *
	 * @param	string	$argumentString
	 * @return	string
	 */
	protected function stripDisallowedChars($argumentString) {
		$argumentString = str_replace('+', '', $argumentString); // replace
		$argumentString = preg_replace('~-[A-Z]+~i', '', $argumentString);
		return $argumentString;
	}

	// ITERATOR METHODS
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->argumentPointer = 0;
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->argumentList[$this->argumentPointer];
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->argumentPointer;
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		$this->argumentPointer++;
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		return (isset($this->argumentList[$this->argumentPointer]));
	}
}
?>