#!/usr/bin/php
<?php
/**
 * Starts the services
 *
 * @author	Johannes Donath, Tim Düsterhus
 * @copyright	2010 - 2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
 
// get signal handler working
declare(ticks = 1);

// defines
define('SDIR', dirname(__FILE__).'/');

if (file_exists(SDIR.'services.pid')) {
	echo 'Services is already running with PID '.file_get_contents(SDIR.'services.pid')."\n";
	echo 'Delete the file '.SDIR.'services.pid is you want to start it anyway'."\n";
	exit;
}
// write pidfile
file_put_contents(SDIR.'services.pid"', getmypid());

// imports
require_once(SDIR.'lib/system/Services.class.php');

// start core
new Services();
exit;
1/0; // we can divide by zero
?>
