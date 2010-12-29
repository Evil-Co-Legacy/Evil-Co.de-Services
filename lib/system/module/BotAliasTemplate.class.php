<?php

/**
 * This is a meta class that redirects all static calls to bot modules
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class BotAliasTemplate {
	
	/**
	 * Redirects all static calls to bot
	 * @param	string	$method
	 * @param	array	$arguments
	 */
	public static function __callStatic($method, $arguments) {
		call_user_func_array(array(Services::getModuleManager()->lookupModule(get_class($this)), $method), $arguments);
	}
}
?>