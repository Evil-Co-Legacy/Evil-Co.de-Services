<?php
// imports
require_once(SDIR.'lib/modules/AbstractModule.class.php');

/**
 * Defines default methods for bots
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class BotModule extends AbstractModule {

	/**
	 * Contains the name of this bot (This must defined!)
	 *
	 * @var		string
	 */
	protected $botName = 'Bot';

	/**
	 * Contains the user type object of this bot
	 *
	 * @var		UserType
	 */
	protected $bot = null;

	/**
	 * Contains all bound commands for this bot
	 *
	 * @var		array<CommandModule>
	 */
	protected $commands = array();

	/**
	 * Contains the trigger of this bot (The trigger is used for public channel commands)
	 *
	 * @var		string
	 */
	protected $trigger = '';

	/**
	 * Creates a new instance of type Bot
	 *
	 * @param	UserType		$bot
	 * @param	string		$trigger
	 * @param	array<mixed>	$data
	 */
	public function __construct(UserType $bot, $trigger = '', Array $data = array()) {
		parent::__construct($data);
		
		// handle additional parameters
		$this->bot = $bot;
		$this->trigger = $trigger;
		$this->data = $data;

		// join channel
		$this->join(Services::getConnection()->getProtocol()->getServiceChannel());
	}

	/**
	 * Returns the bot reference
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

		// loop through commands array
		foreach($this->commands as $key => $command) {
			if ($this->commands[$key]->matches($message)) {
				if ($this->getPermissions($user, $command->neededPermissions)) {
					// catch UserExceptions
					try {
						$this->commands[$key]->execute($user, $target, $message);
					} catch (UserException $ex) {
						$ex->sendMessage();
						if (defined('DEBUG')) $ex->sendDebugLog();
					}
				} else {
					// send permission denied message
					$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->language, 'bot.global.permissionDenied'));
				}
				
				$found = true;
			}
		}

		// command not found
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
	public function bind($command) {
		$this->commands[] = $command;
	}
	
	/**
	 * Unbinds a command
	 * 
	 * @param	string	$commandName
	 * @return 	void
	 */
	public function unbind($commandName) {
		foreach($this->commands as $key => $command) {
			if ($command->commandName == $commandName) unset($this->commands[$key]);
		}
	}

	/**
	 * Registers a bot (Sas the module manager that this module is available)
	 *
	 * @deprecated
	 * @return	void
	 */
	public static function registerBot();

	/**
	 * Spits out the help
	 *
	 * @param	UserType	$user
	 * @param	string		$target
	 * @param	string		$message
	 * @return	void
	 * @todo 	This should be an external module
	 */
	public function generateHelp(UserType $user, $target, $message) {
		// split message
		$inputEx = explode(' ', $message);

		// check for index 1
		if (!isset($inputEx[1])) {
			// get longest command
			$longestCommandName = 0;

			foreach($this->commands as $key => $command) {
				if ($command->appearInHelp and strlen($command->commandName) > $longestCommandName) $longestCommandName = strlen($command->commandName);
			}
			
			// create header
			// TODO: Add a table for this
			$this->sendMessage($user->getUuid(), MessageParser::addColorCode(COLOR_UNDERLINE, MessageParser::addColorCode(COLOR_BOLD, Services::getLanguage()->get($user->languageID, 'bot.global.help', $this->getNick()))));

			// send command help
			foreach($this->commands as $key => $command) {
				if ($command->appearInHelp and $this->getPermissions($user, $command->neededPermissions)) {
					$this->sendMessage($user->getUuid(), str_pad($command->commandName, ($longestCommandName + 3)).Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName));
				}
			}
		} else {
			// loop through commands and find searched command
			foreach($this->commands as $key => $command) {
				if ($command->commandName == strtoupper($inputEx[1])) {
					// send command name
					$this->sendMessage($user->getUuid(), $command->commandName);
					
					// send syntax help
					$this->sendMessage($user->getUuid(), MessageParser::addColorCode(COLOR_UNDERLINE, MessageParser::addColorCode(COLOR_BOLD, "Syntax:"))." ".Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName.'.syntaxHint'));
					
					// add needed permissions if needed
					if ($command->neededPermissions > 0) $this->sendMessage($user->getUuid(), MessageParser::addColorCode(COLOR_UNDERLINE, MessageParser::addColorCode(COLOR_BOLD, Services::getLanguage()->get($user->languageID, 'bot.global.neededPermissions')))." ".$command->neededPermissions);
					
					// add description
					if (Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName.'.description') != 'command.'.$command->originalName.'.description') {
						// split message at newlines
						$descriptionArray = explode("\n", Services::getLanguage()->get($user->languageID, 'command.'.$command->originalName.'.description'));
						
						// send each line to user
						foreach($descriptionArray as $description) {
							$this->sendMessage($user->getUuid(), $description);
						}
					}
					return;
				}
			}

			// send no such command message
			$this->sendMessage($user->getUuid(), Services::getLanguage()->get($user->languageID, 'bot.global.noSuchCommand'));
		}
	}

	/**
	 * Checks for needed permissions and returns true if correct permissions are set
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
	 * Returns the bot's trigger
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
	 * @throws 	RecoverableException
	 */
	public final function __call($method, $arguments) {
		if (method_exists($this->bot, $method))
			return call_user_func_array(array($this->bot, $method), $arguments);

		throw new RecoverableException("Method '".$method."' does not exist in class ".get_class($this));
	}
}
?>