<?php
// service imports
require_once(SDIR.'lib/system/timer/Timer.class.php');

/**
 * Manages registered timers
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class TimerManager implements Iterator {

	/**
	 * Contains a list of registered timers
	 * @var Timer
	 */
	protected $timerList = array();

	/**
	 * Contains an integer that points to current position in array
	 * @var integer
	 */
	protected $timerPointer = 0;

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// get keys
		$keys = array_keys($this->timerList);

		// get timerID for current pointer
		$timerID = $keys[$this->timerPointer];

		// get current Timer object
		return $this->timerList[$timerID];
	}

	/**
	 * Executes all timers
	 */
	public function execute() {
		foreach($this as $timer) {
			if ($timer->needsExecution()) $timer->execute();
		}
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$keys = array_keys($this->timerList);

		// get current timerID
		return $keys[$this->timerPointer];
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->timerPointer;
	}

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->timerPointer = 0;
	}

	/**
	 * Registeres a new timer
	 * @param	callback	$callback
	 * @param	integer		$interval
	 * @return integer
	 */
	public function registerTimer($callback, $interval) {
		// add new timer object to list
		$this->timerList[] = new Timer($callback, $interval);

		// return ID
		return (count($this->timerList) - 1);
	}

	/**
	 * Unregisteres a timer
	 * @param	integer	$timerID
	 * @return boolean
	 */
	public function unregisterTimer($timerID) {
		// check for invalid timer ID
		if (!isset($this->timerList[$timerID])) return false;

		// delete timer
		unset($this->timerList[$timerID]);

		// no error -> return true
		return true;
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		// get keys
		$keys = array_keys($this->timerList);

		return isset($keys[$this->timerPointer]);
	}
}
?>