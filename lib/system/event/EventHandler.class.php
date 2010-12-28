<?php

/**
 * Manages events
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class EventHandler {
	
	/**
	 * Contains all events
	 * @var	array
	 */
	protected $events = array();
	
	/**
	 * Registeres a new event
	 * @param	object	$class
	 * @param	string	$targetClass
	 * @param	string	$targetEvent
	 */
	public function registerEvent($class, $method, $targetClass, $targetEvent) {
		// create arrays
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))] = array();
		if (!isset($this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent])) $this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent] = array();
		
		// ad event
		$this->events[(is_string($targetClass) ? $targetClass : get_class($targetClass))][$targetEvent][] = array('class' => $class, 'method' => $method);
	}
	
	/**
	 * Fires an event
	 * @param	object	$class
	 * @param	string	$eventName
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
				Services::getConnection()->getProtocol()->var_dump($actions);
				if (isset($actions[$eventName]) and count($actions[$eventName]) > 0) {                        
					foreach ($actions[$eventName] as $action) {
						Services::getConnection()->getProtocol()->var_dump($action);
						$action[$class]->{$action[$method]}($data);
					}
				}
			}
		}
	}
}
?>