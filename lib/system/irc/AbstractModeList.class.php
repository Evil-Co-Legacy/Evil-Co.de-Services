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
	 * @see	ModeList::__construct()
	 */
	public function __construct($modeString = '+') {
		$modeString = substr($modeString, 1);
		$argumentList = substr($modeString, (stripos(" ") + 1));
		$argumentList = explode($argumentList);
		
		for($i = 0; $i < strlen($modeString[0]); $i++) {
			$mode = $modeString[0]{$i};
			if (file_exists(SDIR.'lib/system/irc/'.IRCD.'/modes/'.$this->type.'/'.$mode.'Mode.class.php')) {
				require_once(SDIR.'lib/system/irc/'.IRCD.'/modes/'.$this->type.'/'.$mode.'Mode.class.php');
				$className = $mode.'Mode';
				
				if (call_user_func(array($className, 'canHaveArgument'))) {
					if (isset($argumentList[$i])) $argument = $argumentList[$i];
				}
				
				$this->modeList[] = new $className((isset($argument) ? $argument : ''));
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