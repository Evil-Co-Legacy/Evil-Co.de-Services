<?php
// imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for commands
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class CommandModule implements Module {

	/**
	 * Contains the bot instance
	 * @var	BotModule
	 */
	protected $bot = null;

	/**
	 * Contains the name of bound command
	 * Note: This must be uppercase!
	 * @var	string
	 */
	public $commandName = '';

	/**
	 * Contains the original command name
	 * Note: This will used in language system
	 * @var string
	 */
	public $originalName = 'Command';

	/**
	 * If this is set to false we'll hide the command in HELP command
	 * @var boolean
	 */
	public $appearInHelp = true;

	/**
	 * Contains the needed level
	 * @var integer
	 */
	public $neededPermissions = 0;

	/**
	 * Creates a new instance of type Command
	 * @param	BotModule	$bot
	 * @param	string		$name
	 */
	public function __construct(&$bot, $name, $appearInHelp = true) {
		$this->commandName = strtoupper($name);
		$this->bot = &$bot;
		$this->appearInHelp = $appearInHelp;
		$this->registerEvents();
	}

	/**
	 * Executes the command
	 * @param	UserObject	$user
	 * @param	string		$message
	 */
	abstract public function execute($user, $target, $message);

	/**
	 * Returnes true if the given line matches command
	 * @param	string	$input
	 * @return	boolean
	 */
	public final function matches($command) {
		if (!empty($this->commandName) and strtoupper($command) == $this->commandName) return true;

		// TODO: Add more match types here

		return false;
	}

	/**
	 * @see	Module::registerEvents()
	 */
	public function registerEvents() {
		// nothing to do here
	}

	/**
	 * Registers the command
	 */
	public static final function registerCommand() {
		// TODO: Implement this method ...
	}
}
?>