<?php
/**
 * Manages UUIDs
 *
 * @author	Tim Düsterhus
 * @copyright	2010 - 2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UUID {

	/**
	 * Contains an instance of type UUID
	 *
	 * @var 	UUID
	 */
	protected static $instance = null;

	/**
	 * Contains the ID of user (Used to generate a queue of UUIDs)
	 *
	 * @var	integer
	 */
	protected static $userID = 0;

	/**
	 * Contains all valid chars
	 *
	 * @var	string
	 */
	protected static $charmap = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

	/**
	 * Returns an instance of type UUID
	 *
	 * @return	UUID
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new UUID();
		}
		return self::$instance;
	}

	/**
	 * Creates a new uuid
	 *
	 * @return	string
	 */
	public function generate($prefix = null) {
		// get rest
		$rest = self::$userID++;
		$uuid = '';

		do {
			$uuid = substr(self::$charmap, $rest % 26, 1).$uuid;
			$rest = floor($rest / 26);
		} while ($rest > 0);
		$uuid = str_pad($uuid, 6, 'A', STR_PAD_LEFT);

		// handle prefix
		if ($prefix !== null) $uuid = $prefix.$uuid;

		return $uuid;
	}
}
?>