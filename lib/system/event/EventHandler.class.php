<?php

/**
 * Manages events
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class EventHandler {
	
	/**
	 * Contains all events
	 *
	 * @var	array<array>
	 */
	protected $events = array();
	
	/**
	 * Registers a new event
	 *
	 * @param	object	$class
	 * @param	string	$targetClass
	 * @param	string	$targetEvent
	 * @return	void
	 */
	public function registerEvent($callback, $targetClass, $targetEvent) {
		// validate callback
		if (!is_callable($callback)) throw new RecoverableException("Passed invalid callback to registerEvent()");
		
		// create arrays
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))] = array();
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent] = array();
		
		// add event
		$this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent][] = $callback;
		
		// send debug line
		Services::getLogger()->debug("Registered event ".$targetEvent."@".(is_string($targetClass) ? $targetClass : get_class($targetClass)));
	}
	
	/**
	 * Fires an event
	 *
	 * @param	object	$class
	 * @param	string	$eventName
	 * @return	void
	 */
	public function fire($eventObj, $eventName, $data = array()) {
		// log
		Services::getLogger()->debug("Firing event ".$eventName."@".(is_string($eventObj) ? $eventObj : get_class($eventObj)));
		
		// get parent classes
		$familyTree = array();
		$member = $className = (is_object($eventObj) ? get_class($eventObj) : $eventObj);
		
		while ($member != false) {
			$familyTree[] = $member;
			$member = get_parent_class($member);
		}
		
		// add interfaces
		// this is a little workaround ...
		$reflection = new ReflectionClass($className);
		$familyTree = array_merge($familyTree, $reflection->getInterfaceNames());

		foreach ($familyTree as $member) {
			if (isset($this->events[$member])) {
				$actions = $this->events[$member];
				
				if (isset($actions[$eventName]) and count($actions[$eventName]) > 0) {                        
					foreach ($actions[$eventName] as $action) {
						call_user_func_array($action, $data);
					}
				}
			}
		}
		
		// send debug line
		Services::getLogger()->debug("Fired event ".$eventName."@".(is_string($eventObj) ? $eventObj : get_class($eventObj)));
	}
}
?>