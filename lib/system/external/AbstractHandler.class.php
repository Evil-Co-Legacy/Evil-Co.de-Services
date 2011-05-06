<?php
require_once(SDIR.'lib/system/external/Handler.class.php');

abstract class AbstractHandler implements Handler {
	public $manager, $data;
	public function __construct(ExternalManager $manager, $data) {
		$this->manager = $manager;
		$this->data = $data;
	}
}
?>