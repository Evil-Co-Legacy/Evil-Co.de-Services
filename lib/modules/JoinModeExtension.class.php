<?php
// imports
require_once(SDIR.'lib/modules/ExtensionModule.class.php');

/**
 * Listens on JOIN notifications and gives additional permissions to users
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class JoinModeExtension extends ExtensionModule {

	/**
	 * @see lib/modules/ExtensionModule::registerEvents()
	 */
	public function registerEvents() {
		parent::registerEvents();

		Services::getEvent()->registerEvent($this, 'handleJoin', 'Protocol', 'channelJoined');
	}
	
	/**
	 * Handles joins on channels registered with chanserv
	 * @param	string	$data
	 */
	public function handleJoin($data) {
		// get chanserv address
		$chanserv = Services::getModuleManager()->lookupModule('ChanServ');
		
		// check for registered channel
		if (call_user_func(array($chanserv, 'isRegistered'), $data['channel'])) {
			foreach($data['userList'] as $user) {
				// get user from array
				$user = $user['user'];
				
				// get channel access
				$access = Services::getModuleManager()->getBot($chanserv)->getAccess($data['channel'], $user->accountname);
				
				if ($access >= 500) {
					Services::getConnection()->getProtocol()->sendMode(Services::getModuleManager()->getBot($chanserv)->getUuid(), $data['channel'], '+q '.$user->getNick());
				}
				else if ($access >=  Services::getModuleManager()->getBot($chanserv)->getNeededAccess($data['channel'], 'getOp')) {
					Services::getConnection()->getProtocol()->sendMode(Services::getModuleManager()->getBot($chanserv)->getUuid(), $data['channel'], '+o '.$user->getNick());
				}
				else if ($access >=  Services::getModuleManager()->getBot($chanserv)->getNeededAccess($data['channel'], 'getVoice')) {
					Services::getConnection()->getProtocol()->sendMode(Services::getModuleManager()->getBot($chanserv)->getUuid(), $data['channel'], '+v '.$user->getNick());
				}
			}
		}
	}
}
?>