<?php
/**
 * This exception will thrown on syntax errors
 *
 * @author	Johannes Donath
 * @copyright	2010 DEVel Fusion
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class SyntaxErrorException extends UserException {
	
	/**
	 * @see UserException::sendMessage()
	 */
	public function sendMessage() {
		$this->user->sendMessage(MessageParser::addColorCode(COLOR_UNDERLINE, MessageParser::addColorCode(COLOR_BOLD, "Syntax:")).$this->message);
	}
}
?>