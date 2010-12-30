<?php
// wcf imports
require_once(SDIR.'lib/modules/Module.class.php');

/**
 * Defines default methods for modules
 
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractModule implements Module {
	
	/**
	 * Contains additional properties for modules
	 *
	 * @var		array<mixed>
	 */
	protected $data = array();
	
	/**
	 * Creates a new instance of AbstractModule
	 *
	 * @param	array<mixed>	$data
	 * @return 	void
	 */
	public function __construct(Array $data) {
		// handle arguments
		$this->data = $data;
		
		// call init methods
		$this->registerEvents();
	}
	
	/**
	 * @see Module::registerEvents()
	 */
	public function registerEvents() {
		// nothing to do here yet
	}
	
	/**
	 * Sets an additional property
	 *
	 * @param	string	$property
	 * @param	mixed	$value
	 * @return	void
	 */
	public final function __set($property, $value) {
		$this->data[$property] = $value;
	}
	
	/**
	 * Returns whether the given property already exists
	 *
	 * @param	string	$property
	 * @return	boolean
	 */
	public final function __isset($property) {
		return (isset($this->data[$property]));
	}
	
	/**
	 * Returns the value of given property
	 *
	 * @param	string	$property
	 * @return	mixed
	 */
	public final function __get($property) {
		if ($this->__isset($property)) return $this->data[$property];
		return null;
	}
}
?>