<?php
// defines
define('SDIR', dirname(__FILE__).'/');

// write pidfile
file_put_contents("services.pid", getmypid());

// imports
require_once(SDIR.'lib/system/Services.class.php');

// start core
new Services();
?>