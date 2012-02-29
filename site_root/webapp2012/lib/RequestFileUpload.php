<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class RequestFileUpload
{
	private $fileName;
	private $mimeType;
	private $tmpPath;
	private $error;
	private $size;

	public function RequestFileUpload($params)
	{
		$this->fileName = null;
		$this->mimeType = null;
		$this->tmpPath = null;
		$this->error = null;
		$this->size = null;

		if (array_key_exists('name', $params)) {
			$this->fileName = $params['name'];
		}
		if (array_key_exists('type', $params)) {
			$this->mimeType = $params['type'];
		}
		if (array_key_exists('tmp_name', $params)) {
			$this->tmpPath = $params['tmp_name'];
		}
		if (array_key_exists('error', $params)) {
			$this->error = $params['error'];
		}
		if (array_key_exists('size', $params)) {
			$this->size = $params['size'];
		}
	}

	public function getFileName()
	{
		return $this->fileName;
	}
	public function getMimeType()
	{
		return $this->mimeType;
	}
	public function getTmpPath()
	{
		return $this->tmpPath;
	}
	public function getError()
	{
		return $this->error;
	}
	public function getSize()
	{
		return $this->size;
	}
}