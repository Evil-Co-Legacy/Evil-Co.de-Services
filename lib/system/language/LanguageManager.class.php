<?php
// Zend imports
require_once('Zend/Translate.php');

/**
 * Manages all language variables
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class LanguageManager {

	/**
	 * Contains all
	 *
	 * @var array<mixed>
	 */
	protected $availableLanguages = array();

	/**
	 * Contains all items
	 *
	 * @var	array<array>
	 */
	protected $items = array();

	/**
	 * Creates a new instance of LanguageManager
	 */
	public function __construct() {
		$sql = "SELECT
					*
				FROM
					language";
		$result = Services::getDB()->sendQuery($sql);

		while($row = Services::getDB()->fetchArray($result)) {
			$this->availableLanguages[] = $row;
			$this->items[intval($row['languageID'])] = new Zend_Translate(array(
					'adapter' => 'gettext',
					'content' => SDIR.'language/'.$row['code'].'.mo',
					'locale'  => $row['code']
    				)
			);
		}
	}

	/**
	 * Returns the content of the given language var (This method returnes the name of the variable if no matching variable exists)
	 *
	 * @param	integer	$languageID
	 * @param	string	$variable
	 * @return	string
	 */
	public function get($languageID, $variable) {
		// whohoo hardcoded shit
		if ($languageID == null or !isset($this->items[$languageID]))
			$languageID = 1;
		else
			$languageID = intval($languageID);

		// create needed vars
		$value = $this->items[$languageID]->_($variable);
		$arguments = func_get_args();

		// kick languageID and variable from argument list
		$arguments[0] = $value;
		unset($arguments[1]);

		// resort array
		$arguments = array_merge(array(), $arguments);

		// return correct value
		return call_user_func_array('sprintf', $arguments);
	}
}
?>