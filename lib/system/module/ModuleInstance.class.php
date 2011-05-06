<?php

/**
 * Represents a module instance
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ModuleInstance extends DevNull {
	
	/**
	 * Type representation for commands
	 * @var integer
	 */
	const TYPE_COMMAND = 9001;
	
	/**
	 * Type repesentation for extensions
	 * @var integer
	 */
	const TYPE_EXTENSION = 1337;
	
	/**
	 * Type representation for bots
	 * @var integer
	 */
	const TYPE_BOT = 42;
	
	/**
	 * Type representation for dummy modules
	 * @var integer
	 */
	const TYPE_DUMMY = 21;
	
	/**
	 * Type representation for unknown modules
	 * @var integer
	 */
	const TYPE_UNKNOWN = 0;
	
	/**
	 * Contains an module instance
	 * @var Module
	 */
	protected $instance = null;
	
	/**
	 * Contains information about modules
	 * @var LoadedModule
	 */
	protected $moduleInformation = null;
	
	/**
	 * Contains the module type
	 * @var integer
	 */
	public $type = 0;
	
	/**
	 * Creates a new instance of type ModuleInstance
	 * @param	LoadedModule		$moduleInformation
	 */
	public function __construct(LoadedModule $moduleInformation) {
		// handle arguments
		$this->moduleInformation = $moduleInformation;
		
		// create instance
		$this->instance = $this->moduleInformation->createInstance();
		
		// detect module type
		if ($this->instance instanceof BotModule)
			$this->type = self::TYPE_BOT;
		elseif ($this->instance instanceof CommandModule)
			$this->type = self::TYPE_COMMAND;
		elseif ($this->instance instanceof ExtensionModule)
			$this->type = self::TYPE_EXTENSION;
		else
			$this->type = self::TYPE_DUMMY;
	}
	
	/**
	 * Handles external calls ...
	 * @param	string	$methodName
	 * @param	array	$argumentList
	 * @return mixed
	 * @throws RecoverableException
	 * @throws SuccessException
	 */
	public function __call($methodName, $argumentList) {
		if (!method_exists($this->moduleInformation, $methodName)) return call_user_func_array(array($this->moduleInformation, $methodName), $argumentList);
		if (!method_exists($this->instance, $methodName)) return call_user_func_array(array($this->instance, $methodName), $argumentList);
		
		// whoops
		throw new RecoverableException("Called to undefined method '%s' on class %s", $methodName, get_class($this));
		
		// what the?
		throw new SuccessException("What the ...?! What's going on with your system?");
	}
}
?>