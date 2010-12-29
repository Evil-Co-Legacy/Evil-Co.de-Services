<?php

class UserException extends SystemException {
	
	/**
	 * Contains a user object
	 * @var UserType
	 */
	protected $user = null;
	
	/**
	 * Creates a new instance of type UserException
	 * @param	UserType	$user
	 * @param	string		$message
	 * @param	integer	$code
	 * @return void
	 */
	public function __construct($user, $message, $code = 0) {
		parent::__construct($message, $code);
		
		$this->user = $user;
	}
	
	/**
	 * Returnes a user object
	 * @return UserType
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * Sends the given message to user
	 */
	public function sendMessage() {
		$this->user->sendMessage($this->message);
	}
	
	/**
	 * @see SystemException::sendDebugLog()
	 */
	public function sendDebugLog() {
		parent::sendDebugLog();
		
		Services::getConnection()->sendLogLine("User: ".$this->user);
	}
}