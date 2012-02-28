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
	private $encoding;

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
			$this->setEncoding(null); //reset encoding
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
		return $this->state === self::$STATE_HEADERS || $this->state === self::$STATE_OPEN;
	}

	public function redirect($url)
	{
		if ($this->isEditable()) {
			$this->headers = array('Location: ' . $url);
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
		}
	}

	public function error($message)
	{
		if ($this->isEditable()) {
			$this->headers = array('HTTP/1.0 404 Not Found');
			$this->responseBody = $message;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、ヘッダーを設定することは');
		}
	}

	public function setContentTypeCharset($mimeType, $charset)
	{
		if ($this->isEditable()) {
			$this->headers[] = 'Content-type: ' . $mimeType . '; charset=' . $charset;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、応答のデータ刑を変更することはできません。');
		}
	}

	public function setHeader($header)
	{
		if ($this->isEditable()) {
			$this->headers[] = $header;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、応答のデータ刑を変更することはできません。');
		}
	}

	public function setResponse($responseBody)
	{
		if ($this->isEditable()) {
			if ($this->encoding != mb_internal_encoding()) {
				$responseBody = mb_convert_encoding($responseBody, $this->encoding);
			}
			$this->headers[] = 'Content-length: ' . strlen($responseBody);
			$this->responseBody = $responseBody;
		} else {
			throw new Exception('HttpResponse:redirect -- HTTP応答は返し中なので、リダイレクトはできません。');
		}
	}

	public function setEncoding($encoding)
	{
		if (is_null($encoding) || strlen(trim($encoding)) === 0) {
			$encoding = mb_http_output();
			if ($encoding === 'pass') {
				$encoding = mb_internal_encoding();
			}
		}
		$this->encoding = $encoding;
	}

	public function writeHeaders()
	{
		if ($this->state != self::$STATE_OPEN && $this->state != self::$STATE_HEADERS) {
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
		if ($this->state != self::$STATE_HEADERS && $this->state != self::$STATE_RESPONSE) {
			throw new Exception('HttpResponse::writeResponse -- HTTP応答内容を返せません。ヘッダーはまだ返してないまたは応答はすでに閉じています。');
		}
		$this->state = self::$STATE_RESPONSE;
		if (!is_null($this->responseBody)) {
			print $this->responseBody;
		}
		$this->responseBody = null;
	}

	public function close()
	{
		$this->headers = null;
		$this->responseBody = null;
		$this->state = self::$STATE_CLOSED;
	}

	public function respondAndClose()
	{
		$this->writeHeaders();
		$this->writeResponse();
		$this->close();
	}
}