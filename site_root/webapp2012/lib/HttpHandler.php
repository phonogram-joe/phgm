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
	private $view;

	private $controllerClass;
	private $actionName;
	private $requestFormat;
	private $requestParams;

	public function HttpHandler()
	{
		$this->request = new HttpRequest();
		$this->response = new HttpResponse();
		$this->controller = null;
		$this->view = null;
	
		$this->controllerClass = null;
		$this->actionName = null;
		$this->requestFormat = null;
		$this->requestParams = null;
	}

	public function handleRequest()
	{
		try {
			try {
				$this->determineControllerActionFromRoute();
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
			$this->renderView();
		} catch (Exception $e) {
			$this->response->error($e->getMessage());
		}
	}

	private function determineControllerActionFromRoute()
	{
		$router = Router::getInstance();
		defineRoutes($router);
		$route = $router->matchRoute();
		if ($route == null) {
			$this->controllerClass = $router->getDefaultController();
			$this->actionName = $router->getDefaultAction();
			$this->requestParams = array_merge($_GET);
		} else {
			$this->controllerClass = $route->getController();
			$this->actionName = $route->getAction();
			$this->requestParams = array_merge($_GET, $route->getParams());
		}
	}

	private function executeController()
	{
		$actionName = $this->actionName;
		$controllerClass = $this->controllerClass;
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass($actionName, HttpResponse::$FORMAT_HTML);
		$this->controller->execute($this->requestParams);
	}

	private function executeErrorController($errorMessage)
	{
		$controllerClass = Router::getInstance()->get404Controller();
		ClassLoader::load(CONTROLLER, $controllerClass);

		$this->controller = new $controllerClass();
		$this->controller->execute($errorMessage);
	}

	private function renderView()
	{
		//should alternate view based on render format
		$view = new BaseView($this->controllerClass, $this->controller->getRenderAction());
		$view->render($this->controller->getRenderData());
		print $view->getOutput();
	}
}