<?php
// define exception handler
set_exception_handler(array('Services', 'handleException'));

// define error handler
set_error_handler(array('Services', 'handleError'), E_ALL);

/**
 * Autoloads default classes
 * @param	string	$className
 */
function __autoload($className) {
	// autoload utils
	if (file_exists(SDIR.'lib/utils/'.$className.'.class.php')) {
		require_once(SDIR.'lib/utils/'.$className.'.class.php');
		return;
	}
}

/**
 * @see Database::escapeString()
 * @param	string	$str
 */
function escapeString($str) {
	return Services::getDB()->escapeString($str);
}
?>