<?php
/**
 * Does nothing with sense
 *
 * @author		Johannes Donath, Tim Düsterhus
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
	public static final function getTheAnswerOfLifeTheUniverseAndEverything() {
		return self::HALF_ANSWER * 2;
	}
}
?>
