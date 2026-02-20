<?php

declare(strict_types=1);

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->find('username=:u', [':u' => $this->username]);

		if ($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		}

		if (!CPasswordHelper::verifyPassword($this->password, $user->password_hash)) {
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
			return false;
		}

		$this->_id = $user->id;
		$this->setState('role', $user->role);
		$this->errorCode = self::ERROR_NONE;
		return true;
	}

	public function getId()
	{
		return $this->_id;
	}
}
