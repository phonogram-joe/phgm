<?php

class ErrorController extends BaseController
{
	public $errorMessage;
	public $errorTrace;
		
	public function handleError($error)
	{
		if (is_null($error)) {
			$this->errorMessage = '不明なエラー';
		} else if (is_object($error)) {
			$this->errorMessage = $error->getMessage();
			$this->errorTrace = $error->getTraceAsString();
		} else if (is_string($error)) {
			$this->errorMessage = $error;
			$this->errorTrace = null;
		}
		return $this->doRender();
	}
}