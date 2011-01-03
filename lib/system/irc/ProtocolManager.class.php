<?php

/**
 * Manages connection protocols
 * @author		Johannes Donath
 * @copyright		2010 DEVel Fusion
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class ProtocolManager {

	/**
	 * Contains a relative path from SDIR to location where protocol classes are located
	 * @var string
	 */
	const PROTOCOL_PATH = 'lib/system/irc/protocol/';

	/**
	 * Contains the correct protocol instance
	 * @var Protocol
	 */
	protected $protocol = null;

	/**
	 * Contains additional information for protocol
	 * @var array
	 */
	protected $protocolInformation = array();

	/**
	 * Contains required protocol information fields
	 * @var array
	 */
	protected $requiredProtocolInformation = array('author', 'copyright', 'file');

	/**
	 * Contains a list of supported methods and modes
	 * @var array
	 */
	protected $supportedTypes = array();

	/**
	 * Creates a new instance of ProtocolManager
	 */
	public function __construct() {
		// get protocol from configuration
		$protocol = self::getCorrectProtocol();

		// validate protocol path
		if (!is_dir(SDIR.self::PROTOCOL_PATH.$protocol.'/')) throw new ProtocolException("Cannot find protocol location");

		// get protocol information
		$this->readProtocolInformation($protocol);

		// get supported types
		$this->readSupportedTypes($protocol);

		// init protocol object
		$this->initProtocol();

		// validate protocol object
		if (!($this->protocol instanceof Protocol)) throw new ProtocolException("Protocol definition of '".$protocol."' is invalid: Must be an instance of Protocol");
	}

	/**
	 * Reads the correct protocol string from configuration or detects it automaticly
	 * @return void
	 */
	protected static function getCorrectProtocol() {
		// get configuration
		$connectionConfiguration = Services::getConfiguration()->get('connection');

		// detect missing information
		if (!isset($connectionConfiguration['protocol'])) throw new ProtocolException("Missing protocol configuration variable");

		// get protocol string
		return strtolower($connectionConfiguration['protocol']);
	}

	/**
	 * Creates a new protocol instance for correct protocol
	 * @return void
	 */
	protected function initProtocol() {
		// include protocol
		require_once(SDIR.$this->protocolInformation['file']);

		// generate class name
		$className = basename($this->protocolInformation['file'], '.class.php');

		// create new instance
		$this->protocol = new $className($this->supportedTypes);
	}

	/**
	 * Returnes true if the protocol is alive
	 * @return boolean
	 */
	public function isAlive() {
		if ($this->protocol !== null and $this->protocol->isAlive()) return true;
		return false;
	}

	/**
	 * Returnes true if the given type (or function) is supported in current protocol
	 * @param	string	$type
	 * @return boolean
	 */
	public function isSupported($type) {
		if (isset($this->supportedTypes[$type])) return $this->supportedTypes[$type];
		return false;
	}

	/**
	 * Reads protocol information from xml
	 * @param	string	$protocol
	 * @return void
	 */
	protected function readProtocolInformation($protocol) {
		try {
			$xml = new XML(SDIR.self::PROTOCOL_PATH.$protocol.'/protocol.xml');
		} catch (SystemException $ex) {
			// replace SystemExceptions with correct ProtocolException
			throw new ProtocolException("Cannot read protocol information: ".$ex->getMessage());
		}

		// get protocol tree
		$data = $xml->getElementTree('protocol');

		// read information
		foreach($data['children'] as $child) {
			// remove elements without content
			if (!isset($child['cdata'])) continue;


			$this->protocolInformation[$child['name']] = $child['cdata'];
		}

		// validate protocol information
		$this->validateProtocolInformation();
	}

	/**
	 * Reads information about protocol supports from xml
	 * @param	string	$protocol
	 */
	protected function readSupportedTypes($protocol) {
		try {
			$xml = new XML(SDIR.self::PROTOCOL_PATH.$protocol.'/protocol.xml');
		} catch (SystemException $ex) {
			// replace SystemExceptions with correct ProtocolException
			throw new ProtocolException("Cannot read protocol information: ".$ex->getMessage());
		}

		// get protocol tree
		$data = $xml->getElementTree('definition');

		// read information
		foreach($data['children'] as $child) {
			// remove elements without content
			if (!isset($child['cdata']) or !isset($child['attrs']['type'])) continue;

			$this->supportedTypes[$child['attrs']['type']] = $child['cdata'];
		}
	}

	/**
	 * Validates protocol information
	 * @return void
	 * @throws ProtocolException
	 */
	protected function validateProtocolInformation() {
		foreach($this->requiredProtocolInformation as $field) {
			if (!isset($this->protocolInformation[$field])) throw new ProtocolException("Invalid protocol information: Field '".$field."' is missing");
		}
	}

	/**
	 * Redirects calls to undefined methods to current protocol
	 * @param	string	$method
	 * @param	array	$arguments
	 * @return mixed
	 */
	public function __call($method, $arguments) {
		if (method_exists($this->protocol, $method))
			return call_user_func_array(array($this->protocol, $method), $arguments);

		// method not found ...
		throw new RecoverableException("Method '".$method."' does not exist in class ".get_class($this));
	}

	/**
	 * Redirects use of undefined properties to current protocol
	 * @param	string	$property
	 * @return mixed
	 */
	public function __get($property) {
		if (property_exists($this->protocol, $property))
			return $this->protocol->{$property};

		// property does not exist
		throw new RecoverableException("Property '".$property."' does not exist in class ".get_class($this));
	}

	/**
	 * Redirects use of undefined properties to current protocol
	 * @param	string	$property
	 * @param	mixed	$value
	 * @return void
	 */
	public function __set($property, $value) {
		if (property_exists($this->protocol, $property))
			$this->protocol->{$property} = $value;

		// property does not exist
		throw new RecoverableException("Property '".$property."' does not exist in class ".get_class($this));
	}
}
?>