<?php
/**
 * Starts the services
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
 
// get signal handler working
declare(ticks = 1);

// defines
define('SDIR', dirname(__FILE__).'/');

// write pidfile
file_put_contents("services.pid", getmypid());

// imports
require_once(SDIR.'lib/system/Services.class.php');

// start core
new Services();
exit;
1/0; // we can divide by zero
?>