<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for commands
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class Command extends Module {
	
	/**
	 * Contains the bot instance
	 * @var	Bot
	 */
	protected $bot = null;
	
	/**
	 * Creates a new instance of type Command
	 * @param	Bot	$bot
	 */
	public function __construct(&$bot) {
		$this->bot = &$bot;
	}
	
	/**
	 * Executes the command
	 * @param	UserObject	$user
	 * @param	string		$message
	 */
	abstract public function execute($user, $message);
	
	/**
	 * Registers the command
	 */
	public final function registerCommand() {
		// TODO: Implement this method ...
	}
}
?>