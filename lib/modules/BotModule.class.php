<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for bots
 *
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class BotModule implements Module {

	/**
	 * Contains the name of this bot (This must defined!)
	 *
	 * @var	string
	 */
	protected $botName = 'Bot';

	/**
	 * Contains the user type object of this bot
	 *
	 * @var	UserType
	 */
	protected $bot = null;

	/**
	 * Contains all bound commands for this bot
	 *
	 * @var	array<CommandModule>
	 */
	protected $commands = array();

	/**
	 * Contains the trigger of this bot (The trigger is used for public channel commands)
	 *
	 * @var	string
	 */
	protected $trigger = '';

	/**
	 * Creates a new instance of type Bot
	 *
	 * @param	UserType	$bot
	 */
	public function __construct(UserType $bot, $trigger = '') {
		$this->bot = $bot;
		$this->trigger = $trigger;
		$this->registerEvents();

		// join channel
		$this->join(Services::getConnection()->getProtocol()->getServiceChannel());
	}

	/**
	 * Returnes the bot reference
	 *
	 * @return	UserType
	 */
	public function getBot() {
		return $this->bot;
	}

	/**
	 * Handles a line
	 *
	 * @param	string	$user
	 * @param	string	$target
	 * @param	string	$message
	 * @return	void
	 */
	public final function handleLine($user, $target, $message) {
		$found = false;

		foreach($this->commands as $key => $command) {
			if ($this->commands[$key]->matches($message) and $this->getPermissions($user, $command->neededPermissions)) {
				$this->commands[$key]->execute($user, $target, $message);
				$found = true;
			} elseif ($this->commands[$key]->matches($message) and !$this->getPermissions($user, $command->neededPermissions)) {
				$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->language, 'bot.global.permissionDenied'));
				$found = true;
			}
		}

		if (!$found) {
			// handle help command
			$inputEx = explode(' ', $message);
			if (strtoupper($inputEx[0]) == 'HELP' and count($this->commands)) return $this->generateHelp($user, $target, $message);

			// send noSuchCommand message
			$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'bot.global.noSuchCommand'));
		}
	}

	/**
	 * Adds a new command to bot
	 *
	 * @param	CommandModule	$command
	 * @return	void
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
	 * Registers a bot (Sas the module manager that this module is available)
	 *
	 * @return	void
	 */
	public static function registerBot() {
		// TODO: Implement this function
	}

	/**
	 * Spits out the help
	 *
	 * @param	UserType	$user
	 * @param	string		$target
	 * @param	string		$message
	 * @return	void
	 */
	public function generateHelp(UserType $user, $target, $message) {
		$inputEx = explode(' ', $message);

		if (!isset($inputEx[1])) {
			$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'bot.global.help'));
			$longestCommandName = 0;

			foreach($this->commands as $key => $command) {
				if ($command->appearInHelp and strlen($command->commandName) > $longestCommandName) $longestCommandName = strlen($command->commandName);
			}

			foreach($this->commands as $key => $command) {
				if ($command->appearInHelp and $this->getPermissions($user, $command->neededPermissions)) {
					$this->sendMessage($user->getUuid(), str_pad($command->commandName, ($longestCommandName + 3)).Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName));
				}
			}
		} else {
			foreach($this->commands as $key => $command) {
				if ($command->commandName == strtoupper($inputEx[1])) {
					$this->sendMessage($user->getUuid(), $command->commandName);
					$this->sendMessage($user->getUuid(), COLOR_BOLD.COLOR_UNDERLINE."Syntax:".COLOR_UNDERLINE.COLOR_BOLD." ".Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName.'.syntaxHint'));
					if ($command->neededPermissions > 0) $this->sendMessage($user->getUuid(), COLOR_BOLD.Services::getLanguage()->get($user->languageID, 'bot.global.neededPermissions').COLOR_BOLD." ".$command->neededPermissions);
					if (Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName.'.description') != 'command.'.$command->originalName.'.description') $this->sendMessage($user->getUuid(), 'command.'.$command->originalName.'.description');
					return;
				}
			}

			$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'bot.global.noSuchCommand'));
		}
	}

	/**
	 * Checks for needed permissions and returnes true if correct permissions are set
	 *
	 * @param 	UserType	$user
	 * @param	integer		$neededPermissions
	 * @return	boolean
	 */
	public function getPermissions(UserType $user, $neededPermissions) {
		// when 0 always is okay
		if ($neededPermissions == 0) return true;

		// handle empty account names
		//if ($user->accountname === null or $user->accountname != '') return false;

		// check for correct level
		if (call_user_func(array(Services::getModuleManager()->lookupModule('AuthServ'), 'getAccessLevel'), $user->accountname) >= $neededPermissions) return true;

		return false;
	}

	/**
	 * Returnes bot's trigger
	 *
	 * @return	string
	 */
	public final function getTrigger() {
		return $this->trigger;
	}

	/**
	 * Redirects all unknown method calls to user object
	 *
	 * @param	string	$method
	 * @return	void
	 * @throws 	Exception
	 */
	public final function __call($method, $arguments) {
		if (method_exists($this->bot, $method))
			return call_user_func_array(array($this->bot, $method), $arguments);

		throw new Exception("Method '".$method."' does not exist in class ".get_class($this));
	}
}
?>