<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

require_once(LIB_DIR . DS . 'Route.php');

class Router
{
	public static $DEFAULT_HTTP_METHOD_PARAMETER = '__http_method';

	private $defaultController;
	private $errorController;
	private $urlPrefix;

	private $routes;

	private static $ROUTER;

	private function __construct()
	{
		$this->urlPrefix = '';
		$this->defaultController = null;
		$this->errorController = null;

		$this->routes = array();
	}

	public static function getRouter()
	{
		if (is_null(self::$ROUTER)) {
			self::$ROUTER = new Router();
		}
		return self::$ROUTER;
	}

	public function setUrlPrefix($prefix)
	{
		$this->urlPrefix = $prefix;
	}	
	public function getUrlPrefix()
	{
		return $this->urlPrefix;
	}

	public function setDefaultController($controller)
	{
		$this->defaultController = $controller;
	}	
	public function getDefaultController()
	{
		return $this->defaultController;
	}

	public function setErrorController($controller)
	{
		$this->errorController = $controller;
	}	
	public function getErrorController()
	{
		return $this->errorController;
	}

	public function routeCurrent($httpRequest)
	{
		$routable = $this->uriToRoutable();
		return $this->routeFor($routable, $httpRequest);
	}

	public function routeFor($routable, $httpRequest = null)
	{
		$match = null;
		foreach($this->routes as $route) {
			$match = $route->attemptMatchRoute($routable, $httpRequest);
			if (!is_null($match)) {
				return $match;
			}
		}
		return null;
	}

	public function urlForName($name, $params = array())
	{
		$url = $this->routes[$name]->attemptCreateUrl(null, $action, $params);
		return $this->routableToUri($url);
	}

	public function urlForRoute($controller, $action, $params = null)
	{
		$url = null;
		foreach ($this->routes as $route) {
			$url = $route->attemptCreateUrl($controller, $action, $params);
			if (!is_null($url)) {
				return $this->routableToUri($url);
			}
		}
		return null;
	}

	public function map($name, $verbUrl, $controller, $action, $params = array(), $conditions = array())
	{
		$newRoute = new Route($name, $verbUrl, $controller, $action, $params, $conditions);

		foreach ($this->routes as $route) {
			if ($route->isConflict($newRoute)) {
				throw new Exception('Router:map -- 新ルート' . $name . 'は登録されてるルート' . $route->getName() . 'とぶつかります。');
			}
		}
		$this->routes[$name] = $newRoute;
	}

	/*
	 *	uriToRoutable([$uri])
	 *		リクエストURI(デフォルト）または渡されたURIを、ルートできるURIに変換する。接頭辞やクエリパラムがある場合それを取ってくれる。
	 */
	public function uriToRoutable($uri = null)
	{
		if (is_null($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
		}
		$pos = strpos($uri, '?');
		if ($pos) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = str_replace($this->urlPrefix, '', $uri);
		if (strpos($uri, '/') !== 0) {
			$uri = '/' . $uri;
		}
		return $uri;
	}

	/*
	 *	routableToUri($routable)
	 *		ルートできるパスを本番のパスに変換する。接頭辞がある場合にそれを付けてくれる。
	 */
	public function routableToUri($routable)
	{
		$prefix = '';
		if (strpos($routable, '/') === 0) {
			$routable = substr($rou, 1);
		}
		if (strlen($this->urlPrefix) > 0) {
			if (!preg_match('#/$#', $routable)) {
				$prefix = $this->urlPrefix . '/';
			} else {
				$prefix = $this->urlPrefix;
			}
		}
		return $prefix . $routable;
	}
}