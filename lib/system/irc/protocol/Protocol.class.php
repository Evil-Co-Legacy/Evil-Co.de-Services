<?php

/**
 * Defines default methods for protocols
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface Protocol {

	/**
	 * Creates a new instance of type Protocol
	 */
	public function __construct(&$supportedTypes);
	
	/**
	 * Handles exceptions in protocol system
	 * @param	ProtocolException	$ex
	 * @return void
	 */
	public function handleException(ProtocolException $ex);

	/**
	 * Inits the IRC server-to-server connection
	 * @return void
	 */
	public function initConnection();
	
	/**
	 * Returnes true if the current connection to server is alive
	 * @return boolean
	 */
	public function isAlive();
	
	/**
	 * Returnes true if the current connection to server is ready for msgs and other funny things
	 * @return boolean
	 */
	public function isReady();
}
?>