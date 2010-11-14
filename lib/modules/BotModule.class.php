<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');
require_once(SDIR.'lib/language/LanguageManager.class.php');

/**
 * Defines default methods for bots
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class BotModule extends Module {
	
	/**
	 * Contains the name of this bot (This must defined!)
	 * @var	string
	 */
	protected $botName = 'Bot';
	
	/**
	 * Contains the user type object of this bot
	 * @var	UserType
	 */
	protected $bot = null;
	
	/**
	 * Contains all bound commands for this bot
	 * @var	array<CommandModule>
	 */
	protected $commands = array();
	
	/**
	 * Contains the trigger of this bot (The trigger is used for public channel commands)
	 * @var unknown_type
	 */
	protected $trigger = '';
	
	/**
	 * Creates a new instance of type Bot
	 * @param	UserType	$bot
	 */
	public function __construct(&$bot, $trigger = '') {
		$this->bot = &$bot;
	}
	
	/**
	 * Handles a line
	 * @param	string	$message
	 * @param	string	$type (Can be 'public' (For channel messages) or 'private' (For notice, msg, etc.))
	 */
	public function handleLine(&$user, $message, $type) {
		$found = false;
		
		foreach($this->commands as $key => $command) {
			if ($this->commands[$key]->matches($message)) {
				$this->commands[$key]->execute(&$user, $message);
				$found = true;
			}
		}
		
		if (!$found) {
			$this->sendMessage(&$user, LanguageManager::get($user->languageID, 'bot.global.noSuchCommand'));
		}
	}
	
	/**
	 * Adds a new command to bot
	 * @param	CommandModule	$command
	 */
	public function registerCommand($command) {
		$this->commands[] = $command;
	}
	
	/**
	 * Registeres a bot (Sas the module manager that this module is available)
	 */
	public static function registerBot() {
		// TODO: Implement this function
	}
}
?>