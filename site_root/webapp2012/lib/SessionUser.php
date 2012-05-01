<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class SessionUser
{
	private $isAuthenticated;
	private $credentials;

	public function SessionUser()
	{
		$this->isAuthenticated = false;
		$this->credentials = array();
	}

	public function isAuthenticated()
	{
		return $this->isAuthenticated;
	}

	public function setAuthenticated($isAuthenticated)
	{
		if (is_bool($isAuthenticated)) {
			$this->isAuthenticated = $isAuthenticated;
		} else {
			throw new Exception('SessionUser:setAuthenticated() -- true/falseのみ設定できます。');
		}
	}

	public function set($key, $value)
	{
		$_SESSION[SESSION_USER_KEY][$key] = $value;
	}

	public function get($key)
	{
		if (isset($_SESSION[SESSION_USER_KEY][$key])) {
			return $_SESSION[SESSION_USER_KEY][$key];
		}
		return null;
	}

	public function clear($key)
	{
		unset($_SESSION[SESSION_USER_KEY][$key]);
	}

	public function addCredential($value)
	{
		if (is_null($value)) {
			//throw new Exception('SessionUser:addCredential() -- 「ナル」は追加できません。');
		} else if (is_array($value)) {
			foreach ($value as $credential) {
				if (is_null($credential)) {
					//throw new Exception('SessionUser:addCredential() -- 「ナル」は追加できません。');
				} else {
					$this->credentials[] = $credential;
				}
			}
		} else {
			$this->credentials[] = $value;
		}
	}

	public function hasCredential($value)
	{
		return array_search($value, $this->credentials) !== false;
	}

	public function clearCredentials()
	{
		$this->credentials = array();
	}

	public function clearFromSession()
	{
		unset($_SESSION[SESSION_USER_KEY]);
	}

}