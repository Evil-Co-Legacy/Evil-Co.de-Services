<?php
/**
 * This is a little php script that makes it easy to ship modules (You'll need to set the ini variable 'phar.readonly' to zero!)
 * @author	Johannes Donath
 * @copyright
 */

// check configuration
@ini_set('phar.readonly', 0);
if (ini_get('phar.readonly')) die("Sorry but you have to set 'phar.readonly' to 0!");

// get arguments
if (!isset($argc)) die("Sorry but you have to execute this script in CLI!");

if ($argc < 2) {
	// print usage information
	die("Usage: ".__FILE__." <moduleDir>");
}

$file = $argv[1];

// validate arguments
if (!is_dir($file)) die("Sorry but the given directory isn't a valid dir. Please correct your input!");

// remove trailing slash if needed
if ($file{(strlen($file) - 1)} == '/') $file = substr($file, 0, (strlen($file) - 1));

// create iterator
$iterator = new RecursiveDirectoryIterator($file);

// validate
if (!$iterator->valid()) die("Sorry but i can't ship an empty module!");

// check for module information file
if (!file_exists($file.'/module.xml'))
	die("Sorry but i can't ship modules without module information file (module.xml)");
else
	echo "I've found a module information file (module.xml)!\nNote that this script will NOT check for correct information or XML-Syntax!\n";

// get phar path
$pharPath = dirname($file).'/'.basename($file).'.phar';

// delete old phars if needed
if (file_exists($pharPath)) {
	echo "WARNING! Deleting old phar archive ...\n";
	unlink($pharPath);
	if (file_exists($pharPath.'.gz')) unlink($pharPath.'.gz');
}

// build phar object
$phar = new Phar($pharPath, 0, $pharPath);
$phar->buildFromIterator(new RecursiveIteratorIterator($iterator), $file);
$phar->compress(Phar::GZ);

// success!
echo "Ok! Your phar file was saved to ".$pharPath."\nNote that there will be an gzipped version of the phar too!";
?>