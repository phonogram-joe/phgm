<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class RouteMatch
{
	private $controller;
	private $action;
	private $params;

	public function RouteMatch($controller, $action, $params)
	{
		$this->controller = $controller;
		$this->action = $action;
		$this->params = $params;
	}	

	public function getController()
	{
		return $this->controller;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getParams()
	{
		return $this->params;
	}
}