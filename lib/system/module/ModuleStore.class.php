<?php

/**
 * Saves module information to database
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleStore {

	/**
	 * Contains an instance of this type for factory pattern
	 * @var ModuleStore
	 */
	protected static $instance = null;

	/**
	 * Contains all stored modules
	 * @var ArrayIterator
	 */
	protected $store = null;

	/**
	 * Constructor is marked as protected for factory pattern
	 */
	protected function __construct() {
		$data = array();

		$sql = "SELECT
				*
			FROM
				module_instance";
		$result = Services::getDB()->fetchAll($sql);

		foreach($result as $row) {
			$data[] = new LoadedModule($row->moduleName, LoadedModule::LOAD_STORE);
		}

		// create iterator
		$this->store = $data;
	}

	/**
	 * Returnes an instance of this type
	 * @return ModuleStore
	 */
	public static function getInstance() {
		// create instance if needed
		if (self::$instance === null) self::$instance = new ModuleStore();

		return self::$instance;
	}

	/**
	 * Returnes a list of stored modules
	 * @return array
	 */
	public function getModuleList() {
		return $this->store;
	}

	/**
	 * Removes a module from
	 * @param unknown_type $moduleName
	 */
	public function remove($moduleName) {
		foreach($this->store as $key => $module) {
			if ($module->moduleName == $moduleName) unset($this->store[$key]);
		}
	}
}
?>