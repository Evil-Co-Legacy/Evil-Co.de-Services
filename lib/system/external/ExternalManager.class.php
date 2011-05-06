<?php

/**
 * Manages all external access
 *
 * @author	Tim Düsterhus
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
 
class ExternalManager {
	
	
	public function __construct() {
	
	}
	
	public function fire() {
		$sql = "SELECT *
			FROM	external_actions";
		$actions = Services::getDB()->fetchAll($sql);
		
		$finished = array();
		foreach ($actions as $action) {
			$class = $action->handler;
			try {
				$finished[] = new $class($this, $action);
			}
			catch (Exception $e) {
				Services::handleException($e);
			}
		}
		
		foreach ($finished as $tmp) {
			$sql = "DELETE FROM external_actions 
				WHERE ";
			throw new SystemException('ToDo');
		}
	}
}
?>