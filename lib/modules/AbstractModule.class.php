<?php
// wcf imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for modules
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractModule implements Module {
	
	/**
	 * Contains additional properties for modules
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Creates a new instance of AbstractModule
	 * @param	array<mixed>	$data
	 * @return void
	 */
	public function __construct($data) {
		$this->data = $data;
	}
	
	/**
	 * @see Module::registerEvents()
	 */
	public function registerEvents() {
		// nothing to do here yet
	}
	
	/**
	 * Sets an additional property
	 * @param	string	$property
	 * @param	mixed	$value
	 */
	public final function __set($property, $value) {
		$this->data[$property] = $value;
	}
	
	/**
	 * Returnes true if the given property already exists
	 * @param	string	$property
	 */
	public final function __isset($property) {
		return (isset($this->data[$property]));
	}
	
	/**
	 * Returnes the value of given property
	 * @param	string	$property
	 */
	public final function __get($property) {
		if ($this->__isset($property)) return $this->data[$property];
		return null;
	}
}
?>