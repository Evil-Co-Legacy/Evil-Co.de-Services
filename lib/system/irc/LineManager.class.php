<?php
// service imports
require_once(DIR.'lib/system/irc/Line.class.php');

/**
 * Manages global server lines (Such as g-lines)
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class LineManager implements Iterator {

	/**
	 * Contains all global lines
	 * @var array<Line>
	 */
	protected $lines = array();

	/**
	 * Contains a pointer for iterator feature
	 * @var integer
	 */
	protected $linePointer = 0;

	/**
	 * Adds a new line to manager
	 * @param	string	$type
	 * @param	string	$mask
	 * @param	string	$setter
	 * @param	integer	$timestamp
	 * @param	integer	$duration
	 * @param	string	$reason
	 * @return integer
	 */
	public function addLine($type, $mask, $setter, $timestamp, $duration, $reason) {
		// add line
		$this->lines[] = new Line($type, $mask, $setter, $timestamp, $duration, $reason);

		// fire event
		Services::getEventHandler()->fire($this, 'lineAdded', array('type' => $type, 'mask' => $mask, 'setter' => $setter, 'timestamp' => $timestamp, 'duration' => $duration, 'reason' => $reason, 'lineID' => (count($this->lines) - 1)));

		// send debug line
		Services::getLogger()->debug("Added line of type ".$type." for mask ".$mask." (Set by ".$setter." at ".$timestamp.") expiring in ".$duration." seconds with reason '".$reason."'");

		// return ID
		return (count($this->lines) - 1);
	}

	/**
	 * @see Iterator::current()
	 */
	public function current() {
		// get keys
		$keys = array_keys($this->lines);

		// get key
		$key = $keys[$this->linePointer];

		return $this->lines[$key];
	}

	/**
	 * @see Iterator::key()
	 */
	public function key() {
		// get keys
		$keys = array_keys($this->lines);

		return $keys[$this->linePointer];
	}

	/**
	 * @see Iterator::next()
	 */
	public function next() {
		++$this->linePointer;
	}

	/**
	 * Removes a line from manager
	 * @param	integer	$lineID
	 * @return void
	 * @throws RecoverableException
	 */
	public function removeLine($lineID) {
		// validate lineID
		if (!isset($this->lines[$lineID])) throw new RecoverableException("Tried to remove an unknown line with ID '".$lineID."'");

		// fire event
		Services::getEventHandler()->fire($this, 'lineRemoved', array('lineID' => $lineID));

		// send debug line
		Services::getLogger()->debug("Removed line with ID ".$lineID);

		// remove line
		unset($this->lines[$lineID]);
	}

	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->linePointer = 0;
	}

	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		// get keys
		$keys = array_keys($this->lines);

		// search for pointer
		return (isset($keys[$this->linePointer]));
	}

	// TODO: Add a timer for removing expired lines
}
?>