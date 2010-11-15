<?php
// imports
require_once(SDIR.'lib/system/irc/ModeList.class.php');

/**
 * Defines default methods for mode lists
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
abstract class AbstractModeList implements ModeList {
	
	/**
	 * Contains all modes
	 * @var	array<Mode>
	 */
	protected $modeList = array();
	
	/**
	 * Contains the type of all modes that should managed by this class
	 * @var	string
	 */
	protected $type = '';
	
	/**
	 * @see	ModeList::__construct()
	 */
	public function __construct($modeString = '+') {
		// remove '+' from mode list
		$modeString = substr($modeString, 1);
		$modeStr = explode(" ", $modeString);
		
		// get arguments
		if (count($modeStr) > 1) {
			$argumentList = $modeStr;
			unset($argumentList[0]);
			$argumentList = array_merge(array(), $argumentList);
		}
		
		// remove whitespace from mode string
		$modeString = (count($modeStr) ? $modeStr[0] : $modeString);
		
		// create needed variables
		$lastArgument = 0;
		
		// get every item
		for($i = 0; $i < strlen($modeString); $i++) {
			// write char to $mode
			$mode = $modeString{$i};
			
			// search for mode classfile
			if (file_exists(SDIR.'lib/system/irc/'.IRCD.'/modes/'.$this->type.'/'.$mode.'Mode.class.php')) {
				// include mode
				require_once(SDIR.'lib/system/irc/'.IRCD.'/modes/'.$this->type.'/'.$mode.'Mode.class.php');
				$className = $mode.'Mode';
				
				// get argument
				if (call_user_func(array($className, 'canHaveArgument'))) {
					if (isset($argumentList[($lastArgument + 1)])) {
						$lastArgument++;
						$argument = $argumentList[$lastArgument];
					}
				}
				
				// create new mode instance
				$this->modeList[] = new $className((isset($argument) ? $argument : ''));
			} else {
				// No modefile found ...
				throw new Exception("Invalid mode '".$mode."'! Maybe choosen wrong IRCd?");
			}
		}
	}
	
	/**
	 * @see	ModeList::setMode()
	 */
	public function setMode($mode, $argument = null) {
		if (!$this->hastMode($mode)) {
			require_once(SDIR.'lib/system/irc/'.IRCD.'/modes/'.$this->type.'/'.$mode.'Mode.class.php');
			$className = $mode.'Mode';
			
			$this->modeList[] = new $className($argument);
		}
	}
	
	/**
	 * @see	ModeList::hasMode()
	 */
	public function hasMode($modeName) {
		foreach($this->modeList as $mode) {
			if ($mode->getName() == $modeName) return true;
		}
		return false;
	}
	
	/**
	 * @see	ModeList::getModeArgument()
	 */
	public function getModeArgument($modeName) {
		foreach($this->modeList as $mode) {
			if ($mode->getName() == $modeName) return $mode->getArgument();
		}
		return null;
	}
}
?>