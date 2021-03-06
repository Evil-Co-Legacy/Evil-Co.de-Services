<?php
/**
 * Creates autoloader and error handlers
 *
 * @author	Johannes Donath, Tim Düsterhus
 * @copyright	2010 - 2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

// define exception handler
set_exception_handler(array('Services', 'handleException'));

// define error handler
set_error_handler(array('Services', 'handleError'), E_ALL | E_NOTICE | E_STRICT | E_DEPRECATED);

// define shutdown method
register_shutdown_function(array('Services', 'destruct'));

// register assert handler
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);
assert_options(ASSERT_CALLBACK, array('Services', 'handleAssertion'));

// register signals
if (function_exists('pcntl_signal')) {
	pcntl_signal(SIGTERM, array('Services', 'signalHandler'));
	pcntl_signal(SIGUSR1, array('Services', 'signalHandler'));
	pcntl_signal(SIGUSR2, array('Services', 'signalHandler'));
	pcntl_signal(SIGHUP, array('Services', 'signalHandler'));
}

// Zend loader
require_once('Zend/Loader/Autoloader.php');
Zend_Loader_Autoloader::getInstance();

/**
 * Autoloads default classes
 *
 * @param	string	$className
 */
spl_autoload_register(function ($className) {
	// autoload utils
	if (file_exists(DIR.'lib/utils/'.$className.'.class.php')) {
		require_once(DIR.'lib/utils/'.$className.'.class.php');
		return;
	}

	// autoload exceptions
	if (file_exists(DIR.'lib/system/exception/'.$className.'.class.php')) {
		require_once(DIR.'lib/system/exception/'.$className.'.class.php');
		return;
	}
});
?>