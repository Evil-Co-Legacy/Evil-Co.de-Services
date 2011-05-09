<?php
// services imports
require_once(DIR.'lib/system/module/ModuleCacheManager.class.php');

/**
 * Represents a loaded module
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class LoadedModule {

	/**
	 * Set if no modules should loaded
	 * @var integer
	 */
	const LOAD_NONE = 0x0;

	/**
	 * Set if module loaded manually by user
	 * @var integer
	 */
	const LOAD_MANUAL = 0xF4240;

	/**
	 * Set if module loaded from store information
	 * @var integer
	 */
	const LOAD_STORE = 0x1E8480;

	/**
	 * Set if module loaded as dummy module (Just there ...)
	 * @var integer
	 */
	const LOAD_DUMMY = 0x3D0900;

	/**
	 * Contains the name of this module
	 * @var string
	 */
	public $moduleName = '';

	/**
	 * Contains the load type
	 * @var integer
	 */
	public $loadType = 0x0;

	/**
	 * Contains the name of the cached class
	 * @var string
	 */
	protected $cacheClassName = '';
	
	/**
	 * Contains additional strings for module hash
	 * @var string
	 */
	protected $hash = '';
	
	/**
	 * Contains a list of required modules
	 * @var	array<string>
	 */
	public $dependencies = array();

	/**
	 * Creates a new instance of type LoadedModule
	 * @param	string	$moduleName
	 * @param	integer	$loadType
	 */
	public function __construct($moduleName, $loadType = self::LOAD_DUMMY) {
		// handle arguments
		$this->moduleName = $moduleName;
		$this->loadType = $loadType;
		$this->hash = time();

		// load or generate cache
		if ($this->loadType != self::LOAD_DUMMY) $this->cacheClassName = ModuleCacheManager::getInstance()->getCache($moduleName, $this);
	}

	/**
	 * Creates a new instance of module
	 * @return Module
	 */
	public function createInstance() {
		return new $this->cacheClassName();
	}

	/**
	 * Returnes the complete class name of module cache
	 * @return		string
	 */
	public function getCacheClassName() {
		return $this->cacheClassName;
	}
	
	/**
	 * Returnes an unique hash for this module
	 * @return string
	 */
	public function getModuleHash() {
		return 'Module_'.$this->hash.'_'.spl_object_hash($this);
	}
}
?>