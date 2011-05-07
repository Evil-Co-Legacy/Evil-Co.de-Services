<?php
/**
 * Manages external access
 *
 * @author	Tim Düsterhus
 * @copyright	2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ExternalManager {

	public function __construct() { }

	/**
	 * Executes actions
	 *
	 * @return void
	 */
	public function fire() {
		$sql = "SELECT *
			FROM	external_actions";
		$actions = Services::getDB()->fetchAll($sql);

		$finished = array();
		foreach ($actions as $action) {
			try {
				$class = $action->handler;
				require_once(DIR.'lib/system/external/handlers/'.$class.'.class.php');
				$handler = new $class($this, $action);
				$handler->execute();
				$finished[] = $handler;

				Services::getDB()->delete('external_actions', 'actionID = '.Services::getDB()->quote($action->actionID, 'INTEGER'));
				Services::getDB()->insert('external_action_log', array('actionID' => $tmp->actionID, 'data' => Services::getDB()->quote(serialize($action)), 'exception' => ''));
			}
			catch (Exception $e) {
				Services::getDB()->insert('external_action_log', array('actionID' => $tmp->actionID, 'data' => Services::getDB()->quote(serialize($action)), 'exception' => Services::getDB()->quote(serialize($e))));
			}
		}
	}
}
?>