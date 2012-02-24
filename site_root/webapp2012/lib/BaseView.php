<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseView
{
	private $templatePath;
	private $templateOutput;
	private $originalControllerName;
	private $originalActionName;
	private $convertedControllerName;

	public function BaseView($controllerName, $actionName)
	{
		$this->originalControllerName = $controllerName;
		$this->originalActionName = $actionName;
		$controllerName = StringUtils::camelToUnderscores(preg_replace('/Controller/', '', $controllerName));
		$this->convertedControllerName = $controllerName;
		$this->templatePath = VIEWS_DIR . DS . $controllerName . DS . $actionName . '.html';
		if (!file_exists($this->templatePath)) {
			$this->templatePath = null;
		}
	}

	public function isTemplateValid()
	{
		return !is_null($this->templatePath);
	}

	public function render($data)
	{
		$this->templateOutput = $this->renderCustom($data);
	}

	public function renderCustom($data)
	{
		return file_get_contents($this->templatePath);
	}

	public function getOutput()
	{
		var_dump($this->originalControllerName);
		var_dump($this->originalActionName);
		var_dump($this->convertedControllerName);
		return $this->templateOutput;
	}
}