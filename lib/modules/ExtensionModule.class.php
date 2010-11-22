<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for extensions
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class ExtensionModule extends Module {
	
	/**
	 * Creates a new instance of type Command
	 * @param	BotModule	$bot
	 * @param	string		$name
	 */
	public function __construct() {
		$this->registerEvents();
	}
	
	/**
	 * @see	Module::registerEvents()
	 */
	public function registerEvents() {
		// nothing to do here
	}
}
?>