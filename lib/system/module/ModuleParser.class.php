<?php

/**
 * Parses a module for using in our system
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @version		2.1.0-namespace
 */
class ModuleParser {
	
	/**
	 * Contains the name of the dir (relative from SDIR) where cache files should stored
	 * @var string
	 */
	const PARSER_DIR = 'cache/';
	
	/**
	 * Contains already loaded namespaces
	 * Note: This will only used to find existing namespaces
	 * @var array<string>
	 */
	protected static $knownNamespaces = array();
	
	/**
	 * Parses the given module file and creates a copy
	 * @param	string	$filename
	 * @param	array	$knownNamespaces This array should formated like this:
	 * array(
	 * 	[namespace] => 'class'
	 * 	[namespace2] => 'class2'
	 * )
	 */
	public function parseModule($filename, $knownNamespaces) {
		// generate namespace
		$namespace = self::generateNamespaceID();
		
		// read file
		$file = file_get_contents($filename);
		
		// add namespace definition
		$namespaceSection = "namespace ".$namespace.";\n\n/** known namespaces **/\n";
		
		// add known namespaces
		foreach($knownNamespaces as $namespaceName => $class) {
			$namespaceSection .= "use ".$namespaceName.";";
			$file = str_replace($class, $namespaceName."\\".$class, $file);
		}
		
		// add namespace section
		$file = str_replace("<?php\n", "<?php\n".$namespaceSection."\n", $file);
		
		// get filename
		$newFile = basename($filename);
		
		// write parsed file
		file_put_contents(SDIR.self::PARSER_DIR.$namespace.'/'.$newFile, $file);
		
		// return namespace
		return $namespace;
	}
	
	/**
	 * Generates an unique namespace ID
	 */
	protected static function generateNamespaceID() {
		// loop while generating a new ID
		do {
			$namespaceID = "Ox".dechex(((time() + count(self::$knownNamespaces)) * 10000)); 
		} while (in_array($namespaceID, self::$knownNamespaces));
		
		// add to list
		self::$knownNamespaces[] = $namespaceID;
		
		return $namespaceID;
	}
}
?>