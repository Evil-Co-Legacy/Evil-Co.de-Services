<?php
/**
 * Manages the XML configuration
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class Configuration {
	
	/**
	 * Contains all configuration variables
	 *
	 * @var	array<mixed>
	 */
	protected $configuration = array();
	
	/**
	 * Creates a new instance of type Configuration
	 */
	public function __construct() {
		if (!file_exists(SDIR.'config/config.xml')) throw new Exception("Startup error: config.xml not found in ".SDIR."config/! Aborted start!");
		
		// load xml
		$xml = new XML(SDIR.'config/config.xml');
		
		// get element array
		$data = $xml->getElementTree('config');
		
		// call loop
		$this->readConfiguration($this->configuration, $data);
	}
	
	/**
	 * Loops through array
	 *
	 * @param	array	$array
	 * @param	array	$data
	 * @return	void
	 */
	protected function readConfiguration(&$array, $data) {
		// start loop
		foreach($data['children'] as $key => $child) {
			// handle elements with children
			if (isset($child['children']) and count($child['children'])) {
				$this->readConfiguration($array[$child['name']], $child);
				continue;
			}
			
			if (!isset($child['cdata'])) continue;
			
			$array[$child['name']] = $child['cdata'];
		}
	}
	
	/**
	 * Returnes a configuration variable
	 *
	 * @param	string	$name
	 * @return	mixed
	 */
	public function get($name) {
		if (isset($this->configuration[$name])) return $this->configuration[$name];
		return null;
	}
}
?>