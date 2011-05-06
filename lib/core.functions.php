<?php
/**
 * Creates autoloader and error handlers
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
// define exception handler
set_exception_handler(array('Services', 'handleException'));

// define error handler
set_error_handler(array('Services', 'handleError'), E_ALL);

// define shutdown method
register_shutdown_function(array('Services', 'destruct'));

// register signals

pcntl_signal(SIGTERM, array('Services', 'signalHandler'));
pcntl_signal(SIGUSR1, array('Services', 'signalHandler'));
pcntl_signal(SIGUSR2, array('Services', 'signalHandler'));
pcntl_signal(SIGHUP, array('Services', 'signalHandler'));

		
/**
 * Autoloads default classes
 *
 * @param	string	$className
 */
function __autoload($className) {
	// autoload utils
	if (file_exists(SDIR.'lib/utils/'.$className.'.class.php')) {
		require_once(SDIR.'lib/utils/'.$className.'.class.php');
		return;
	}
	
	// autoload exceptions
	if (file_exists(SDIR.'lib/system/exception/'.$className.'.class.php')) {
		require_once(SDIR.'lib/system/exception/'.$className.'.class.php');
		return;
	}
}

/**
 * @see Database::escapeString()
 */
function escapeString($str) {
	return Services::getDB()->escapeString($str);
}
?>