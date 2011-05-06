<?php
class ArgumentParser {
	protected $data = array(
		'script'      => '',
		'options'   => array(),
		'flags'     => array(),
		'arguments' => array(),
	);
		
	public function __construct($arguments) {
		$this->parse($arguments);
	}
	
	public function get($type, $name) {
		return isset($this->data[$type][$name]) ? $this->data[$type][$name] : null;
	}
	
	/**
	 * Parses CLI-Args
	 *
	 * @param	array<mixed>	$args	arguments
	 * @return	array<mixed>		parsed arguments
	 */
	protected function parse($args){
		$this->data['script'] = array_shift($args);

		while (($arg = array_shift($args)) !== null) {
			if (substr($arg, 0, 2) === '--') {
				$option = substr($arg, 2);

				// is it the syntax '--option=argument'?
				if (strpos($option, '=') !== false) {
					array_push($this->data['options'], explode('=', $option, 2));
				}
				else {
					array_push($this->data['options'], $option);
				}
			}
			else if (substr($arg, 0, 1) == '-') {
				for ($i = 1; isset($arg[$i]); $i++) {
					if (isset($this->data['flags'][$arg[$i]])) $this->data['flags'][$arg[$i]]++;
					else $this->data['flags'][$arg[$i]] = 1;
				}
			}
			else {
				$this->data['arguments'][] = $arg;
			}
		}
	}
}
?>