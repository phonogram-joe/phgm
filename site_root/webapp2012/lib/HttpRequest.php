<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpRequest
{
	private $params;

	public function HttpRequest()
	{
		$this->params = array();
	}

	public function setParams($params)
	{
		$this->params = $params;
	}
	public function getParams($params)
	{
		return $this->params;
	}
}