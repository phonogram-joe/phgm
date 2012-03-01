<?php

class Session
{
	public function Session()
	{
	}

	public static function getUser()
	{
		return $_SESSION[SESSION_USER_KEY];
	}

	public static function setUser($user)
	{
		$_SESSION[SESSION_USER_KEY] = $user;
	}

	public static function isAuthenticated()
	{
		return self::getUser()->isAuthenticated();
	}

	public static function setAuthenticated($isAuthenticated)
	{
		return self::getUser()->setAuthenticated($isAuthenticated);
	}

	public static function setUserParam($key, $value)
	{
		self::getUser()->set($key, $value);
	}

	public static function getUserParam($key)
	{
		return self::getUser()->get($key);
	}

	public static function addUserCredential($value)
	{
		self::getUser()->setCredential($value);
	}

	public static function isUserCredential($value)
	{
		self::getUser()->isCredential($value);
	}

	public static function set($key, $value)
	{
		$_SESSION[SESSION_GLOBAL_KEY][$key] = $value;
	}

	public static function get($key)
	{
		if (isset($_SESSION[SESSION_GLOBAL_KEY][$key])) {
			return $_SESSION[SESSION_GLOBAL_KEY][$key];
		}
		return null;
	}

	public static function clear($key)
	{
		unset($_SESSION[SESSION_GLOBAL_KEY][$key]);
	}

	public static function generateOneTimeTicket()
	{
		$ticket = md5(uniqid(mt_rand(), true));
		self::set('ticket', $ticket);
		return $ticket;
	}

	public static function isValidOneTimeTicket($ticket)
	{
		$storedTicket = self::get('ticket');
		if (!is_null($storedTicket)) {
			self::clear('ticket');
			return $ticket === $storedTicket;
		}
		return false;
	}
}