<?php

/**
 * Manages UUIDs
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class UUID {
	
	/**
	 * Contains an instance of type UUID
	 * @var UUID
	 */
	protected static $instance = null;
	
	/**
	 * Contains the ID of user (Used to generate a queue of UUIDs)
	 * @var	integer
	 */
	protected static $userID = 0;
	
	/**
	 * Contains all valid chars
	 * @var	string
	 */
	protected static $charmap = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
	/**
	 * Returnes an instance of type UUID
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new UUID();
		}
		return self::$instance;
	}
	
	/**
	 * Creates a new uuid
	 * @return	string
	 */
	public function generate() {
		// get rest
		$rest = self::$userID++;
		$uuid = '';
		do {
			$uuid = substr(self::$charmap, $rest % 26, 1).$uuid;
			$rest = floor($rest / 26);
		} while ($rest > 0);
		$uuid = str_pad($uuid, 6, 'A', STR_PAD_LEFT);
		return $uuid;
	}
}
?>