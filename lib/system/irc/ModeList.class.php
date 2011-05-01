<?php

/**
 * Manages modes for channels or users
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
interface ModeList {
	
	/**
	 * Creates a new instance of type ModeList
	 * @param	string	$modeString
	 */
	public function __construct($modeString = '+');
	
	/**
	 * Sets a new mode
	 * @param	string	$mode
	 * @param	string	$argument
	 */
	public function setMode($mode, $argument = null);
	
	/**
	 * Returns true if the given mode is set
	 * @param	string	$mode
	 * @return	boolean
	 */
	public function hasMode($mode);
	
	/**
	 * Returns the argument for the given mode
	 * Note: Returns false if the mode can't have an argument
	 * @param	string	$mode
	 * @return	mixed
	 */
	public function getModeArgument($mode);
}
?>