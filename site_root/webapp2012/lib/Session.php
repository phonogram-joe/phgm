<?php

class Session
{
	private $userKey;
	private $globalKey;

	const NONCE_KEY = 'phgm:nonce';

	public function Session()
	{
		$this->userKey = Config::get(Config::SESSION_USER_KEY);
		$this->globalKey = Config::get(Config::SESSION_GLOBAL_KEY);
	}

	public function getUser()
	{
		if (isset($_SESSION[$this->userKey])) {
			return $_SESSION[$this->userKey];
		}
		return null;
	}

	public function setUser($user)
	{
		$_SESSION[$this->userKey] = $user;
	}

	public function clearUser()
	{
		$user = $this->getUser();
		unset($_SESSION[$this->userKey]);
		return $user;
	}

	public function addFlashMessage($msg)
	{
		//TODO: implement addFlashMessage()
	}
	public function getFlashList()
	{
		//TODO: implement getFlashList()
	}
	public function clearFlashList()
	{
		//TODO: implement clearFlashList()
	}

	public function set($key, $value)
	{
		$_SESSION[$this->globalKey][$key] = $value;
	}

	public function get($key)
	{
		if (isset($_SESSION[$this->globalKey][$key])) {
			return $_SESSION[$this->globalKey][$key];
		}
		return null;
	}

	public function clear($key)
	{
		$value = $this->get($key);
		unset($_SESSION[$this->globalKey][$key]);
		return $value;
	}

	public function generateNonce()
	{
		$nonce = $this->get(self::NONCE_KEY);
		if (!is_null($nonce)) {
			return $nonce;
		} else {
			$nonce = md5(uniqid(mt_rand(), true));
			$this->set(self::NONCE_KEY, $nonce);
			return $nonce;
		}
	}

	public function isValidNonce($nonce)
	{
		$storedTicket = $this->get(self::NONCE_KEY);
		if (!is_null($storedTicket)) {
			return $nonce === $storedTicket;
		}
		return false;
	}
}