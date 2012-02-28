<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

require_once(LIB_DIR . DS . 'Route.php');

class Router
{
	private $default_controller;
	private $error_controller;
	private $format_parameter;
	private $url_prefix;

	private $routes;

	private $request_uri;
	private $request_route;

	private static $ROUTER;

	private function __construct()
	{
		$this->url_prefix = '';
		$this->default_controller = null;
		$this->error_controller = null;
		$this->format_parameter = 'format';

		$this->routes = array();

		$this->request_uri = $this->uriToRoutable();
		$this->request_route = $this->routeFor($this->request_uri);
	}

	public static function getRouter()
	{
		if (is_null(self::$ROUTER)) {
			self::$ROUTER = new Router();
		}
		return self::$ROUTER;
	}

	public function routeFor($routable)
	{
		$match = null;
		foreach($this->routes as $route) {
			$match = $route->attemptMatchRoute($routable);
			if (!is_null($match)) {
				return $match;
			}
		}
		return null;
	}

	public function urlFor($controller, $action = null, $params = null, $format = null)
	{
		$url = null;
		foreach ($this->routes as $route) {
			$url = $route->attemptCreateUrl($controller, $action, $params, $format)) {
			if (!is_null($url)) {
				return $url;
			}
		}
		return null;
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
		if (($pos = strpos($uri, '?'))) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = str_replace($this->url_prefix, '', $uri);
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
		if (strlen($this->url_prefix) > 0)
			if (!preg_match('#/$#', $routable)) {
				$prefix = $this->url_prefix . '/';
			} else {
				$prefix = $this->url_prefix;
			}
		}
		return $prefix . $routable;
	}
}