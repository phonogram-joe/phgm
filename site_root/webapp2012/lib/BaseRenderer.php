<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseRenderer
{
	private static $RENDERERS;
	private static $IS_INITIALIZED = false;

	public $templatePath;

	public static function initialize()
	{
		self::$IS_INITIALIZED = true;
		self::$RENDERERS = array();
	}

	public static function registerRenderer($format, $rendererClass)
	{
		self::$RENDERERS[$format] = $rendererClass;
	}

	public static function getRenderer($format, $templatePath)
	{
		$class = self::$RENDERERS[$format];
		$renderer = new $class($templatePath);
		return $renderer;
	}

	public function BaseRenderer($templatePath)
	{
		$this->templatePath = $templatePath;
		$this->initialize();
	}

	public function initialize()
	{
		
	}

	public function render($data, $httpResponse)
	{
		
	}
}