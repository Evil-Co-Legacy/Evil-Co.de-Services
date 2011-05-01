<?php

class Timer {
	
	/**
	 * Contains the callback that should executed
	 * @var callback
	 */
	protected $callback = array();
	
	/**
	 * Contains the interval for timer
	 * @var integer
	 */
	protected $interval = 0;
	
	/**
	 * Contains the timestamp of last execution
	 * @var integer
	 */
	protected $lastExecutionTimestamp = 0;
	
	/**
	 * Creates a new instance of type Timer
	 * @param	callback	$callback
	 * @param	integer		$interval
	 */
	public function __construct($callback, $interval) {
		$this->callback = $callback;
		$this->interval = $interval;
	}
	
	/**
	 * Executes the callback and sets the new last execution timestamp
	 * @return void
	 */
	public function execute() {
		// execute callback
		call_user_func($this->callback, $this->lastExecutionTimestamp);
		
		// set last execution timestamp
		$this->lastExecutionTimestamp = time();
	}
	
	/**
	 * Returns true if an execution is needed
	 * @return boolean
	 */
	public function needsExecution() {
		return (($this->lastExecutionTimestamp + $this->interval) <= time());
	}
}
?>