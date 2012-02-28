<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpRequest
{
	private $params;
	private $httpVerb;

	public function HttpRequest()
	{
		$this->params = array();
		$this->setParams();
		$this->setVerb();
	}

	private function setParams()
	{
		//TODO: combine input from $_GET and $_POST, also read in upload files
		$params = array();
		foreach ($_GET as $key => $value) {
			$params[$key] = $value;
		}

		foreach ($_POST as $key => $value) {
			$params[$key] = $value;
		}

		foreach ($_FILES as $key => $value) {
			if (is_array($value)) {
				$file = null; //TODO: new UploadFile($value)
				$params[$key] = $file;
			} else {
				$params[$key] = $value;
			}
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
		//TODO: use either $_SERVER['REQUEST_METHOD'] or alternately read from a specific param to override it (in case of non GET/POST verbs)
		$this->httpVerb = strtolower($_SERVER['REQUEST_METHOD']);
		if (defined('HTTP_METHOD_PARAM') && array_key_exists(HTTP_METHOD_PARAM, $this->params)) {
			$this->httpVerb = $this->params[HTTP_METHOD_PARAM];
		}
	}

	public function getVerb()
	{
		return $this->httpVerb;
	}
}