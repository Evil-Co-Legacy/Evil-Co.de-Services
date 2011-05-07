<?php
require_once(DIR.'lib/system/external/Handler.class.php');

/**
 * Basic implementation of a handler
 *
 * @author	Tim Düsterhus
 * @copyright	2011 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
abstract class AbstractHandler implements Handler {
	public $manager, $data;

	public function __construct(ExternalManager $manager, $data) {
		$this->manager = $manager;
		$this->data = $data;
	}
}
?>