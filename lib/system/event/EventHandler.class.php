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
	public function registerEvent(&$class, $method, $targetClass, $targetEvent) {
		$this->events[] = array('class' => $class, 'method' => $method, 'targetClass' => $targetClass, 'targetEvent' => $targetEvent);
	}
	
	/**
	 * Fires an event
	 * @param	object	$class
	 * @param	string	$eventName
	 */
	public function fire(&$class, $eventName) {
		foreach($this->events as $event) {
			if (is_subclass_of($class, $event['targetClass']) and $eventName == $event['targetEvent']) call_user_func(array($event['class'], $event['method'])); 
		}
	}
}
?>