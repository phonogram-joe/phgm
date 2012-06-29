<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpRequest
{
	private $params;
	private $httpVerb;
	private $session;
	private $originalUri;

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	const HTTP_JSON_TYPE = 'application/json';

	public function HttpRequest()
	{
		$this->params = array();
		$this->setParams();
		$this->setVerb();
		$this->setSession();
		$this->originalUri = $_SERVER['REQUEST_URI'];

		Logger::info($this->httpVerb . ' ' . $this->originalUri . ' ' . $_SERVER['REMOTE_ADDR']);
	}

	public static function isHttpMethod($method)
	{
		if (false !== array_search($method, array(self::GET, self::POST, self::PUT, self::DELETE))) {
			return true;
		}
		return false;
	}

	public function toString()
	{
		return strtoupper($this->httpVerb) . ' ' . $this->originalUri;
	}

	public function getOriginalUri()
	{
		return $this->originalUri;
	}	

	private function setSession()
	{
		if (Config::get(Config::SESSIONS_ENABLED)) {
			$this->session = Session::makeSession();
		} else {
			$this->session = null;
		}
	}

	public function getSession()
	{
		return $this->session;
	}

	private function setParams()
	{
		$params = array();
		foreach ($_GET as $key => $value) {
			$params[$key] = $value;
		}

		$contentType = $_SERVER['CONTENT_TYPE'];
		if ($contentType === self::HTTP_JSON_TYPE) {
			$json = file_get_contents(stripslashes('php://input'));
			$postData = json_decode($json);
		} else {
			$postData = $_POST;
		}
		foreach ($postData as $key => $value) {
			$params[$key] = $value;
		}

		foreach ($_FILES as $key => $value) {
			$params[$key] = $value;
		}
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function addParams($params)
	{
		$this->params = array_merge($this->params, $params);
	}

	private function setVerb()
	{
		$this->httpVerb = $_SERVER['REQUEST_METHOD'];
		$verbKey = Config::get(Config::HTTP_METHOD_PARAM);
		if (array_key_exists($verbKey, $this->params)) {
			$this->httpVerb = $this->params[$verbKey];
		}
		$this->httpVerb = strtoupper($this->httpVerb);
	}

	public function getVerb()
	{
		return $this->httpVerb;
	}

	public function isFormSafe()
	{
		$formSafeKey = Config::get(Config::FORM_SAFE_KEY);
		if (isset($this->params[$formSafeKey])) {
			return $this->session->isValidNonce($this->params[$formSafeKey]);
		}
		Logger::trace('HttpRequest:isFormSafe() -- フォームの提出者を確認できません。');
		return false;
	}

}






