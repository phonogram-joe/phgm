<?php

class ErrorController extends BaseController
{
	public $errorMessage;
	public $errorTrace;
		
	public function handleError($error)
	{
		$environment = Config::get(Config::ENVIRONMENT);
		if ($environment === Config::ENVIRONMENT_DEVELOPMENT) {
			$this->pageTitle = 'エラー';
			$this->params = $this->getRequest()->getParams();
			$this->config = Config::getAll();
			$this->routes = Router::getRouter()->getAllRoutes();
			$errorMessage = null;
			$errorTrace = null;
			try {
				if (is_null($error)) {
					$errorMessage = '不明なエラー';
					$errorTrace = null;
				} else if (is_object($error)) {
						$errorMessage = $error->getMessage();
						$errorTrace = $error->getTraceAsString();
				} else if (is_string($error)) {
					$errorMessage = $error;
					$errorTrace = null;
				}
				$this->errorMessage = $errorMessage;
				$this->errorTrace = $errorTrace;
			} catch (Exception $e) {
				@Logger::error($e->getMessage);
			}
			return $this->doRender();
		} else {
			if (is_object($error)) {
				throw $error;
			} else if (is_string($error)) {
				throw new Exception($error);
			} else {
				throw new Exception('ErrorController::handleError() -- 不明なエラー。');
			}
		}
	}

	public function showConfig($params)
	{
		$this->config = Config::getAll();
		$this->routes = Router::getRouter()->getAllRoutes();
		return $this->doRender();
	}
}