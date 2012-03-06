<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class Session
{
	public function Session()
	{
	}

	public function getUser()
	{
		if (isset($_SESSION[SESSION_USER_KEY])) {
			return $_SESSION[SESSION_USER_KEY];
		}
		return null;
	}

	public function setUser($user)
	{
		$_SESSION[SESSION_USER_KEY] = $user;
	}

	public function clearUser()
	{
		$user = $this->getUser();
		unset($_SESSION[SESSION_USER_KEY]);
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
		$_SESSION[SESSION_GLOBAL_KEY][$key] = $value;
	}

	public function get($key)
	{
		if (isset($_SESSION[SESSION_GLOBAL_KEY][$key])) {
			return $_SESSION[SESSION_GLOBAL_KEY][$key];
		}
		return null;
	}

	public function clear($key)
	{
		$value = $this->get($key);
		unset($_SESSION[SESSION_GLOBAL_KEY][$key]);
		return $value;
	}

	public function generateOneTimeTicket()
	{
		$ticket = md5(uniqid(mt_rand(), true));
		$this->set('ticket', $ticket);
		return $ticket;
	}

	public function isValidOneTimeTicket($ticket)
	{
		$storedTicket = $this->get('ticket');
		if (!is_null($storedTicket)) {
			$this->clear('ticket');
			return $ticket === $storedTicket;
		}
		return false;
	}
}