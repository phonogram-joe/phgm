<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
class BaseController
{
	public static $ERROR_CONTROLLER_ACTION_NAME = 'handleError';
	private static $RESULT_REDIRECT = 0;
	private static $RESULT_RENDER = 1;
	private static $RESULT_ERROR = 2;

	private $_phgmDefaultActionName;
	private $_phgmDefaultRenderFormat;
	private $_phgmActionName;
	private $_phgmRenderActionName;
	private $_phgmRedirectUrl;
	private $_phgmErrorMessage;
	private $_phgmRenderFormat;
	private $_phgmRenderData;
	private $_phgmResultState;
	private $_phgmIsReturned;
	private $_phgmHttpRequest;
	private $_phgmHttpResponse;

	public static $BEFORE_FILTER = 'before';
	private $_beforeFilters;
	public static $AFTER_FILTER = 'after';
	private $_afterFilters;
	public static $AROUND_FILTER = 'around';
	private $_aroundFilters;
	public static $AROUND_FILTER_BEFORE = 0;
	public static $AROUND_FILTER_AFTER = 1;

	public function BaseController($request, $response, $actionName)
	{
		$this->_phgmHttpRequest = $request;
		$this->_phgmHttpResponse = $response;
		$this->_phgmActionName = $actionName;
		$this->_phgmRenderActionName = $actionName;
		$this->_phgmRedirectUrl = null;
		$this->_phgmErrorMessage = null;
		$this->_phgmRenderFormat = null;
		$this->_phgmRenderData = null;
		$this->_phgmResultState = null;
		$this->_phgmDefaultActionName = null;
		$this->_phgmDefaultRenderFormat = HttpResponseFormat::getDefaultFormat();
		$this->_phgmIsReturned = false;

		$this->_beforeFilters = array();
		$this->_afterFilters = array();
		$this->_aroundFilters = array();
	}

	private static function asArray($controller)
	{
		$objectVars = get_object_vars($controller);
		$array = array();
		foreach ($objectVars as $key => $value) {
            if (preg_match('/^_.*/i', $key)) {
            	continue;
            }
            $array[$key] = $value;
		}
		return $array;
	}

	//do not override
	public function execute($params)
	{
		$this->initialize();
		//	アクション名や応答データ刑がナルの場合はデフォルトに設定する
		if (is_null($this->_phgmRenderFormat)) {
			$this->_phgmRenderFormat = $this->_phgmDefaultRenderFormat;
		}
		if (is_null($this->_phgmActionName)) {
			$this->_phgmActionName = $this->_phgmDefaultActionName;
		}

		$this->doFilters(self::$BEFORE_FILTER);
		if ($this->_phgmIsReturned) { return; }

		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_BEFORE);
		if ($this->_phgmIsReturned) { return; }

		call_user_func(array($this, $this->_phgmActionName), $params);
		
		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_AFTER);
		$this->doFilters(self::$AFTER_FILTER);
	}

	private function doFilters($type, $beforeAfter = null)
	{
		if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_beforeFilters as $name) {
				//	アラウンドフィルターの前にこーるする場合、レスポンスが決まったら中止する
				if ($this->_phgmIsReturned) { return; }
				call_user_func(array($this, $name));
			}
		} else if ($type === self::$AFTER_FILTER) {
			foreach ($this->_afterFilters as $name) {
				call_user_func(array($this, $name));
			}
		} else if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_aroundFilters as $name) {
				//	アラウンドフィルターの前にこーるする場合、レスポンスが決まったら中止する
				if ($this->_phgmIsReturned && $beforeAfter === self::$AROUND_FILTER_BEFORE) { return; }
				call_user_func(array($this, $name), $beforeAfter);
			}
		}
	}

	//override to put common initialization code for all actions
	public function initialize()
	{
		
	}

	public function addFilter($type, $name)
	{
		if ($type === self::$BEFORE_FILTER) {
			$this->_beforeFilters[] = $name;
		} else if ($type === self::$AROUND_FILTER) {
			$this->_aroundFilters[] = $name;
		} else if ($type === self::$AFTER_FILTER) {
			$this->_afterFilters[] = $name;
		}
	}

	/*
	 *	getRequest()
	 *		HTTPリクエストを返す
	 */
	public function getRequest()
	{
		return $this->_phgmHttpRequest;
	}

	/*
	 *	getResponse()
	 *		HTTPレスポンスを返す。注意：直接使うとフレームワークとぶつかる可能性が高いので、フレームワークを通す方がお勧めです。
	 *		例えばデータにより画像書き出して返す場合、カスタムなレンダラ(/webapp/app/libs/renderers/)
	 *		とそれに合わせてHttpResponseFormatを設定する。レンダラではコントローラのパラムにより画像を作成する。
	 */
	public function getResponse()
	{
		return $this->_phgmHttpResponse;
	}

	public function getSession()
	{
		return $this->_phgmHttpRequest->getSession();
	}

	/*
	 *	doIsReturned()　－－　応答が決まったってことを設定する
	 *		二度コールするとエラーを発生する
	 */
	private function doIsReturned()
	{
		if ($this->_phgmIsReturned) {
			throw new Exception('BaseController::doIsReturned -- レンダー・エラー・リダイレクトは既にコールされてます。');
		}
		$this->_phgmIsReturned = true;
	}

	/*--------------------------------------------------------------
	 *	リダイレクトの場合
	 */
	public function doRedirectUrl($url) {
		$this->doIsReturned();
		$this->_phgmRedirectUrl = $url;
	}
	public function doRedirect($controller, $actionName, $params = null)
	{
		$this->doIsReturned();
		if (is_null($actionName) || (is_array($actionName) && is_null($params))) {
			//	名前が付いてるルート
			$name = $controller;
			$params = $actionName;
			$this->_phgmRedirectUrl = Router::getRouter()->urlForName($name, $params);
		} else {
			$this->_phgmRedirectUrl = Router::getRouter()->urlForRoute($controller, $actionName, $params);
		}
		if (is_null($this->_phgmRedirectUrl)) {
			throw new Exception('BaseController:doRedirect() -- そのルートはありません。' . $controller . ' ' . $actionName . ' ' . $params);
		}
	}
	public function isRedirect()
	{
		return !is_null($this->_phgmRedirectUrl);
	}
	public function getRedirectUrl()
	{
		return $this->_phgmRedirectUrl;
	}

	/*--------------------------------------------------------------
	 *	普段の場合（データを返す）
	 */
	public function doRender($format = null, $data = null, $actionName = null)
	{
		$this->doIsReturned();
		if (!is_null($format)) {
			$this->_phgmRenderFormat = $format;
		}
		if (!is_null($data)) {
			$this->_phgmRenderData = $data;
		}
		if (!is_null($actionName)) {
			$this->_phgmRenderActionName = $actionName;
		}
	}
	public function getRenderFormat()
	{
		return $this->_phgmRenderFormat;
	}
	public function getRenderData()
	{
		if (!is_null($this->_phgmRenderData)) {
			return $this->_phgmRenderData;
		} else {
			return BaseController::asArray($this);
		}
	}
	public function getRenderAction()
	{
		return $this->_phgmRenderActionName;
	}

	/*--------------------------------------------------------------
	 *	エラーの場合
	 */
	public function doError($message)
	{
		$this->doIsReturned();
		$this->_phgmErrorMessage = $message;
	}
	public function isError()
	{
		return !is_null($this->_phgmErrorMessage);
	}
	public function getErrorMessage()
	{
		return $this->_phgmErrorMessage;
	}
}