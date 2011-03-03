<?php
// imports
require_once(SDIR.'lib/system/irc/ModeList.class.php');
require_once(SDIR.'lib/system/irc/ModeArgumentList.class.php');

/**
 * Manages and parses mode strings
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractModeList implements ModeList, Iterator {
	
	/**
	 * TODO: Add description
	 *
	 * @var array<boolean>
	 */
	protected static $loadedModeInformation = array();
	
	/**
	 * Contains the name of the file that contains mode information
	 *
	 * @var string
	 */
	protected static $modeInformationFilename = '';
	
	/**
	 * Contains a list of modes
	 *
	 * @var array<Mode>
	 */
	protected $modes = array();
	
	/**
	 * Contains a pointer to current iterator element
	 *
	 * @var integer
	 */
	protected $modePointer = 0;
	
	/**
	 * @see ModeList::__construct($modeString)
	 */
	public function __construct($modeString) {
		// parse modes
		$this->parseModeString($modeString);
	}
	
	/**
	 * @see ModeList::addMode()
	 */
	public function addMode($modeChar, $argument = null) {
		if (!$this->hasMode($modeChar)) $this->modes[] = new Mode($modeChar, $argument);
	}
	
	/**
	 * @see ModeList::hasArgument()
	 */
	public static function hasArgument($modeChar) {
		// try to load information
		if (!isset(static::$loadedModeInformation[$modeChar])) self::loadMode($modeChar);
		
		// validate
		if (!isset(static::$loadedModeInformation[$modeChar])) throw new RecoverableException("Unknown mode char '".$modeChar."'");
		
		return static::$loadedModeInformation[$modeChar];
	}
	
	/**
	 * @see ModeList::hasMode()
	 */
	public function hasMode($modeChar) {
		return (stripos($this->__toString(), $modeChar) !== false ? true : false);
	}
	
	/**
	 * @see ModeList::loadMode()
	 */
	public static function loadMode($modeChar) {
		if (!isset(self::$loadedModeInformation[$modeChar])) {
			try {
				$xml = new XML(Services::getProtocol()->getProtocolDir().'modes/'.self::$modeInformationFilename.'.xml');
			} catch (SystemException $ex) {
				throw new RecoverableException($ex->getMessage(), $ex->getCode());
			}
			
			$data = $xml->getElementTree('information');
			
			foreach($data['children'] as $child) {
				if (!isset($child['cdata']) or !isset($child['attrs']['attribute'])) throw new RecoverableException("Invalid mode definition in file '".Services::getProtocol()->getProtocolDir().'modes/'.self::$modeInformationFilename.'.xml'."'");
				
				if ($child['cdata'] == $modeCar) static::$loadedModeInformation[$modeChar] = (bool) intval($child['attrs']['attribute']);
			}
			
			// destroy elements
			unset($xml);
			unset($data);
		}
	}
	
	/**
	 * @see ModeList::parseModeString()
	 */
	public function parseModeString($string) {
		// get arguments
		$argumentList = new ModeArgumentList(get_class($this), $string);
		$string = substr($string, 0, (stripos($string, ' ') ? stripos($string, ' ') : 0));
		
		// get needed variables
		$currentFunction = '+';
		$length = strlen($string);
		
		for($i = 0; $i < $length; $i++) {
			switch($string{$i}) {
				case '+':
					$currentFunction = '+';
					break;
				case '-':
					$currentFunction = '-';
				default:
					switch($currentFunciton) {
						case '+':
							$argument = $argumentList->getArgument($i);
							$this->addMode($string{$i}, $argument);
						break;
						case '-':
							$this->removeMode($string{$i});
						break;
					}
			}
		}
	}
	
	/**
	 * @see ModeList::removeString()
	 */
	public function removeMode($modeChar) {
		foreach($this->modes as $key => $mode) {
			if ($mode->__toString() == $modeChar) unset($this->mode[$modeChar]);
		}
	}
	
	/**
	 * @see ModeList::updateModes()
	 */
	public function updateModes($modeString) {
		$this->parseModeString($modeString);
	}
	
	/**
	 * @see ModeList::__toString
	 */
	public function __toString() {
		$string = "";
		
		foreach($this->modes as $mode) {
			$string .= $mode->__toString();
			$string = $string." ".$mode->getArgument();
		}
		
		return $string;
	}
	
	// ITERATOR METHODS
	
	/**
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->modePointer = 0;
	}
	
	/**
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->modes[$this->modePointer];
	}
	
	/**
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->modePointer;
	}
	
	/**
	 * @see Iterator::next()
	 */
	public function next() {
		$this->modePointer++;
	}
	
	/**
	 * @see Iterator::valid()
	 */
	public function valid() {
		return (isset($this->modes[$this->modePointer]));
	}
}
?>