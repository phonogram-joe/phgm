<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class BaseRenderer
{
	private static $RENDERERS;
	private static $RENDERER_EXTENSIONS;
	private static $IS_INITIALIZED = false;

	public $templatePath;
	public $format;

	public static function classInitialize()
	{
		self::$IS_INITIALIZED = true;
		self::$RENDERERS = array();
		self::$RENDERER_EXTENSIONS = array();
	}

	public static function registerRenderer($format, $rendererClass, $extension)
	{
		self::$RENDERERS[$format] = $rendererClass;
		self::$RENDERER_EXTENSIONS[$format] = $extension;
	}

	public static function getRenderer($format, $templatePath)
	{
		if (isset(self::$RENDERERS[$format])) {
			$className = self::$RENDERERS[$format];
			$extension = self::$RENDERER_EXTENSIONS[$format];
		} else {
			throw new Exception('BaseRenderer::getRenderer -- ' . $format . 'のレンダラは設定されてない。');
		}
		$renderer = new $className($format, $templatePath . $extension);
		$renderer->initialize();
		return $renderer;
	}

	public function BaseRenderer($format, $templatePath)
	{
		$this->templatePath = $templatePath;
		$this->format = $format;
	}

	public function initialize()
	{
		
	}

	public function renderHttpResponse($data, $httpResponse)
	{
		$output = $this->customRender($data);

		$httpResponse->setContentTypeCharset(HttpResponseFormat::mimeType($this->format), HttpResponseFormat::charset($this->format));
		$httpResponse->setEncoding(HttpResponseFormat::encoding($this->format));
		$httpResponse->setResponse($output);
		$this->customHttpResponse($data, $httpResponse);
	}

	public function renderFetch($data)
	{
		return $this->customRender($data);
	}

	public function customRender($data, $httpResponse)
	{
	}

	public function customHttpResponse($data, $httpResponse)
	{
		
	}
}