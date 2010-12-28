<?php
// imports
require_once(SDIR.'lib/modules/ExtensionModule.class.php');

/**
 * Listens on KICK commands and kicks users that aren't allowed to kick other users
 * @author		Johannes Donath
 * @copyright	2010 DEVel Fusion
 */
class KickRevengeExtension extends ExtensionModule {

	/**
	 * @see lib/modules/ExtensionModule::registerEvents()
	 */
	public function registerEvents() {
		parent::registerEvents();

		Services::getEvent()->registerEvent($this, 'handleKick', 'Protocol', 'userKicked');
	}

	/**
	 * Handles an incoming KICK event
	 * @param	array	$data
	 */
	public function handleKick($data) {
		// get chanserv
		$chanserv = Services::getModuleManager()->lookupModule('ChanServ');

		// ignore unregistered channels
		if (call_user_func(array($chanserv, 'isRegistered'), $data['target'])) {
			// check permissions
			if (call_user_func(array($chanserv, 'getAccess'), $data['target'], Services::getUserManager()->getUser($data['issuer'])->accountname) < call_user_func(array($chanserv, 'getAccess'), $data['target'], Services::getUserManager()->getUser($data['victim'])->accountname)) {
				// ok lets kick the issuer
				Services::getConnection()->getProtocol()->sendKick(Services::getModuleManager()->getBot($chanserv)->getUuid(), $data['target'], $data['issuer'], Services::getLanguage()->get(Services::getUserManager()->getUser($data['issuer'])->languageID, 'chanserv.kickedUserIsProtected'));
			}
		}
	}
}
?>