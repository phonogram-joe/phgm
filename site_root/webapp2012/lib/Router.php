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

	public function routeCurrent($httpVerb)
	{
		$routable = $this->uriToRoutable();
		return $this->routeFor($routable, $httpVerb);
	}

	public function routeFor($routable, $httpVerb = null)
	{
		$match = null;
		foreach($this->routes as $route) {
			$match = $route->attemptMatchRoute($routable, $httpVerb);
			if (!is_null($match)) {
				return $match;
			}
		}
		return null;
	}

	public function urlForName($name, $params = array())
	{
		$controller = $this->routes[$name]->getController();
		$action = $this->routes[$name]->getAction();
		$url = $this->routes[$name]->attemptCreateUrl($controller, $action, $params);
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
	 *	mapRest($namePrefix, $urlPrefix, $controller)
	 *		shortcut for mapping a RESTful controller
	 */
	public function mapRest($namePrefix, $urlPrefix, $controller)
	{
		$this->map($namePrefix . '_index', 			'GET  ' . $urlPrefix,	 				$controller, 'index');
		$this->map($namePrefix . '_new_form', 		'GET  ' . $urlPrefix . '/new', 			$controller, 'newForm');
		$this->map($namePrefix . '_new_save', 		'POST ' . $urlPrefix . '/new', 			$controller, 'newSave');
		$this->map($namePrefix . '_show', 			'GET  ' . $urlPrefix . '/#id', 			$controller, 'show');
		$this->map($namePrefix . '_edit_form', 		'GET  ' . $urlPrefix . '/#id/edit', 	$controller, 'editForm');
		$this->map($namePrefix . '_edit_save', 		'PUT ' . $urlPrefix . '/#id/edit', 	$controller, 'editSave');
		$this->map($namePrefix . '_delete_form',	'GET  ' . $urlPrefix . '/#id/delete', 	$controller, 'deleteForm');
		$this->map($namePrefix . '_delete_save',	'DELETE ' . $urlPrefix . '/#id/delete', 	$controller, 'deleteSave');
	}

	/*
	 *	mapForm($namePrefix, $urlPrefix, $controller)
	 *		shortcut for mapping a RESTful form controller with 3 screens (input, confirm, and complete). Input & Confirm
	 *		share URLs with GET/POST distinguishing. Complete screen has separate URL.
	 */
	public function mapForm($namePrefix, $urlPrefix, $controller)
	{
		$this->map($namePrefix . '_form', 		'GET  ' . $urlPrefix, 				$controller, 'form');
		$this->map($namePrefix . '_confirm', 	'POST ' . $urlPrefix, 				$controller, 'confirm');
		$this->map($namePrefix . '_complete', 	'GET  ' . $urlPrefix . '/complete', $controller, 'complete');
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
			$routable = substr($routable, 1);
		}
		$routable = trim($routable);
		if (strlen($this->urlPrefix) > 0) {
			if (!preg_match('/\/$/', $this->urlPrefix)) {
				$prefix = $this->urlPrefix . '/';
			} else {
				$prefix = $this->urlPrefix;
			}
		}
		return $prefix . $routable;
	}
}