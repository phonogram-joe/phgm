<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

class HttpHandler
{
	private $request;
	private $response;
	private $controller;
	private $action;

	public function HttpHandler()
	{
		$router = null;
		$route = null;
		$controllerClass = null;
		$action = null;
		$params = null;

		try {
			$router = Router::getInstance();
			defineRoutes($router); // URLの設定 webapp/config/routes.php
			$route = $router->matchRoute();
			if ($route == null) {
				$controllerClass = $router->get404Controller();
				$action = $router->getDefaultAction();
			} else {
				$controllerClass = $route->getController();
				$action = $route->getAction();
			}
			ClassLoader::load(CONTROLLER, $controllerClass);

			if (!class_exists($controllerClass)) {
				throw new Exception();//'クラス　'　. $controllerClass . 'は存在しません。');
			}
			$this->controller = new $controllerClass();
			if (!method_exists($this->controller, $action)) {
				throw new Exception();//'クラス　' . $controllerClass . 'には' . $action . 'というメソッドがありません。');
			}
			$params = is_null($route) ? array() : $route->getParams();

			$this->action = $action;
			$this->request = new HttpRequest($params);
			$this->response = new HttpResponse();

		} catch (Exception $e) {
			print $e->getMessage();
			return;
		}
	}

	public function handleRequest()
	{
		$controller = $this->controller;
		$action = $this->action;
		$request = $this->request;
		$response = $this->response;

		if (is_null($this->controller)) {
			print('エラーが発生しました。');
			return;
		}
		$controller->$action($request, $response);
	}
}