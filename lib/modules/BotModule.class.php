<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for bots
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class BotModule implements Module {

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
	public function __construct($bot, $trigger = '') {
		$this->bot = $bot;
		$this->trigger = $trigger;
		$this->registerEvents();

		// join channel
		$this->join(Services::getConnection()->getProtocol()->getServiceChannel());
	}

	/**
	 * Returnes the bot reference
	 */
	public function getBot() {
		return $this->bot;
	}

	/**
	 * Handles a line
	 * @param	string	$message
	 * @param	string	$type (Can be 'public' (For channel messages) or 'private' (For notice, msg, etc.))
	 */
	public final function handleLine($user, $target, $message) {
		$found = false;

		foreach($this->commands as $key => $command) {
			if ($this->commands[$key]->matches($message)) {
				$this->commands[$key]->execute($user, $target, $message);
				$found = true;
			}
		}

		if (!$found) {
			// handle help command
			$inputEx = explode(' ', $message);
			if (strtoupper($inputEx[0]) == 'HELP') return $this->generateHelp();

			// send noSuchCommand message
			$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'bot.global.noSuchCommand'));
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
	 * @see	Module::registerEvents()
	 */
	public function registerEvents() {
		// nothing to do here
	}

	/**
	 * Registeres a bot (Sas the module manager that this module is available)
	 */
	public static function registerBot() {
		// TODO: Implement this function
	}

	/**
	 * Redirects all unknown method calls to user object
	 * @param	string	$method
	 * @throws Exception
	 */
	public final function __call($method, $arguments) {
		if (method_exists($this->bot, $method))
			return call_user_func_array(array($this->bot, $method), $arguments);

		throw new Exception("Method '".$method."' does not exist in class ".get_class($this));
	}
}
?>