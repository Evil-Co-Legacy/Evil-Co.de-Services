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
	public function registerEvent($class, $method, $targetClass, $targetEvent) {
		// create arrays
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))] = array();
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent] = array();
		
		// add event
		$this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent][] = array('class' => $class, 'method' => $method);
		
		// send debug line
		Services::getLog()->debug("Registered event ".$targetEvent."@".(is_string($targetClass) ? $targetClass : get_class($targetClass))." with callback ".(is_string($class) ? $class : get_class($class))."::".$method);
	}
	
	/**
	 * Fires an event
	 *
	 * @param	object	$class
	 * @param	string	$eventName
	 * @return	void
	 */
	public function fire($eventObj, $eventName, $data = array()) {
		// get parent classes
		$familyTree = array();
		$member = (is_object($eventObj) ? get_class($eventObj) : $eventObj);
		while ($member != false) {
			$familyTree[] = $member;
			$member = get_parent_class($member);
		}

		foreach ($familyTree as $member) {
			if (isset($this->events[$member])) {
				$actions = $this->events[$member];
				if (isset($actions[$eventName]) and count($actions[$eventName]) > 0) {                        
					foreach ($actions[$eventName] as $action) {
						$action['class']->{$action['method']}($data);
					}
				}
			}
		}
		
		// send debug line
		Services::getLog()->debug("Fired event ".$eventName."@".(is_string($eventObj) ? $eventObj : get_class($eventObj)));
	}
}
?>