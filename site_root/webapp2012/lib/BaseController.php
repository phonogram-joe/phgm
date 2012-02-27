<?php
/*
 *	Copyright (C) Phonogram Inc 2012
 *	Licensed Under the MIT license http://www.opensource.org/licenses/mit-license.php
 */
class BaseController
{
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

	public static $BEFORE_FILTER = 'before';
	private $_beforeFilters;
	public static $AFTER_FILTER = 'after';
	private $_afterFilters;
	public static $AROUND_FILTER = 'around';
	private $_aroundFilters;
	public static $AROUND_FILTER_BEFORE = 0;
	public static $AROUND_FILTER_AFTER = 1;

	public function BaseController($actionName, $format)
	{
		$this->_phgmActionName = $actionName;
		$this->_phgmRenderActionName = $actionName;
		$this->_phgmRedirectUrl = null;
		$this->_phgmErrorMessage = null;
		$this->_phgmRenderFormat = $format;
		$this->_phgmRenderData = null;
		$this->_phgmResultState = null;
		$this->_phgmDefaultActionName = null;
		$this->_phgmDefaultRenderFormat = null;

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

	private function doFilters($type, $beforeAfter = null)
	{
		if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_beforeFilters as $name) {
				call_user_func(array($this, $name));
			}
		} else if ($type === self::$AFTER_FILTER) {
			foreach ($this->_afterFilters as $name) {
				call_user_func(array($this, $name));
			}
		} else if ($type === self::$BEFORE_FILTER) {
			foreach ($this->_aroundFilters as $name) {
				call_user_func(array($this, $name), $beforeAfter);
			}
		}
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
		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_BEFORE);
		call_user_func(array($this, $this->_phgmActionName), $params);
		$this->doFilters(self::$AROUND_FILTER, self::$AROUND_FILTER_AFTER);
		$this->doFilters(self::$AFTER_FILTER);
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

	/*--------------------------------------------------------------
	 *	リダイレクトの場合
	 */
	public function doRedirect($url, $action = null, $params = null)
	{
		$this->_phgmRedirectUrl = $url; //TODO: if action given, the $url is a controller name and we need to determine URL from router
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