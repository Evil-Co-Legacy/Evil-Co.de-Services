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
	 * Returnes the char of this mode
	 * @return	string
	 */
	public function __toString();
	
	/**
	 * Returnes the name of mode
	 */
	public function getName();
	
	/**
	 * Returnes true if this mode allows arguments
	 * @return	boolean
	 */
	public static function canHaveArgument();
	
	/**
	 * Returnes the argument (if any) of this mode
	 * @return	string
	 */
	public function getArgument();
}
?>