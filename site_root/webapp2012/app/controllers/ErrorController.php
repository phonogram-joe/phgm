<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */

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