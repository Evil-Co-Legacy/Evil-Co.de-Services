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
		// update userID
		self::$userID++;
		
		// get rest
		$rest = self::$userID;
		
		// set active char
		$activeChar = 5;
		
		// Sample UUID: AAAAAB
		$chars = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
		
		do {
			if ($rest > 26) {
				$rest = self::$userID % 26;
				$chars[$activeChar] = 25;
				if ($activeChar > 0)
					$activeChar--;
				else
					throw Exception("TO MANY BOTS! DONT LOAD 2 MIO MODULES!!!!!");
			} else
				$rest = 0;
		} while ($rest > 26);
		$activeChar = $rest;
		
		$uuid = "";
		
		foreach($chars as $mapID) {
			$uuid .= self::$charmap{$mapID};
		}
		
		return $uuid;
	}
}
?>