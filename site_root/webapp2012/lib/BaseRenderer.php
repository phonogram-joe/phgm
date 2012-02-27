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
	public $format;

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
		$className = self::$RENDERERS[$format];
		$renderer = new $className($format, $templatePath);
		return $renderer;
	}

	public function BaseRenderer($format, $templatePath)
	{
		$this->templatePath = $templatePath;
		$this->format = $format;
	}

	public function render($data, $httpResponse)
	{
		$httpResponse->setContentTypeCharset(HttpResponseFormat::mimeType($this->format), HttpResponseFormat::charset($this->format));
		$httpResponse->setEncoding(HttpResponseFormat::encoding($this->format));
		$this->customRender($data, $httpResponse);
	}

	public function customRender($data, $httpResponse)
	{
		$this->templatePath .= '.html';
		if (file_exists($this->templatePath)) {
			$contents = file_get_contents($this->templatePath);
			$format = HttpResponseFormat::$TEXT;
		} else {
			$format = HttpResponseFormat::$JSON;
			$contents = json_encode($data);
		}
		$httpResponse->setContentTypeCharset(HttpResponseFormat::mimeType($format), HttpResponseFormat::charset($format));
		$httpResponse->setResponse($contents);
	}
}