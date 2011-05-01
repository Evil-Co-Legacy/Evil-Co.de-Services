<?php

/**
 * Represents a line (e.g. a G-Line)
 * @author		Johannes Donath
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class Line {
	
	/**
	 * Contains the type of this line
	 * @var string
	 */
	protected $type = '';
	
	/**
	 * Contains the mask of this line
	 * @var string
	 */
	protected $mask = '';
	
	/**
	 * Contains the setter of this line
	 * @var string
	 */
	protected $setter = '';
	
	/**
	 * Contains the timestamp of this line
	 * @var integer
	 */
	protected $timestamp = 0;
	
	/**
	 * Contains the duration of this line
	 * @var integer
	 */
	protected $duration = 0;
	
	/**
	 * Contains the reason of this line
	 * @var string
	 */
	protected $reason = '';
	
	/**
	 * Creates a new instance of type Line
	 * @param	string	$type
	 * @param	string	$mask
	 * @param	string	$setter
	 * @param	integer	$timestamp
	 * @param	integer	$duration
	 * @param	string	$reason
	 */
	public function __construct($type, $mask, $setter, $timestamp, $duration, $reason) {
		$this->type = $type;
		$this->mask = $mask;
		$this->setter = $setter;
		$this->timestamp = intval($timestamp);
		$this->duration = intval($duration);
		$this->reason = $reason;
	}
	
	/**
	 * Returns the type of this line
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Returns the mask of this line
	 * @return string
	 */
	public function getMask() {
		return $this->mask;
	}
	
	/**
	 * Returns the setter of this line
	 * @return string
	 */
	public function getSetter() {
		return $this->setter;
	}
	
	/**
	 * Returns the timestamp of this line
	 * @return integer
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}
	
	/**
	 * Returns the duration of this line
	 * @return integer
	 */
	public function getDuration() {
		return $this->duration;
	}
	
	/**
	 * Returns the reason of this line
	 * @return string
	 */
	public function getReason() {
		return $this->reason;
	}
	
	/**
	 * Returns true if this line is expired
	 * @return boolean
	 */
	public function isExpired() {
		// handle lines without expire period
		if ($this->duration == 0) return false;
		
		// handle duration property
		if (($this->timestamp + $this->duration) > time()) return false;
		
		return false;
	}
}
?>