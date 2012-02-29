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

	private $controllerClass;
	private $actionName;
	private $requestParams;

	public function HttpHandler()
	{
		$this->request = new HttpRequest();
		$this->response = new HttpResponse();
		$this->controller = null;
	
		$this->controllerClass = null;
		$this->actionName = null;
		$this->requestParams = null;
	}

	/*
	 *	handleRequest([...params...])
	 *		HTTPリクエストを処理して応答を返す。
	 *
	 *	params:
	 *		$controllerClass (optional, String) - URLルートを使わない場合に、クラス名を設定する
	 *		$actionName (optional, String) - URLルートを使わない場合に、コントローラクラスのメソッド名を設定する
	 *		$requestParams (optional, Array) - URLルートを使わない場合に、$_GETに加えるパラムを設定する
	 */
	public function handleRequest($controllerClass = null, $actionName = null, $requestParams = null)
	{
		try {
			try {
				if (is_null($controllerClass)) {
					$this->determineControllerActionFromRoute();
				} else {
					$this->useFixedControllerAction($controllerClass, $actionName, $requestParams);
				}
				$this->executeController();
				if ($this->controller->isError()) {
					throw new Exception($this->controller->getErrorMessage());
				} else if ($this->controller->isRedirect()) {
					$this->response->redirect($this->controller->getRedirectUrl());
					$this->response->respondAndClose();
					return;
				}
			} catch (Exception $e) {
				Logger::error($e);
				$this->executeErrorController($e);
			}
			$this->renderResponse();
		} catch (Exception $e) {
			try {
				Logger::fatal($e);
				$this->response->error(FATAL_ERROR_MESSAGE);
				$this->response->respondAndClose();
			} catch (Exception $e) {
				Logger::fatal($e);
			}
		}
	}

	private function useFixedControllerAction($controllerClass, $actionName, $requestParams = null)
	{
		$this->controllerClass = $controllerClass;
		$this->actionName = $actionName;
		if (!is_null($requestParams)) {
			$this->request->addParams($requestParams);
		}
	}

	private function determineControllerActionFromRoute()
	{
		$router = Router::getRouter();
		defineRoutes($router); //このメソッドは/webapp/config/routes.phpで設定される
		$route = $router->routeCurrent($this->request->getVerb());
		if ($route == null) {
			throw new Exception('HttpHandler -- このURLに一致するルートはありません。');
		}
		$this->controllerClass = $route->getController();
		$this->actionName = $route->getAction();
		$this->request->addParams($route->getParams());
	}

	private function executeController()
	{
		$actionName = $this->actionName;
		$controllerClass = $this->controllerClass;
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass($actionName);
		$this->controller->execute($this->request->getParams());
	}

	private function executeErrorController($error)
	{
		$controllerClass = Router::getRouter()->getErrorController();
		$this->controllerClass = $controllerClass;
		$this->actionName = BaseController::$ERROR_CONTROLLER_ACTION_NAME;
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass(BaseController::$ERROR_CONTROLLER_ACTION_NAME);
		$this->controller->execute($error);
	}

	private function renderResponse()
	{
		//	コントローラ・メソッドに基づいてビューファイルのパスを計算
		$controllerNamePrefix = ClassLoader::classNamePrefix($this->controllerClass);
		$this->templatePath = VIEWS_DIR . DS . $controllerNamePrefix . DS . $this->controller->getRenderAction();

		//	コントローラのデータを最終的なデータ刑に変換する。Smartyなどにより。
		$renderer = BaseRenderer::getRenderer($this->controller->getRenderFormat(), $this->templatePath);
		$renderer->renderHttpResponse($this->controller->getRenderData(), $this->response);
		$this->response->respondAndClose();
	}
}