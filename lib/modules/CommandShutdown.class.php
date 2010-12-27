<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Loads modules via IRC
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class CommandLoadModule extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'loadModule';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 900;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		exit;
	}
}
?>