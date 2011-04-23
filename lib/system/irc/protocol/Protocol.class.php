<?php

/**
 * Defines default methods for protocols
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class Protocol {

	/**
	 * Creates a new instance of type Protocol
	 */
	abstract public function __construct(&$supportedTypes);
	
	/**
	 * Handles exceptions in protocol system
	 * @param	ProtocolException	$ex
	 * @return void
	 */
	abstract public function handleException(ProtocolException $ex);

	/**
	 * Inits the IRC server-to-server connection
	 * @return void
	 */
	abstract public function initConnection();
	
	/**
	 * Returnes true if the current connection to server is alive
	 * @return boolean
	 */
	abstract public function isAlive();
	
	/**
	 * Returnes true if the current connection to server is ready for msgs and other funny things
	 * @return boolean
	 */
	abstract public function isReady();
	
	/**
	 * Shuts the connection down
	 * @return void
	 */
	abstract public function shutdown();
}
?>