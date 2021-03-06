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
	public function handleRequest($routeName = null, $routeParams = null)
	{
		try {
			Profiler::getProfiler()->startSnapshot('handleRequest');
			if (is_null($routeName)) {
				$this->determineControllerActionFromRoute();
			} else {
				$this->useFixedControllerAction($routeName, $routeParams);
			}
			
			$httpMethod = strtoupper($this->request->getVerb());
			if (false !== array_search($httpMethod, array(HttpRequest::POST, HttpRequest::PUT, HttpRequest::DELETE))) {
				$db = DB::getSession();
				if (!is_null($db)) {
					//	POST・PUT・DELETEのリクエストに対してDBの変更・購入・削除を有効にする
					$db->setAllowUpdates(true);
				}

				if (!$this->request->isFormSafe()) {
					//	XSRFの対策で、フォームにはランダムなキーを埋め込まないといけない。{{formSafe}}というSmartyメソッドを使ってください。
					throw new Exception('HttpHandler:handleRequest() -- フォームの提出者を確認できません。');
				}
			}
			$this->executeController();
			if ($this->controller->isError()) {
				throw new Exception($this->controller->getErrorMessage());
			} else if ($this->controller->isRedirect()) {
				$this->response->redirect($this->controller->getRedirectUrl());
				$this->response->respondAndClose();
				return;
			} else {
				Profiler::getProfiler()->stopSnapshot('handleRequest');
				$this->renderResponse();
				return;
			}
		} catch (Exception $e) {
			Logger::error($e);
			if ($this->response->isEditable()) {
				$this->response->reset();
			} else {
				throw $e; //HTTPレスポンスは既に閉じられたのでエラーを表示できない。
			}
			$this->executeErrorController($e);
			$this->renderResponse();
			return;
		}
	}

	private function useFixedControllerAction($routeName, $routeParams)
	{
		$route = Router::getRouter()->getNamedRoute($routeName);
		if (is_null($route)) {
			throw new Exception('HttpHandler:useFixedControllerAction() -- 「' . $routeName . '」というルートは設定されいてないです。');
		}
		$this->controllerClass = $route->getController();
		$this->actionName = $route->getAction();
		$this->request->addParams($routeParams);
	}

	private function determineControllerActionFromRoute()
	{
		$router = Router::getRouter();
		Profiler::getProfiler()->startSnapshot('routing');
		$match = $router->routeRequest($this->request);
		Profiler::getProfiler()->stopSnapshot('routing');
		if ($match == null) {
			throw new Exception('HttpHandler -- 「' . $this->request->toString() . '」URLに一致するルートはありません。');
		}
		$this->controllerClass = $match->getController();
		$this->actionName = $match->getAction();
		$this->request->addParams($match->getParams());
	}

	private function executeController()
	{
		$actionName = $this->actionName;
		$controllerClass = $this->controllerClass;
		Profiler::getProfiler()->startSnapshot('loadController');
		ClassLoader::load($controllerClass);
		Profiler::getProfiler()->stopSnapshot('loadController');

		Profiler::getProfiler()->startSnapshot('controller');
		$this->controller = new $controllerClass($this->request, $this->response, $actionName);
		$this->controller->execute($this->request->getParams());
		Profiler::getProfiler()->stopSnapshot('controller');
	}

	private function executeErrorController($error)
	{
		$controllerClass = Router::getRouter()->getErrorController();
		$this->controllerClass = $controllerClass;
		$this->actionName = BaseController::$ERROR_CONTROLLER_ACTION_NAME;
		ClassLoader::load($controllerClass);

		$this->controller = new $controllerClass($this->request, $this->response, BaseController::$ERROR_CONTROLLER_ACTION_NAME);
		$this->controller->execute($error);
	}

	private function renderResponse()
	{
		//	コントローラ・メソッドに基づいてビューファイルのパスを計算
		$controllerNamePrefix = ClassLoader::classNamePrefix($this->controllerClass);
		$renderAction = ClassLoader::camelToUnderscores($this->controller->getRenderAction());
		$this->templatePath = ClassLoader::$APP_VIEWS_DIR . DS . $controllerNamePrefix . DS . $renderAction;

		//	コントローラのデータを最終的なデータ形に変換する。Smartyなどにより。
		$renderer = BaseRenderer::getRenderer($this->controller->getRenderFormat(), $this->templatePath);
		Profiler::getProfiler()->startSnapshot('rendering');
		$renderer->renderHttpResponse($this->controller->getRenderData(), $this->request, $this->response);
		$this->response->respondAndClose();
	}
}