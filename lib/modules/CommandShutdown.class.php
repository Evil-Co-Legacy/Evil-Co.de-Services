<?php
// imports
require_once(SDIR.'lib/modules/CommandModule.class.php');

/**
 * Loads modules via IRC
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class CommandShutdown extends CommandModule {

	/**
	 * @see CommandModule::$originalName
	 */
	public $originalName = 'shutdown';

	/**
	 * @see CommandModule::$neededPermissions
	 */
	public $neededPermissions = 900;

	/**
	 * @see lib/modules/CommandModule::execute()
	 */
	public function execute($user, $target, $message) {
		$messageEx = explode(' ', $message);
		unset($messageEx[0]);
		
		// quit with a nice message
		if (count($messageEx)) {
			Services::getModuleManager()->shutdown(implode(' ', $messageEx));
		}
		else {
			Services::getModuleManager()->shutdown();
		}
		exit;
	}
}
?>