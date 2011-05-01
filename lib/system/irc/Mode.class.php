<?php

/**
 * Represents a mode
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
interface Mode {
	
	/**
	 * Creates a new instance of type Mode
	 * @param	string	$argument
	 */
	public function __construct($argument = '');
	
	/**
	 * Returns the char of this mode
	 * @return	string
	 */
	public function __toString();
	
	/**
	 * Returns the name of mode
	 */
	public function getName();
	
	/**
	 * Returns true if this mode allows arguments
	 * @return	boolean
	 */
	public static function canHaveArgument();
	
	/**
	 * Returns the argument (if any) of this mode
	 * @return	string
	 */
	public function getArgument();
}
?>