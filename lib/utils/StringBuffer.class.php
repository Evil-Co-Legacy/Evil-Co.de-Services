<?php
/**
 * Represents a string
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class StringBuffer {
	
	/**
	 * Contains the correct newline char
	 *
	 * @var		string
	 */
	const NEWLINE = "\n";
	
	/**
	 * Contains the current string
	 *
	 * @var		string
	 */
	protected $string = '';
	
	/**
	 * Adds a part to current string
	 *
	 * @param	string	$string
	 * @return	void
	 */
	public function add($string) {
		$this->string .= $string;
	}
	
	/**
	 * Clears the buffer
	 *
	 * @return	void
	 */
	public function clearBuffer() {
		$this->string = '';
	}
	
	/**
	 * Returnes the current buffer and clears it
	 *
	 * @return	string
	 */
	public function get() {
		$string = $this->string;
		$this->clearBuffer();
		return $string;
	}
	
	/**
	 * Check whether the given pattern matches the current buffer
	 *
	 * @param	string	$pattern
	 * @return	boolean
	 */
	public function match($pattern) {
		return (preg_match($pattern, $this->string));
	}
	
	/**
	 * Replaces $search with $replace
	 *
	 * @param	string	$search
	 * @param	string	$replace
	 * @return	void
	 */
	public function replace($search, $replace) {
		$this->string = StringUtil::replace($search, $replace, $this->string);
	}
	
	/**
	 * @see StringBuffer::get()
	 */
	public function __toString() {
		return $this->get();
	}
}
?>