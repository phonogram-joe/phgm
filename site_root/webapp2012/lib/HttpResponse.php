<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpResponse
{
	public static $STATE_OPEN = 0;
	public static $STATE_HEADERS = 1;
	public static $STATE_RESPONSE = 2;
	public static $STATE_CLOSED = 3;

	private $state;
	private $headers;
	private $responseBody;

	public function HttpResponse()
	{
		$this->state = self::$STATE_OPEN;
		$this->reset();
	}

	public function reset()
	{
		if ($this->isEditable()) {
			$this->headers = array();
			$this->responseBody = null;
			return true;
		}
		return false;
	}

	public function isClosed()
	{
		return $this->state === self::$STATE_CLOSED;
	}

	public function isEditable()
	{
		return $this->state === self::$STATE_OPEN || $this->state === self::$STATE_HEADERS;
	}

	public function redirect($url)
	{
		if ($this->isEditable()) {
			header('Location: ' . $url);
			$this->state = self::$STATE_CLOSED;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
		}
	}

	public function error($message)
	{
		header('HTTP/1.0 404 Not Found');
		$this->state = self::$STATE_CLOSED;
	}

	public function setContentTypeCharset($format, $charset = null)
	{
		if ($this->isEditable()) {
			$this->headers[] = 'Content-type: ' . HttpResponseFormat::mimeFor($format) . '; charset=' . HttpResponseFormat::charset($charset);
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、応答のデータ刑を変更することはできません。');
		}
	}

	public function setResponse($responseBody)
	{
		if ($this->isEditable()) {
			$this->responseBody = $responseBody;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
		}
	}

	public function writeHeaders()
	{
		if ($this->state != self::$STATE_OPEN || $this->state != self::$STATE_HEADERS) {
			throw new Exception('HttpResponse::writeHeaders -- HTTPヘッダーは既に返しています。');
		}
		$this->state = self::$STATE_HEADERS;
		foreach ($this->headers as $header) {
			header($header);
		}
		$this->headers = array();
	}

	public function writeResponse()
	{
		if ($this->state != self::$STATE_HEADERS || $this->state != self::$STATE_RESPONSE) {
			throw new Exception('HttpResponse::writeResponse -- HTTP応答内容を返せません。ヘッダーはまだ返してないまたは応答はすでに閉じています。');
		}
		$this->state = self::$STATE_RESPONSE;
		print $this->responseBody;
		$this->responseBody = null;
	}

	public function close()
	{
		$this->state = self::$STATE_CLOSED;
	}
}