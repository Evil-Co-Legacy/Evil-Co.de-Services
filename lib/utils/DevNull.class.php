<?php
/**
 * Does nothing with sense
 *
 * @author		Johannes Donath, Tim D�sterhus
 * @copyright		2011 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class DevNull {
	const HALF_ANSWER = 21;
	
	/**
	 * Returns the answer of life, the universe and everything
	 *
	 * @return integer
	 */
	public function getTheAnswerOfLifeTheUniverseAndEverything() {
		return self::HALF_ANSWER * 2;
	}
}
?>