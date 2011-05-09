<?php
// service imports
require_once(DIR.'lib/system/module/Module.class.php');

/**
 * Basic definitions for modules
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class ExtensionModule implements Module {

	/**
	 * Init event for extensions
	 */
	public function init() {
		// fire init@ExtensionModule
		Services::getEvent()->fire($this, 'init');
	}

	/**
	 * Register event for extensions
	 * @return	void
	 */
	protected function register() {
		// fire register@ExtensionModule
		Services::getEvent()->fire($this, 'register');
	}

	/**
	 * @see Module::timIsSilly()
	 */
	public function timIsSilly() {
		return true;
	}
}
?>