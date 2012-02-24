<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpResponse
{
	//	HTTP応答内容のデータ刑
	public static $FORMAT_HTTP_STATUS_ONLY = 'http';
	public static $FORMAT_HTML = 'html';
	public static $FORMAT_JSON = 'json';
	public static $FORMAT_XML = 'xml';
	public static $FORMAT_CSV = 'csv';
	public static $FORMAT_TEXT = 'text';

	public static $STATE_OPEN = 0;
	public static $STATE_HEADERS = 1;
	public static $STATE_RESPONSE = 2;
	public static $STATE_CLOSED = 3;

	private $state;
	private $format;
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
			$this->format = self::$FORMAT_HTML;
			$this->headers = array();
			$this->responseBody = null;
			return true;
		}
		return false;
	}

	public function isEditable()
	{
		return $this->state === self::$STATE_OPEN;
	}

	public function redirect($url)
	{
		if ($this->isEditable()) {
			$this->format = self::$FORMAT_HTTP_STATUS_ONLY;
			$this->headers[] = 'Location: ' . $url; //TODO: redirect the browser
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
		}
	}

	public function error($message)
	{
		header('HTTP/1.0 404 Not Found');
		$this->state = self::$STATE_CLOSED;
	}

	public function setFormat($format)
	{
		if ($this->isEditable()) {
			$this->format = $format;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
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
		//TODO: write headers & then clear them (so they won't be written again)
		foreach ($this->head as $header) {
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
		if ($this->format === self::$FORMAT_HTTP_STATUS_ONLY) {
			return;
		}
		print $this->responseBody;
		$this->responseBody = null;
	}

	public function finishResponse()
	{
		$this->state = self::$STATE_CLOSED;
	}
}