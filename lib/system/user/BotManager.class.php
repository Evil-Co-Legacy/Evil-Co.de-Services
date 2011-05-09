<?php
// imports
require_once(DIR.'lib/system/user/AbstractUserTypeManager.class.php');
require_once(DIR.'lib/system/user/BotUserType.class.php');

/**
 * Manages all service bots
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class BotManager extends AbstractUserTypeManager {
	
	/**
	 * Contains a definition for registration modes
	 * @var	integer
	 */
	const REGISTER_AUTOMATIC = 0;
	
	/**
	 * Cpmtaoms a definition for registration modes
	 * @var integer
	 */
	const REGISTER_MANUAL = 2;

	/**
	 * @see AbstractUserTypeManager::$userType
	 */
	protected $userType = 'BotUserType';
	
	public function registerBot(ModuleInstance $instance, $registerType) {
		// generate UUID
		$uuid = UUID::getInstance()->generate();
		
		// get other information from database
		$sql = "SELECT
				*
			FROM
				module_instance_bot
			WHERE
				moduleName = ?";
		$result = Services::getDB()->fetchRow($sql, $instance->getModuleInformation()->moduleName);
		
		if ($result) {
			// set properties
			$instance->getInstance()->uuid = $uuid;
			$instance->getInstance()->nickname = $result->nickname;
			$instance->getInstance()->trigger = $result->publicTrigger;
			$instance->getInstance()->hostname = $result->hostname;
			$instance->getInstance()->ident = $result->ident;
			$instance->getInstance()->ip = $result->ip;
			$instance->getInstance()->modes = $result->modes;
			$instance->getInstance()->gecos = $result->gecos;
			
			// add to manager
			$this->userList[$uuid] = Services::getMemoryManager()->create($instance->getInstance());
			
			// introduce client
			Services::getProtocolManager()->sendUid($instance->getInstance()->uuid, $instance->getInstance()->nickname, $instance->getInstance()->hostname, $instance->getInstance()->hostname, $instance->getInstance()->ident, $instance->getInstance()->ip, $instance->getInstance()->modes, $instance->getInstance()->gecos);
			
			// join servicechannel
			Services::getProtocolManager()->sendJoin($instance->getInstance()->uuid, Services::getConfiguration()->connection->servicechannel);
			
			// set op
			Services::getProtocolManager()->userSendMode($instance->getInstance()->uuid, Services::getConfiguration()->connection->servicechannel, '+o '.$instance->getInstance()->nickname);
		} else {
			Services::getLogger()->info("No login information for Module found '".$instance->getModuleInformation()->moduleName."'");
		}
	}
}
?>