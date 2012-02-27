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
	private $requestFormat;
	private $requestParams;

	public function HttpHandler()
	{
		$this->request = new HttpRequest();
		$this->response = new HttpResponse();
		$this->controller = null;
	
		$this->controllerClass = null;
		$this->actionName = null;
		$this->requestFormat = null;
		$this->requestParams = null;
	}

	/*
	 *	handleRequest([...params...])
	 *		HTTPリクエストを処理して応答を返す。
	 *
	 *	params:
	 *		$controllerClass (optional, String) - URLルートを使わない場合に、クラス名を設定する
	 *		$actionName (optional, String) - URLルートを使わない場合に、コントローラクラスのメソッド名を設定する
	 *		$requestFormat (optional, String) - URLルートを使わない場合に、応答のデータ刑を設定する
	 *		$requestParams (optional, Array) - URLルートを使わない場合に、$_GETに加えるパラムを設定する
	 */
	public function handleRequest($controllerClass = null, $actionName = null, $requestFormat = null, $requestParams = null)
	{
		try {
			try {
				if (issnull($controllerClass)) {
					$this->determineControllerActionFromRoute();
				else {
					$this->useFixedControllerAction($controllerClass, $actionName, $requestFormat, $requestParams);
				}
				$this->executeController();
				if ($this->controller->isError()) {
					throw new Exception($this->controller->getErrorMessage());
				}
				if ($this->controller->isRedirect()) {
					$this->response->redirect($this->controller->getRedirectUrl());
					return;
				}
			} catch (Exception $e) {
				$this->executeErrorController($e->getMessage());
			}
			$this->renderReponse();
		} catch (Exception $e) {
			try {
				$this->response->error($e->getMessage());
				$this->response->respondAndClose();
			} catch (Exception $e) {
				Logger::getLogger->logException(LOG_FATAL, $e);
			}
		}
	}

	private function useFixedControllerAction($controllerClass = null, $actionName = null, $requestFormat = null, $requestParams = null)
	{
		$this->controllerClass = $controllerClass;
		$this->actionName = $actionName;
		$this->requestFormat = isnull($requestFormat) ? HttpResponseFormat::getDefaultFormat() : $requestFormat;
		$this->requestParams = isnull($requestParams) ? array_merge($_GET) : array_merge($_GET, $requestParams);
	}

	private function determineControllerActionFromRoute()
	{
		$router = Router::getInstance();
		defineRoutes($router);
		$route = $router->matchRoute();
		if ($route == null) {
			$this->controllerClass = $router->getDefaultController();
			$this->actionName = $router->getDefaultAction();
			$this->requestFormat = HttpResponseFormat::getDefaultFormat();
			$this->requestParams = array_merge($_GET);
		} else {
			$this->controllerClass = $route->getController();
			$this->actionName = $route->getAction();
			$this->requestFormat = $route->getFormat();
			if (isnull($this->requestFormat)) {
				$this->requestFormat = HttpResponseFormat::getDefaultFormat();
			}
			$this->requestParams = array_merge($_GET, $route->getParams());
		}
	}

	private function executeController()
	{
		$actionName = $this->actionName;
		$controllerClass = $this->controllerClass;
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass($actionName, $this->requestFormat);
		$this->controller->execute($this->requestParams);
	}

	private function executeErrorController($errorMessage)
	{
		$controllerClass = Router::getInstance()->get404Controller();
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass($this->requestFormat);
		$this->controller->execute($errorMessage);
	}

	private function renderReponse()
	{
		//	コントローラ・メソッドに基づいてビューファイルのパスを計算
		$templateController = StringUtils::camelToUnderscores(preg_replace('/Controller/', '', $this->controllerName));
		$this->templatePath = VIEWS_DIR . DS . $templateControllerPart . DS . $this->controller->getRenderAction();

		//	コントローラのデータを最終的なデータ刑に変換する。Smartyなどにより。
		$renderer = BaseRenderer::getRenderer($this->controller->getRenderFormat(), $this->templatePath);
		$renderer->render($this->controller->getRenderData(), $this->response);
		$this->response->respondAndClose();
	}
}