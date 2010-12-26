<?php
// imports
require_once(SDIR.'lib/system/irc/Mode.class.php');

/**
 * Represents a mode
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class AbstractMode implements Mode {
	
	/**
	 * Contains a mode argument
	 * @var	string
	 */
	protected $argument = '';
	
	/**
	 * Contains a boolean value
	 * @var	boolean
	 */
	protected static $canHaveArgument = false;
	
	/**
	 * @see	Mode::__construct()
	 */
	public function __construct($argument = '') {
		$this->argument = $argument;
	}
	
	/**
	 * @see	Mode::__toString()
	 */
	public function __toString() {
		return $this->getName();
	}
	
	/**
	 * @see	Mode::getName()
	 */
	public function getName() {
		return str_replace('Mode', '', get_class($this));
	}
	
	/**
	 * @see	Module::canHaveArgument()
	 */
	public static function canHaveArgument() {
		return self::$canHaveArgument;
	}
	
	/**
	 * @see	Module::getArgument
	 */
	public function getArgument() {
		return $this->argument;
	}
}
?>